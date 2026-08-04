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

# Check tables in database
print("Listing tables in CRM database...")
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"SHOW TABLES FROM {db_conf['db_name']};\"")
tables = stdout.read().decode()
print(tables)

# Query plans table if it exists, and check columns of user with email company@example.com
print("\nQuerying user company@example.com details...")
sql_user = f"SELECT id, name, email, plan, plan_expire_date FROM {db_conf['db_name']}.users WHERE email = 'company@example.com';"
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_user}\"")
print(stdout.read().decode())

ssh.close()
