import paramiko
import json

with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(hostname=ssh_conf['host'], port=ssh_conf['port'], username=ssh_conf['username'], password=ssh_conf['password'])

# Alter table statement to add microsoft_teams to the enum
sql = f"ALTER TABLE {db_conf['db_name']}.sessions MODIFY COLUMN session_api ENUM('local','big_blue_button','zoom','agora','jitsi','google_meet','microsoft_teams') NOT NULL DEFAULT 'local';"
cmd = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql}\""

stdin, stdout, stderr = ssh.exec_command(cmd)
out = stdout.read().decode()
err = stderr.read().decode()

with open("d:/scholarborg/db_alter_out.txt", "w") as f:
    f.write(f"STDOUT:\n{out}\nSTDERR:\n{err}")

ssh.close()
