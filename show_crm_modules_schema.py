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

# 1. Describe user_active_modules
print("=== DESCRIBE user_active_modules ===")
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"DESCRIBE {db_conf['db_name']}.user_active_modules;\"")
print(stdout.read().decode())

# 2. Select from add_ons
print("=== SELECT * FROM add_ons ===")
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"SELECT * FROM {db_conf['db_name']}.add_ons;\"")
print(stdout.read().decode())

ssh.close()
