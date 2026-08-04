import os
import json
import stat
import argparse
import datetime
import shutil
import paramiko

# Exclude list for deployment
EXCLUDES = [
    'vendor',
    'node_modules',
    'public/store',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    '.git',
    '.env',
    '.revert',
    '__pycache__',
    '.pytest_cache',
    '.DS_Store'
]

def load_config():
    config_path = os.path.join(os.path.dirname(__file__), 'config.json')
    if not os.path.exists(config_path):
        raise FileNotFoundError(f"Configuration file not found at {config_path}")
    with open(config_path, 'r') as f:
        return json.load(f)

def is_excluded(local_path, base_dir):
    rel_path = os.path.relpath(local_path, base_dir).replace('\\', '/')
    for ex in EXCLUDES:
        if rel_path == ex or rel_path.startswith(ex + '/') or ('/' + ex + '/') in rel_path:
            return True
    return False

def sftp_mkdir_p(sftp, remote_directory):
    if remote_directory == '' or remote_directory == '/':
        return
    try:
        sftp.stat(remote_directory)
    except IOError:
        parent = os.path.dirname(remote_directory.rstrip('/'))
        sftp_mkdir_p(sftp, parent)
        try:
            sftp.mkdir(remote_directory)
            print(f"Created remote directory: {remote_directory}")
        except OSError:
            pass

def sync_directory(sftp, local_dir, remote_dir, dry_run=False, checkpoint_dir=None):
    uploaded_files = []
    new_files = []
    skipped_count = 0
    
    print(f"\nScanning directories...")
    print(f"Local: {local_dir}")
    print(f"Remote: {remote_dir}")
    print("-" * 50)
    
    for root, dirs, files in os.walk(local_dir):
        for file in files:
            local_file_path = os.path.join(root, file)
            
            if is_excluded(local_file_path, local_dir):
                continue
                
            rel_path = os.path.relpath(local_file_path, local_dir).replace('\\', '/')
            remote_file_path = remote_dir + '/' + rel_path
            
            local_stat = os.stat(local_file_path)
            local_size = local_stat.st_size
            local_mtime = int(local_stat.st_mtime)
            
            needs_upload = False
            remote_exists = False
            
            try:
                remote_stat = sftp.stat(remote_file_path)
                remote_exists = True
                if local_size != remote_stat.st_size or local_mtime > remote_stat.st_mtime:
                    needs_upload = True
            except IOError:
                needs_upload = True
                
            if needs_upload:
                if dry_run:
                    print(f"[DRY-RUN] Would upload: {rel_path} ({local_size} bytes)")
                else:
                    print(f"Uploading: {rel_path} ({local_size} bytes)")
                    # Ensure parent remote folder exists
                    remote_parent_dir = os.path.dirname(remote_file_path)
                    sftp_mkdir_p(sftp, remote_parent_dir)
                    
                    # Create checkpoint backup if this is a modification
                    if remote_exists and checkpoint_dir:
                        backup_local_path = os.path.join(checkpoint_dir, "backup", rel_path)
                        os.makedirs(os.path.dirname(backup_local_path), exist_ok=True)
                        try:
                            sftp.get(remote_file_path, backup_local_path)
                            uploaded_files.append(rel_path)
                        except Exception as e:
                            print(f"Warning: Failed to back up remote file {rel_path}: {e}")
                    elif not remote_exists:
                        new_files.append(rel_path)
                        
                    # Upload file
                    sftp.put(local_file_path, remote_file_path)
                    sftp.utime(remote_file_path, (local_mtime, local_mtime))
            else:
                skipped_count += 1
                
    print("-" * 50)
    print(f"Sync complete. Uploaded/Modified: {len(uploaded_files) + len(new_files)}, Unchanged: {skipped_count}")
    return uploaded_files, new_files

def run_remote_command(ssh, cmd):
    print(f"Executing: {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    if out:
        print("Out:", out)
    if err:
        print("Err:", err)

def make_checkpoint(target, lms_uploads, lms_news, crm_uploads, crm_news):
    if not (lms_uploads or lms_news or crm_uploads or crm_news):
        return None
        
    timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
    checkpoint_id = f"deploy_{timestamp}"
    checkpoint_dir = os.path.join(os.path.dirname(__file__), ".revert", checkpoint_id)
    
    os.makedirs(checkpoint_dir, exist_ok=True)
    
    manifest = {
        "timestamp": datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
        "target": target,
        "lms": {
            "modified": lms_uploads,
            "new": lms_news
        },
        "crm": {
            "modified": crm_uploads,
            "new": crm_news
        }
    }
    
    with open(os.path.join(checkpoint_dir, "manifest.json"), "w") as f:
        json.dump(manifest, f, indent=2)
        
    print(f"\nCheckpoint '{checkpoint_id}' saved successfully.")
    return checkpoint_dir

def list_checkpoints():
    revert_dir = os.path.join(os.path.dirname(__file__), ".revert")
    if not os.path.exists(revert_dir):
        return []
        
    checkpoints = []
    for item in sorted(os.listdir(revert_dir), reverse=True):
        path = os.path.join(revert_dir, item)
        if os.path.isdir(path) and item.startswith("deploy_"):
            manifest_path = os.path.join(path, "manifest.json")
            if os.path.exists(manifest_path):
                with open(manifest_path, "r") as f:
                    manifest = json.load(f)
                checkpoints.append((item, manifest))
    return checkpoints

def perform_revert(ssh, sftp, checkpoint_id, manifest, config):
    print(f"\n=== Reverting Checkpoint: {checkpoint_id} ({manifest['timestamp']}) ===")
    base_dir = os.path.dirname(os.path.abspath(__file__))
    checkpoint_dir = os.path.join(base_dir, ".revert", checkpoint_id)
    
    # 1. Revert LMS changes
    lms_remote_path = config['lms']['remote_path']
    # Restore modified files
    for rel_path in manifest['lms']['modified']:
        local_backup_path = os.path.join(checkpoint_dir, "backup", rel_path)
        remote_path = lms_remote_path + '/' + rel_path
        if os.path.exists(local_backup_path):
            print(f"Restoring LMS file: {rel_path}")
            sftp.put(local_backup_path, remote_path)
            
    # Delete new files
    for rel_path in manifest['lms']['new']:
        remote_path = lms_remote_path + '/' + rel_path
        try:
            print(f"Deleting new LMS file: {rel_path}")
            sftp.remove(remote_path)
        except IOError:
            pass
            
    # 2. Revert CRM changes
    crm_remote_path = config['crm']['remote_path']
    # Restore modified files
    for rel_path in manifest['crm']['modified']:
        local_backup_path = os.path.join(checkpoint_dir, "backup", rel_path)
        remote_path = crm_remote_path + '/' + rel_path
        if os.path.exists(local_backup_path):
            print(f"Restoring CRM file: {rel_path}")
            sftp.put(local_backup_path, remote_path)
            
    # Delete new files
    for rel_path in manifest['crm']['new']:
        remote_path = crm_remote_path + '/' + rel_path
        try:
            print(f"Deleting new CRM file: {rel_path}")
            sftp.remove(remote_path)
        except IOError:
            pass
            
    # Clear caches
    print("\nClearing server caches...")
    if manifest['lms']['modified'] or manifest['lms']['new']:
        run_remote_command(ssh, f"php {lms_remote_path}/artisan cache:clear")
        run_remote_command(ssh, f"php {lms_remote_path}/artisan config:clear")
        run_remote_command(ssh, f"php {lms_remote_path}/artisan view:clear")
    if manifest['crm']['modified'] or manifest['crm']['new']:
        run_remote_command(ssh, f"php {crm_remote_path}/artisan cache:clear")
        run_remote_command(ssh, f"php {crm_remote_path}/artisan config:clear")
        
    # Clean up checkpoint directory
    shutil.rmtree(checkpoint_dir)
    print(f"\nCheckpoint {checkpoint_id} reverted successfully.")

def handle_revert_flow(config):
    checkpoints = list_checkpoints()
    if not checkpoints:
        print("No deployment checkpoints found to revert.")
        return
        
    print("\nAvailable Deployment Checkpoints (Newest First):")
    for i, (cid, manifest) in enumerate(checkpoints, 1):
        target = manifest['target'].upper()
        time_str = manifest['timestamp']
        
        # Calculate summary
        lms_changes = len(manifest['lms']['modified']) + len(manifest['lms']['new'])
        crm_changes = len(manifest['crm']['modified']) + len(manifest['crm']['new'])
        
        label = "Last Deployment" if i == 1 else f"{i} Deployments Ago"
        print(f" {i}) {label} [{time_str}] Target: {target} (LMS changes: {lms_changes}, CRM changes: {crm_changes})")
        
    print("-" * 60)
    choice = input("Select checkpoint to revert (or 'q' to quit): ").strip()
    if choice.lower() == 'q' or not choice:
        print("Revert cancelled.")
        return
        
    try:
        idx = int(choice) - 1
        if idx < 0 or idx >= len(checkpoints):
            print("Invalid selection.")
            return
    except ValueError:
        print("Invalid selection.")
        return
        
    checkpoint_id, manifest = checkpoints[idx]
    
    ssh_conf = config['ssh']
    print(f"Connecting to SSH server to perform revert...")
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(
        hostname=ssh_conf['host'],
        port=ssh_conf['port'],
        username=ssh_conf['username'],
        password=ssh_conf['password'],
        look_for_keys=False,
        allow_agent=False
    )
    sftp = ssh.open_sftp()
    
    try:
        perform_revert(ssh, sftp, checkpoint_id, manifest, config)
    finally:
        sftp.close()
        ssh.close()

def main():
    parser = argparse.ArgumentParser(description="Deploy local scholarborg changes or revert previous deployments.")
    parser.add_argument('target', choices=['lms', 'crm', 'both', 'revert'], help="The deployment target (lms, crm, both) or 'revert' to restore a checkpoint")
    parser.add_argument('--dry-run', action='store_true', help="Show what changes would be uploaded without making any modifications")
    parser.add_argument('--clear-cache', action='store_true', help="Clear Laravel application cache on the remote server after deployment")
    
    args = parser.parse_args()
    
    try:
        config = load_config()
    except Exception as e:
        print(f"Error loading config: {e}")
        return
        
    if args.target == 'revert':
        handle_revert_flow(config)
        return
        
    ssh_conf = config['ssh']
    
    # Connect SSH
    print(f"Connecting to SSH server {ssh_conf['host']}:{ssh_conf['port']} as {ssh_conf['username']}...")
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try:
        ssh.connect(
            hostname=ssh_conf['host'],
            port=ssh_conf['port'],
            username=ssh_conf['username'],
            password=ssh_conf['password'],
            look_for_keys=False,
            allow_agent=False
        )
    except Exception as e:
        print(f"Connection failed: {e}")
        return
        
    print("Connected successfully!")
    
    try:
        sftp = ssh.open_sftp()
        
        # Prepare checkpoint directory
        checkpoint_dir = None
        if not args.dry_run:
            timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
            checkpoint_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), ".revert", f"deploy_{timestamp}")
            os.makedirs(os.path.join(checkpoint_dir, "backup"), exist_ok=True)
            
        lms_uploads, lms_news = [], []
        crm_uploads, crm_news = [], []
        
        base_dir = os.path.dirname(os.path.abspath(__file__))
        
        # Sync LMS
        if args.target == 'lms' or args.target == 'both':
            print(f"\n================ Deploying LMS ================")
            lms_uploads, lms_news = sync_directory(
                sftp, 
                os.path.join(base_dir, "lms"), 
                config['lms']['remote_path'], 
                dry_run=args.dry_run, 
                checkpoint_dir=checkpoint_dir
            )
            
        # Sync CRM
        if args.target == 'crm' or args.target == 'both':
            print(f"\n================ Deploying CRM ================")
            crm_uploads, crm_news = sync_directory(
                sftp, 
                os.path.join(base_dir, "crm"), 
                config['crm']['remote_path'], 
                dry_run=args.dry_run, 
                checkpoint_dir=checkpoint_dir
            )
            
        # Save or clean checkpoint
        if not args.dry_run:
            if lms_uploads or lms_news or crm_uploads or crm_news:
                # Save manifest
                manifest = {
                    "timestamp": datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                    "target": args.target,
                    "lms": {"modified": lms_uploads, "new": lms_news},
                    "crm": {"modified": crm_uploads, "new": crm_news}
                }
                with open(os.path.join(checkpoint_dir, "manifest.json"), "w") as f:
                    json.dump(manifest, f, indent=2)
                print(f"\nCheckpoint created: {os.path.basename(checkpoint_dir)}")
            else:
                # Delete empty checkpoint directory
                shutil.rmtree(checkpoint_dir)
                
            # Clear caches if requested
            if args.clear_cache and (lms_uploads or lms_news or crm_uploads or crm_news):
                print(f"\nClearing caches...")
                if args.target == 'lms' or args.target == 'both':
                    run_remote_command(ssh, f"php {config['lms']['remote_path']}/artisan cache:clear")
                    run_remote_command(ssh, f"php {config['lms']['remote_path']}/artisan config:clear")
                    run_remote_command(ssh, f"php {config['lms']['remote_path']}/artisan view:clear")
                if args.target == 'crm' or args.target == 'both':
                    run_remote_command(ssh, f"php {config['crm']['remote_path']}/artisan cache:clear")
                    run_remote_command(ssh, f"php {config['crm']['remote_path']}/artisan config:clear")
                    
        sftp.close()
    finally:
        ssh.close()
        print("\nConnection closed.")

if __name__ == '__main__':
    main()
