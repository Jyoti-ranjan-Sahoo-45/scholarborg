"""
Get Robert Ransdell's profile URL from user ID 923.
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

cmd = f"php {p}/artisan tinker --execute=\"$u = \\\\App\\\\User::find(923); print('Username: ' . $u->username);\""
_, out, err = ssh.exec_command(cmd)
# Clean up any non-ascii characters before printing
text = out.read().decode('utf-8', errors='ignore')
clean_text = "".join(ch for ch in text if ord(ch) < 128)
print('OUT:', clean_text.strip())

ssh.close()
