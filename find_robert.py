"""
Find the user ID and username of Robert Ransdell in the MySQL database.
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

cmd = f"mysql -h{db['db_host']} -u{db['db_user']} -p'{db['db_password']}' {db['db_name']} -e \"SELECT id, username, full_name FROM users WHERE full_name LIKE '%Robert%' OR username LIKE '%robert%';\""
_, out, err = ssh.exec_command(cmd)
print('OUT:')
print(out.read().decode().strip())

ssh.close()
