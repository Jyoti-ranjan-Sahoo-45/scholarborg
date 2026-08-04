"""
Check remote basic_settings.blade.php on server to confirm our changes are there.
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

file_path = f"{p}/resources/views/design_1/panel/meeting/settings/tabs/basic_settings.blade.php"
cmd = f"grep -n 'allow_demo\\|Demo Booking\\|allowDemoSwitch' {file_path}"

_, out, err = ssh.exec_command(cmd)
print('GREP result:')
print(out.read().decode().strip())
e = err.read().decode().strip()
if e:
    print('ERR:', e)

ssh.close()
