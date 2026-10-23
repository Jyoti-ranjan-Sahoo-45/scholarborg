import paramiko, json

with open('config.json') as f:
    c = json.load(f)
s = c['ssh']
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'])

cmd = 'sed -n "108,120p" /home/u181357068/domains/scholarb.org/public_html/routes/web.php'
stdin, stdout, stderr = ssh.exec_command(cmd)
print("Lines 108-120:")
print(stdout.read().decode())

ssh.close()
