"""
Run DB schema updates via SSH for the Book a Demo feature.
Adds:
  - meetings.allow_demo       TINYINT(1) DEFAULT 0
  - reserve_meetings.is_demo  TINYINT(1) DEFAULT 0
Then clears Laravel caches on the remote server.
"""
import json, paramiko

with open('config.json', 'r') as f:
    config = json.load(f)

ssh_conf = config['ssh']
lms      = config['lms']
lms_path = lms['remote_path']
db       = lms['database']

db_host = db['db_host']
db_user = db['db_user']
db_pass = db['db_password']
db_name = db['db_name']

commands = [
    # Add allow_demo column to meetings
    f"mysql -h{db_host} -u{db_user} -p'{db_pass}' {db_name} -e "
    f"\"ALTER TABLE meetings ADD COLUMN IF NOT EXISTS allow_demo TINYINT(1) NOT NULL DEFAULT 0;\"",

    # Add is_demo column to reserve_meetings
    f"mysql -h{db_host} -u{db_user} -p'{db_pass}' {db_name} -e "
    f"\"ALTER TABLE reserve_meetings ADD COLUMN IF NOT EXISTS is_demo TINYINT(1) NOT NULL DEFAULT 0;\"",

    # Clear Laravel caches
    f"php {lms_path}/artisan cache:clear",
    f"php {lms_path}/artisan config:clear",
    f"php {lms_path}/artisan view:clear",
]

print(f"Connecting to SSH server {ssh_conf['host']}:{ssh_conf['port']}...")
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(
    hostname=ssh_conf['host'],
    port=ssh_conf['port'],
    username=ssh_conf['username'],
    password=ssh_conf['password'],
    look_for_keys=False,
    allow_agent=False,
    timeout=30
)
print("Connected!\n")

for cmd in commands:
    label = cmd[:80] + '...' if len(cmd) > 80 else cmd
    print(f"Running: {label}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    if out:
        print(f"  OUT: {out}")
    if err and 'Warning' not in err:
        print(f"  ERR: {err}")
    else:
        print(f"  OK")

ssh.close()
print("\nDone! Schema updated and caches cleared.")
