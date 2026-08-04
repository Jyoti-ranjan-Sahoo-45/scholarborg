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

# Read the last 120 lines of the Laravel log to find the error message
log_path = f"{crm_conf['remote_path']}/storage/logs/laravel.log"
cmd = f"tail -n 120 {log_path}"
stdin, stdout, stderr = ssh.exec_command(cmd)
log_content = stdout.read().decode()

# Look for error headers
print("=== Latest Laravel Error Details ===")
lines = log_content.split("\n")
# Print lines that look like error start, and print surrounding context
for i, line in enumerate(lines):
    if "local.ERROR" in line or "Exception" in line or "error" in line.lower():
        # Print 5 lines before and 10 lines after the error line
        start = max(0, i - 2)
        end = min(len(lines), i + 8)
        print(f"\n--- Context near line {i} ---")
        for j in range(start, end):
            print(lines[j])

ssh.close()
