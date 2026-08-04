import paramiko
import json
import sys

with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(hostname=ssh_conf['host'], port=ssh_conf['port'], username=ssh_conf['username'], password=ssh_conf['password'])

sql = sys.argv[1]
cmd = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' {db_conf['db_name']} -e \"{sql}\""

stdin, stdout, stderr = ssh.exec_command(cmd)
print(stdout.read().decode())
err = stderr.read().decode()
if err:
    print("STDERR:", err)
ssh.close()
