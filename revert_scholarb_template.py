import json
import paramiko
import os

def main():
    with open('config.json') as f:
        c = json.load(f)

    s = c['ssh']
    lp = 'd:\\scholarborg\\lms'
    rp = c['lms']['remote_path']

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    
    print(f"Connecting to {s['domain']}:{s['port']}...")
    ssh.connect(s['domain'], port=s['port'], username=s['username'], password=s['password'],
                look_for_keys=False, allow_agent=False, timeout=30)
    print("Connected!")

    sftp = ssh.open_sftp()

    # 1. Restore resources/views/design_1/web/home/index.blade.php
    rel_index = 'resources/views/design_1/web/home/index.blade.php'
    local_index = os.path.join(lp, rel_index.replace('/', os.sep))
    remote_index = f"{rp}/{rel_index}"
    print(f"Restoring remote index: {remote_index} from local: {local_index}")
    sftp.put(local_index, remote_index)

    # 2. Delete scholarb_home.blade.php
    rel_home = 'resources/views/design_1/web/home/scholarb_home.blade.php'
    remote_home = f"{rp}/{rel_home}"
    print(f"Deleting remote home template: {remote_home}")
    try:
        sftp.remove(remote_home)
        print("  Deleted successfully!")
    except IOError as e:
        print(f"  Warning: could not delete {remote_home} (may not exist): {e}")

    # 3. Delete scholarb.css
    rel_css = 'public/assets/default/css/scholarb.css'
    remote_css = f"{rp}/{rel_css}"
    print(f"Deleting remote stylesheet: {remote_css}")
    try:
        sftp.remove(remote_css)
        print("  Deleted successfully!")
    except IOError as e:
        print(f"  Warning: could not delete {remote_css} (may not exist): {e}")

    sftp.close()

    # 4. Clear view, config, route, and application cache
    print("Clearing Laravel caches...")
    commands = [
        f"php {rp}/artisan cache:clear",
        f"php {rp}/artisan config:clear",
        f"php {rp}/artisan route:clear",
        f"php {rp}/artisan view:clear"
    ]
    for cmd in commands:
        print(f"Executing: {cmd}")
        _, out, err = ssh.exec_command(cmd)
        o = out.read().decode().strip()
        e = err.read().decode().strip()
        if o:
            print("  OUT:", o)
        if e:
            print("  ERR:", e)

    ssh.close()
    print("Reversion finished successfully!")

if __name__ == '__main__':
    main()
