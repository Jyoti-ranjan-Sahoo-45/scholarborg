"""
Enable allow_demo for Robert Ransdell (instructor ID 923) in database.
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

cmd = f"php {p}/artisan tinker --execute=\"\$m = \App\Models\Meeting::where('creator_id', 923)->first(); \$m->allow_demo = 1; \$m->save(); print('Updated Robert Ransdell allow_demo to 1');\""
_, out, err = ssh.exec_command(cmd)
print('OUT:')
print(out.read().decode().strip())

ssh.close()
