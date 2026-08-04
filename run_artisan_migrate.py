import json
import paramiko

def main():
    # Load configuration
    with open('config.json') as f:
        c = json.load(f)

    s = c['ssh']
    lms_path = c['lms']['remote_path']

    # Initialize SSH client
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    
    print(f"Connecting to {s['host']}:{s['port']} as {s['username']}...")
    ssh.connect(
        hostname=s['host'],
        port=s['port'],
        username=s['username'],
        password=s['password'],
        look_for_keys=False,
        allow_agent=False,
        timeout=30
    )
    
    cmd = f"php {lms_path}/artisan migrate --force"
    print(f"Executing remote command: {cmd}")
    
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    
    if out:
        print("OUTPUT:\n", out)
    if err:
        print("ERROR:\n", err)
        
    ssh.close()
    print("Artisan migration execution finished!")

if __name__ == '__main__':
    main()
