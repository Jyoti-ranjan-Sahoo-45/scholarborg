import os
import json
import paramiko
import sys

EXCLUDES = [
    'vendor',
    'node_modules',
    '.git',
    '.env',
    '__pycache__',
    '.pytest_cache',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs'
]

def load_config():
    config_path = r"d:\scholarborg\config.json"
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

def sync_directory(sftp, local_dir, remote_dir):
    uploaded_count = 0
    skipped_count = 0
    
    print(f"\nSyncing {local_dir} to {remote_dir}...")
    
    for root, dirs, files in os.walk(local_dir):
        dirs[:] = [d for d in dirs if not is_excluded(os.path.join(root, d), local_dir)]
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
            try:
                remote_stat = sftp.stat(remote_file_path)
                if local_size != remote_stat.st_size or local_mtime > remote_stat.st_mtime:
                    needs_upload = True
            except IOError:
                needs_upload = True
                
            if needs_upload:
                print(f"Uploading: {rel_path}")
                remote_parent_dir = os.path.dirname(remote_file_path)
                sftp_mkdir_p(sftp, remote_parent_dir)
                sftp.put(local_file_path, remote_file_path)
                sftp.utime(remote_file_path, (local_mtime, local_mtime))
                uploaded_count += 1
            else:
                skipped_count += 1
                
    print(f"Sync complete. Uploaded: {uploaded_count}, Unchanged: {skipped_count}")

def main():
    config = load_config()
    ssh_conf = config['ssh']
    
    local_backend_dir = r"d:\scholarborg\scholrbnewproject\backend"
    remote_backend_dir = "/home/u181357068/domains/scholarb.org/marketplace"
    
    print("Connecting to remote server via SSH...")
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(
        hostname=ssh_conf['host'],
        port=ssh_conf['port'],
        username=ssh_conf['username'],
        password=ssh_conf['password']
    )
    
    sftp = ssh.open_sftp()
    
    # 1. Sync files
    sync_directory(sftp, local_backend_dir, remote_backend_dir)
    
    # 2. Always upload/overwrite .env on server
    remote_env_path = remote_backend_dir + '/.env'
    print("Uploading local .env to server...")
    sftp.put(os.path.join(local_backend_dir, '.env'), remote_env_path)
        
    sftp.close()
    
    # 3. Run composer install & migrations on server
    print("\nRunning remote setup commands...")
    commands = [
        f"cd {remote_backend_dir} && composer install --no-dev --optimize-autoloader --no-interaction",
        f"cd {remote_backend_dir} && php artisan migrate --force",
        f"cd {remote_backend_dir} && php artisan config:cache && php artisan route:cache"
    ]
    
    for cmd in commands:
        print(f"Executing: {cmd}")
        stdin, stdout, stderr = ssh.exec_command(cmd)
        out = stdout.read().decode().strip()
        err = stderr.read().decode().strip()
        if out:
            print("OUT:", out)
        if err:
            print("ERR:", err)
            
    ssh.close()
    print("\nDeployment completed successfully!")

if __name__ == '__main__':
    main()
