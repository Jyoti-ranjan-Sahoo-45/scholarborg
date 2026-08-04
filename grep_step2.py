"""
Grep step_2.blade.php on the remote server to see the is_demo input code.
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

cmd = f"grep -n 'is_demo' {p}/resources/views/design_1/web/users/meeting/steps/step_2.blade.php"
_, out, err = ssh.exec_command(cmd)
print('OUT:')
print(out.read().decode().strip())
e = err.read().decode().strip()
if e:
    print('ERR:', e)

ssh.close()
