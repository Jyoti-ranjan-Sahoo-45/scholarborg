import paramiko
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['crm']['database']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(
    hostname=ssh_conf['host'],
    port=ssh_conf['port'],
    username=ssh_conf['username'],
    password=ssh_conf['password'],
    look_for_keys=False,
    allow_agent=False
)

# 1. Describe users table schema
print("=== DESCRIBE users ===")
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"DESCRIBE {db_conf['db_name']}.users;\"")
print(stdout.read().decode())
print(stderr.read().decode())

# 2. Select plans
print("=== SELECT * FROM plans ===")
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"SELECT * FROM {db_conf['db_name']}.plans;\"")
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
