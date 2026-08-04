import paramiko
import json

with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(hostname=ssh_conf['host'], port=ssh_conf['port'], username=ssh_conf['username'], password=ssh_conf['password'])

sql = f"SHOW COLUMNS FROM {db_conf['db_name']}.sessions LIKE 'session_api';"
cmd = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql}\""

stdin, stdout, stderr = ssh.exec_command(cmd)
with open("d:/scholarborg/db_out.txt", "w") as f:
    f.write(stdout.read().decode())

ssh.close()
