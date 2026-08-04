"""
Upload and execute test_blade_render_proper.php on the remote server.
"""
import json, paramiko

with open('config.json') as f:
    c = json.load(f)

s = c['ssh']
rp = c['lms']['remote_path']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'],
            look_for_keys=False, allow_agent=False, timeout=30)

# Upload test script
sftp = ssh.open_sftp()
sftp.put('test_blade_render_proper.php', f"{rp}/test_blade_render_proper.php")
sftp.close()

# Execute test script
print("Running test_blade_render_proper.php on remote server...")
cmd = f"php {rp}/test_blade_render_proper.php"
_, out, err = ssh.exec_command(cmd)
print('OUT:')
print(out.read().decode().strip())
e = err.read().decode().strip()
if e:
    print('ERR:', e)

# Clean up
ssh.exec_command(f"rm {rp}/test_blade_render_proper.php")
ssh.close()
