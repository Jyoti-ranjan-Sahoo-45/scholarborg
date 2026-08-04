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

# 1. Select company details
print("=== company@example.com user record ===")
sql_user = f"SELECT id, name, email, active_plan, plan_expire_date, total_user, storage_limit FROM {db_conf['db_name']}.users WHERE email = 'company@example.com';"
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_user}\"")
print(stdout.read().decode())

# 2. Select first 10 records from user_active_modules
print("=== user_active_modules samples ===")
sql_modules = f"SELECT * FROM {db_conf['db_name']}.user_active_modules LIMIT 15;"
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_modules}\"")
print(stdout.read().decode())

ssh.close()
