import paramiko
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
lms_conf = config['lms']

# PHP code to trigger the real Teams meeting creation in Laravel
php_code = """<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $session = \\App\\Models\\Session::find(239);
    if (!$session) {
        echo "Session 239 not found!\\n";
        exit(1);
    }
    
    // Change session API to microsoft_teams to trigger Teams class
    $session->update(['session_api' => 'microsoft_teams']);
    
    $teams = new \\App\\Sessions\\MicrosoftTeams();
    $res = $teams->makeMeeting($session);
    if ($res) {
        $session->refresh();
        echo "SUCCESS: " . $session->link . "\\n";
    } else {
        echo "FAILED to create meeting\\n";
    }
} catch (\\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\\n";
}
"""

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
remote_file_path = f"{lms_conf['remote_path']}/trigger_teams_temp.php"
print(f"Creating temp file on server: {remote_file_path}...")
with sftp.file(remote_file_path, 'w') as f:
    f.write(php_code)
sftp.close()

print("Executing temp script on server to generate Teams link...")
stdin, stdout, stderr = ssh.exec_command(f"php {remote_file_path}")
out = stdout.read().decode().strip()
err = stderr.read().decode().strip()

print("STDOUT:")
print(out)
if err:
    print("STDERR:")
    print(err)

# Clean up temp file
print("Cleaning up temp file on server...")
ssh.exec_command(f"rm {remote_file_path}")

ssh.close()
print("Done!")
