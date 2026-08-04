import paramiko
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['crm']['database']

# Password to set
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

# 2. Update the superadmin user's password in the database
sql_update = (
    f"UPDATE {db_conf['db_name']}.users "
    f"SET password = '{password_hash}' "
    f"WHERE email = 'superadmin@example.com';"
)
cmd_update = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_update}\""

print(f"Updating password for superadmin@example.com in database...")
stdin, stdout, stderr = ssh.exec_command(cmd_update)
err = stderr.read().decode().strip()

if err:
    print("Warning/Error during database update:", err)
else:
    print("CRM Database updated successfully!")
    print(f"\nLogin details for CRM:")
    print(f"URL: https://scholarb.org/crm")
    print(f"Email: superadmin@example.com")
    print(f"Password: {new_password}")

ssh.close()
