import paramiko
import json

with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']

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

# Query the settings and their translations
sql = (
    f"SELECT s.id, s.name, st.locale, st.value "
    f"FROM {db_conf['db_name']}.settings s "
    f"JOIN {db_conf['db_name']}.setting_translations st ON s.id = st.setting_id "
    f"WHERE s.name = 'features';"
)
cmd = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql}\""

stdin, stdout, stderr = ssh.exec_command(cmd)
out = stdout.read().decode()
err = stderr.read().decode()

print("STDOUT:")
print(out)
if err:
    print("STDERR:", err)

ssh.close()
