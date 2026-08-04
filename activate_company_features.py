import paramiko
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['crm']['database']

# New password for company account
new_password = "admin1234"

print("Connecting to SSH server...")
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

# 1. Generate bcrypt hash using PHP on the server
print("Generating bcrypt password hash...")
stdin, stdout, stderr = ssh.exec_command(f"php -r \"echo password_hash('{new_password}', PASSWORD_BCRYPT);\"")
password_hash = stdout.read().decode().strip()

if not password_hash:
    print("Error generating password hash.")
    ssh.close()
    exit(1)

# 2. Update company@example.com details in CRM database
sql_update = (
    f"UPDATE {db_conf['db_name']}.users "
    f"SET password = '{password_hash}', "
    f"    active_plan = 3, "
    f"    plan_expire_date = '2030-01-01', "
    f"    total_user = 1000, "
    f"    storage_limit = 10240 "
    f"WHERE email = 'company@example.com';"
)
cmd_update = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_update}\""

print("Activating all features and setting password for company@example.com...")
stdin, stdout, stderr = ssh.exec_command(cmd_update)
err = stderr.read().decode().strip()

if err:
    print("Error/Warning:", err)
else:
    print("Company features activated and password set successfully!")
    print("\nLogin Details for Company Account:")
    print("URL: https://scholarb.org/crm/login")
    print("Email: company@example.com")
    print("Password: admin1234")

ssh.close()
