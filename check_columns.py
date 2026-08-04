import json, paramiko

with open('config.json') as f:
    c = json.load(f)

s = c['ssh']
db = c['lms']['database']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'],
            look_for_keys=False, allow_agent=False, timeout=30)

cmd = (
    f"mysql -h{db['db_host']} -u{db['db_user']} -p'{db['db_password']}' {db['db_name']} -e "
    "\"SHOW COLUMNS FROM meetings LIKE 'allow_demo'; SHOW COLUMNS FROM reserve_meetings LIKE 'is_demo';\""
)

_, out, err = ssh.exec_command(cmd)
print('OUT:', out.read().decode().strip())
e = err.read().decode().strip()
if e and 'Warning' not in e:
    print('ERR:', e)

ssh.close()
print('Done')
