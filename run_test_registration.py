import json
import paramiko
import time

def main():
    with open('config.json') as f:
        config = json.load(f)

    ssh_conf = config['ssh']
    rp = config['lms']['remote_path']

    php_script = f"""<?php
require '{rp}/vendor/autoload.php';
$app = require_once '{rp}/bootstrap/app.php';
$kernel = $app->make(Illuminate\\Contracts\\Http\\Kernel::class);
$kernel->handle(Illuminate\\Http\\Request::capture());

use App\\User;
use App\\Models\\Role;
use Illuminate\\Support\Facades\\Hash;
use Illuminate\\Support\Facades\\DB;
use Illuminate\\Auth\\Events\\Registered;

$email = 'zoho.test.student.' . time() . '@gmail.com';
$name = 'Zoho Test Student';

echo "Initializing guest activities session...\\n";
$sessionId = 'test_session_' . rand(1000, 9999);
session()->setId($sessionId);

// Insert guest activities
DB::table('user_activities')->insert([
    [
        'user_id' => null,
        'session_id' => $sessionId,
        'ip_address' => '103.87.143.2',
        'location' => 'Delhi, Delhi, India',
        'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
        'device_type' => 'Mobile',
        'device_name' => 'iOS (Device: iPhone) / Safari',
        'action_type' => 'search',
        'query' => 'Advanced Physics Course',
        'synced_to_zoho' => false,
        'created_at' => time() - 100
    ],
    [
        'user_id' => null,
        'session_id' => $sessionId,
        'ip_address' => '103.87.143.2',
        'location' => 'Delhi, Delhi, India',
        'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
        'device_type' => 'Mobile',
        'device_name' => 'iOS (Device: iPhone) / Safari',
        'action_type' => 'page_view',
        'query' => '/course/Accessibility-in-UI-UX-Design',
        'synced_to_zoho' => false,
        'created_at' => time() - 50
    ]
]);

echo "Registering user: $name ($email)...\\n";

$user = User::create([
    'role_name' => Role::$user,
    'role_id' => Role::getUserRoleId(),
    'email' => $email,
    'mobile' => '+919988776655',
    'full_name' => $name,
    'status' => User::$active,
    'access_content' => true,
    'password' => Hash::make('password123'),
    'created_at' => time()
]);

// Dispatch Registered event (this triggers Zoho Crm sync)
event(new Registered($user));

// Reload user model to see updated zoho_lead_id
$user->refresh();

echo "SUCCESS! Zoho Lead ID: " . $user->zoho_lead_id . "\\n";
"""

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(hostname=ssh_conf['host'], port=ssh_conf['port'], username=ssh_conf['username'], password=ssh_conf['password'])

    sftp = ssh.open_sftp()
    remote_temp = f"{rp}/test_reg_temp.php"
    
    print(f"Uploading temporary PHP script to {remote_temp}...")
    with sftp.file(remote_temp, "w") as f:
        f.write(php_script)
    sftp.close()

    print("Running registration script...")
    stdin, stdout, stderr = ssh.exec_command(f"php {remote_temp}")
    out = stdout.read().decode()
    err = stderr.read().decode()

    print("OUTPUT:\n", out)
    if err:
        print("STDERR:\n", err)

    # Clean up
    print("Cleaning up temporary file...")
    ssh.exec_command(f"rm {remote_temp}")
    ssh.close()
    
    print("Done!")

if __name__ == '__main__':
    main()
