import paramiko, json

with open('config.json') as f:
    c = json.load(f)
s = c['ssh']
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'])

cmd = 'grep -n "Route::get(" /home/u181357068/domains/scholarb.org/public_html/routes/web.php | head -n 20'
stdin, stdout, stderr = ssh.exec_command(cmd)
print("Routes in web.php:")
print(stdout.read().decode())

ssh.close()
