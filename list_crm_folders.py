import paramiko
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
crm_conf = config['crm']

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

# 1. Get the last 15 lines of laravel.log
print("=== Latest CRM Log Entries ===")
log_path = f"{crm_conf['remote_path']}/storage/logs/laravel.log"
stdin, stdout, stderr = ssh.exec_command(f"tail -n 30 {log_path}")
print(stdout.read().decode())
print(stderr.read().decode())

# 2. List physical modules folder on the server
print("=== Remote Modules Folder ===")
modules_path = f"{crm_conf['remote_path']}/Modules"
stdin, stdout, stderr = ssh.exec_command(f"ls -l {modules_path}")
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
