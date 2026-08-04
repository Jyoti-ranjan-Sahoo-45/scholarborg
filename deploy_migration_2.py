import json
import paramiko
import os

def main():
    with open('config.json') as f:
        c = json.load(f)

    s = c['ssh']
    lp = 'd:\\scholarborg\\lms'
    rp = c['lms']['remote_path']

    migration_file = 'database/migrations/2026_07_30_000002_create_user_activities_table.php'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    
    print(f"Connecting to {s['host']}:{s['port']}...")
    ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'],
                look_for_keys=False, allow_agent=False, timeout=30)
    print("Connected!")

    sftp = ssh.open_sftp()
    local_path = os.path.join(lp, migration_file.replace('/', os.sep))
    remote_path = f"{rp}/{migration_file}"

    print(f"Uploading migration file: {local_path} -> {remote_path}")
    sftp.put(local_path, remote_path)
    sftp.close()
    print("Upload complete!")

    cmd = f"php {rp}/artisan migrate --force"
    print(f"Running command: {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    
    if out:
        print("OUTPUT:\n", out)
    if err:
        print("ERROR/WARNINGS:\n", err)

    ssh.close()
    print("Migration deployed and executed!")

if __name__ == '__main__':
    main()
