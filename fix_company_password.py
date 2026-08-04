import paramiko
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['crm']['database']

# New password to set
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

print(f"Generated hash: {password_hash}")

# 2. Update both superadmin and company passwords in database via stdin to preserve $ characters
sql_update = (
    f"UPDATE {db_conf['db_name']}.users "
    f"SET password = '{password_hash}' "
    f"WHERE email IN ('superadmin@example.com', 'company@example.com');\n"
)

print("Updating database via stdin...")
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' {db_conf['db_name']}")
stdin.write(sql_update)
stdin.close()

err = stderr.read().decode().strip()
if err:
    print("MySQL Error/Warning:", err)
else:
    print("Database updated successfully!")

# 3. Verify the updated password strings in the DB
print("\nVerifying stored password format in DB:")
sql_verify = f"SELECT email, LEFT(password, 10) as hash_prefix FROM {db_conf['db_name']}.users WHERE email IN ('superadmin@example.com', 'company@example.com');"
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_verify}\"")
print(stdout.read().decode())

ssh.close()
print("Done!")
