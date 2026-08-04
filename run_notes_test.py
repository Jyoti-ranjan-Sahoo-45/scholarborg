import json
import paramiko

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

use App\\Services\\ZohoCrmService;

$zohoService = new ZohoCrmService();
$leadId = '1363767000000539001';

$note1 = "TEST NOTE 1: User searched for 'Advanced Physics Course' on iOS (Device: iPhone) / Safari from Delhi, India.";
$note2 = "TEST NOTE 2: User viewed course '/course/Accessibility-in-UI-UX-Design' on iOS (Device: iPhone) / Safari from Delhi, India.";

echo "Pushing Note 1 to Lead $leadId...\\n";
$res1 = $zohoService->addLeadNote($leadId, $note1);
echo "Result 1: " . ($res1 ? "SUCCESS" : "FAILED") . "\\n";

echo "Pushing Note 2 to Lead $leadId...\\n";
$res2 = $zohoService->addLeadNote($leadId, $note2);
echo "Result 2: " . ($res2 ? "SUCCESS" : "FAILED") . "\\n";
"""

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(hostname=ssh_conf['host'], port=ssh_conf['port'], username=ssh_conf['username'], password=ssh_conf['password'])

    sftp = ssh.open_sftp()
    remote_temp = f"{rp}/test_notes_temp.php"
    
    print(f"Uploading temporary PHP script to {remote_temp}...")
    with sftp.file(remote_temp, "w") as f:
        f.write(php_script)
    sftp.close()

    print("Running notes push script...")
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
