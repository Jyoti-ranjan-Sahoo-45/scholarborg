import paramiko
import json

with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(hostname=ssh_conf['host'], port=ssh_conf['port'], username=ssh_conf['username'], password=ssh_conf['password'])

stdin, stdout, stderr = ssh.exec_command("tail -n 200 /home/u181357068/domains/scholarb.org/public_html/storage/logs/laravel.log")
with open("d:/scholarborg/remote_logs.txt", "w") as f:
    f.write(stdout.read().decode())

ssh.close()
