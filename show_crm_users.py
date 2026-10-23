import paramiko
import json

# Load configuration
with open('config.json') as f:
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

# Query the users table in CRM database
sql = f"SELECT id, name, email, type FROM {db_conf['db_name']}.users;"
cmd = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql}\""

stdin, stdout, stderr = ssh.exec_command(cmd)
out = stdout.read().decode()
err = stderr.read().decode()

print("STDOUT:")
print(out)
if err:
    print("STDERR:", err)

ssh.close()
