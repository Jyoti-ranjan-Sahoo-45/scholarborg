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

# 1. Clear any existing active modules for company (id=2) to avoid duplicates
sql_clear = f"DELETE FROM {db_conf['db_name']}.user_active_modules WHERE user_id = 2;"
print("Clearing any existing active modules for user ID 2...")
ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_clear}\"")

# 2. Insert all enabled modules from add_ons for user ID 2
sql_insert = (
    f"INSERT INTO {db_conf['db_name']}.user_active_modules (user_id, module, created_at, updated_at) "
    f"SELECT 2, module, NOW(), NOW() "
    f"FROM {db_conf['db_name']}.add_ons "
    f"WHERE is_enable = 1 AND for_admin = 0;"
)

print("Inserting all enabled modules for user ID 2...")
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_insert}\"")
err = stderr.read().decode().strip()

if err:
    print("Error/Warning during insert:", err)
else:
    print("Modules inserted successfully!")

# 3. Verify the modules are active
sql_verify = f"SELECT count(*) as count FROM {db_conf['db_name']}.user_active_modules WHERE user_id = 2;"
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_verify}\"")
print("Active modules count for user ID 2:", stdout.read().decode().strip())

# 4. Clear CRM cache
print("\nClearing CRM application cache...")
crm_path = config['crm']['remote_path']
commands = [
    f"php {crm_path}/artisan cache:clear",
    f"php {crm_path}/artisan config:clear"
]
for cmd in commands:
    print(f"Executing: {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode().strip()
    err_out = stderr.read().decode().strip()
    if out:
        print(out)
    if err_out:
         print("Error:", err_out)

ssh.close()
print("\nDone!")
