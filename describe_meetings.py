"""
Describe the meetings table structure directly from MySQL database.
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

cmd = f"mysql -h{db['db_host']} -u{db['db_user']} -p'{db['db_password']}' {db['db_name']} -e \"DESCRIBE meetings;\""
_, out, err = ssh.exec_command(cmd)
print('OUT:')
print(out.read().decode().strip())
e = err.read().decode().strip()
if e and 'Warning' not in e:
    print('ERR:', e)

ssh.close()
