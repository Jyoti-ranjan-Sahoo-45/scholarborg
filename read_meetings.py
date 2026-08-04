"""
Query the database for the active instructor's meeting setting to see the value of allow_demo.
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

# Run tinker command to query the meetings table
cmd = f"php {p}/artisan tinker --execute=\"print_r(\App\Models\Meeting::all()->toArray());\""
_, out, err = ssh.exec_command(cmd)
print('OUT:')
print(out.read().decode().strip())
e = err.read().decode().strip()
if e:
    print('ERR:', e)

ssh.close()
