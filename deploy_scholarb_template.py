import json
import paramiko
import os

def main():
    with open('config.json') as f:
        c = json.load(f)

    s = c['ssh']
    lp = 'd:\\scholarborg\\lms'
    rp = c['lms']['remote_path']

    files = [
        'resources/views/design_1/web/home/index.blade.php',
        'resources/views/design_1/web/home/scholarb_home.blade.php',
        'public/assets/default/css/scholarb.css'
    ]

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    
    print(f"Connecting to {s['domain']}:{s['port']}...")
    ssh.connect(s['domain'], port=s['port'], username=s['username'], password=s['password'],
                look_for_keys=False, allow_agent=False, timeout=30)
    print("Connected!")

    sftp = ssh.open_sftp()
    
    for rel in files:
        local_path = os.path.join(lp, rel.replace('/', os.sep))
        remote_path = f"{rp}/{rel}"
        
        # Ensure parent remote folder exists
        remote_dir = os.path.dirname(remote_path)
        try:
            sftp.stat(remote_dir)
        except IOError:
            # Simple recursive mkdir
            parts = remote_dir.split('/')
            current = ""
            for p in parts:
                if p:
                    current += "/" + p
                    try:
                        sftp.stat(current)
                    except IOError:
                        sftp.mkdir(current)
        
        print(f"Uploading: {rel} -> {remote_path}")
        sftp.put(local_path, remote_path)
        
    sftp.close()
    print("All files uploaded successfully!")

    # Clear view and application cache
    print("Clearing Laravel caches...")
    commands = [
        f"php {rp}/artisan cache:clear",
        f"php {rp}/artisan config:clear",
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
    print("Deployment finished!")

if __name__ == '__main__':
    main()
