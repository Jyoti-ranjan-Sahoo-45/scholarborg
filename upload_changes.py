import os
import json
import paramiko

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
lms_conf = config['lms']

# Files to upload: (local_path, remote_path)
files_to_upload = [
    (
        'lms/app/Sessions/MicrosoftTeams.php',
        lms_conf['remote_path'] + '/app/Sessions/MicrosoftTeams.php'
    ),
    (
        'lms/app/Http/Controllers/Panel/SessionController.php',
        lms_conf['remote_path'] + '/app/Http/Controllers/Panel/SessionController.php'
    ),
    (
        'lms/app/Http/Controllers/Admin/SessionController.php',
        lms_conf['remote_path'] + '/app/Http/Controllers/Admin/SessionController.php'
    ),
    (
        'lms/public/test_teams_v2.php',
        lms_conf['remote_path'] + '/test_teams_v2.php'
    ),
    (
        'lms/public/test_teams_v2.php',
        lms_conf['remote_path'] + '/public/test_teams_v2.php'
    ),
    (
        'lms/public/test_teams_15.php',
        lms_conf['remote_path'] + '/test_teams_15.php'
    ),
    (
        'lms/public/test_teams_15.php',
        lms_conf['remote_path'] + '/public/test_teams_15.php'
    )
]

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

sftp = ssh.open_sftp()
print("Connected successfully!\n")

for local, remote in files_to_upload:
    local_full = os.path.join('d:/scholarborg', local)
    if os.path.exists(local_full):
        print(f"Uploading {local} -> {remote}...")
        try:
            sftp.put(local_full, remote)
        except Exception as upload_err:
            print(f"Warning: Failed to upload {local} to {remote}: {upload_err}")
    else:
        print(f"Error: Local file not found: {local_full}")

print("\nClearing Laravel cache on remote server...")
commands = [
    f"php {lms_conf['remote_path']}/artisan cache:clear",
    f"php {lms_conf['remote_path']}/artisan config:clear",
    f"php {lms_conf['remote_path']}/artisan view:clear"
]

for cmd in commands:
    print(f"Executing: {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    if out:
        print(out)
    if err:
        print("Error:", err)

sftp.close()
ssh.close()
print("\nDone! All changes deployed successfully.")
