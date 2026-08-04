"""
Query the exact username and full name of user ID 923 (Robert Ransdell) from MySQL database.
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

cmd = f"mysql -h{db['db_host']} -u{db['db_user']} -p'{db['db_password']}' {db['db_name']} -e \"SELECT id, username, full_name FROM users WHERE id = 923;\""
_, out, err = ssh.exec_command(cmd)
print('OUT:')
print(out.read().decode().strip())

ssh.close()
