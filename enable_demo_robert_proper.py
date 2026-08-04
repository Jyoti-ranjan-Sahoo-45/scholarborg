"""
Enable allow_demo for the correct Robert Ransdell (instructor ID 1015) in database.
"""
import json, paramiko

with open('config.json') as f:
    c = json.load(f)

s = c['ssh']
db = c['lms']['database']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'],
            look_for_keys=False, allow_agent=False, timeout=30)

cmd = f"mysql -h{db['db_host']} -u{db['db_user']} -p'{db['db_password']}' {db['db_name']} -e \"UPDATE meetings SET allow_demo = 1 WHERE creator_id = 1015;\""
_, out, err = ssh.exec_command(cmd)
print('OUT:')
print(out.read().decode().strip())

ssh.close()
print("Updated allow_demo to 1 for Robert Ransdell (ID 1015)!")
