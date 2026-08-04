"""
Clear Laravel caches via SSH.
"""
import json, paramiko

with open('config.json') as f:
    c = json.load(f)

s = c['ssh']
p = c['lms']['remote_path']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'],
            look_for_keys=False, allow_agent=False, timeout=30)

for cmd in [f'php {p}/artisan cache:clear', f'php {p}/artisan view:clear', f'php {p}/artisan config:clear']:
    _, out, err = ssh.exec_command(cmd)
    print(cmd.split('artisan ')[1], ':', out.read().decode().strip() or err.read().decode().strip())

ssh.close()
