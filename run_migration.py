"""
Execute the MySQL ALTER TABLE statements directly on the production database.
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

queries = [
    "ALTER TABLE meetings ADD COLUMN allow_demo TINYINT(1) NOT NULL DEFAULT 0;",
    "ALTER TABLE reserve_meetings ADD COLUMN is_demo TINYINT(1) NOT NULL DEFAULT 0;"
]

for q in queries:
    cmd = f"mysql -h{db['db_host']} -u{db['db_user']} -p'{db['db_password']}' {db['db_name']} -e \"{q}\""
    print(f"Running: {q}")
    _, out, err = ssh.exec_command(cmd)
    o = out.read().decode().strip()
    e = err.read().decode().strip()
    if o:
        print("  OUT:", o)
    if e:
        print("  ERR:", e)
    else:
        print("  Success!")

ssh.close()
print("Migration run finished!")
