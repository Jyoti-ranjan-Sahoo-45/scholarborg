"""
SSH Verification Script
Checks: SSH connection, DB columns, remote file content, and site status
"""
import json, paramiko, requests

with open('config.json') as f:
    c = json.load(f)

s  = c['ssh']
db = c['lms']['database']
rp = c['lms']['remote_path']

print("=" * 60)
print("SSH & SERVER VERIFICATION")
print("=" * 60)

# 1. SSH Connection
print("\n[1] SSH Connection...")
try:
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(s['host'], port=s['port'], username=s['username'],
                password=s['password'], look_for_keys=False,
                allow_agent=False, timeout=30)
    print(f"    OK  Connected to {s['host']}:{s['port']}")
except Exception as e:
    print(f"    FAIL  {e}")
    exit(1)

# 2. DB Column Check
print("\n[2] Database Columns...")
for table, col in [('meetings', 'allow_demo'), ('reserve_meetings', 'is_demo')]:
    cmd = (f"mysql -h{db['db_host']} -u{db['db_user']} -p'{db['db_password']}' "
           f"{db['db_name']} -e \"SHOW COLUMNS FROM {table} LIKE '{col}';\" --skip-column-names 2>/dev/null")
    _, out, _ = ssh.exec_command(cmd)
    result = out.read().decode().strip()
    if result:
        print(f"    OK  {table}.{col} EXISTS  ({result.split()[1]})")
    else:
        print(f"    MISS  {table}.{col} is MISSING!")

# 3. Remote File Check
print("\n[3] Remote Blade File: basic_settings.blade.php...")
blade = f"{rp}/resources/views/design_1/panel/meeting/settings/tabs/basic_settings.blade.php"
checks = ['allow_demo', 'Enable Demo Booking', 'allowDemoSwitch']
for token in checks:
    _, out, _ = ssh.exec_command(f"grep -c '{token}' {blade}")
    count = out.read().decode().strip()
    status = "OK" if int(count or 0) > 0 else "MISSING"
    print(f"    {status}  '{token}' — count: {count}")

# 4. Panel MeetingController Check
print("\n[4] Remote MeetingController (Panel)...")
ctrl = f"{rp}/app/Http/Controllers/Panel/MeetingController.php"
for token in ['allow_demo']:
    _, out, _ = ssh.exec_command(f"grep -c '{token}' {ctrl}")
    count = out.read().decode().strip()
    status = "OK" if int(count or 0) > 0 else "MISSING"
    print(f"    {status}  '{token}' — count: {count}")

# 5. Web MeetingController Check
print("\n[5] Remote MeetingController (Web)...")
wctrl = f"{rp}/app/Http/Controllers/Web/MeetingController.php"
for token in ['is_demo', 'demo_booking_max_limit']:
    _, out, _ = ssh.exec_command(f"grep -c '{token}' {wctrl}")
    count = out.read().decode().strip()
    status = "OK" if int(count or 0) > 0 else "MISSING"
    print(f"    {status}  '{token}' — count: {count}")

# 6. PHP Syntax Check
print("\n[6] PHP Syntax Check (Panel MeetingController)...")
_, out, err = ssh.exec_command(f"php -l {ctrl}")
result = (out.read().decode() + err.read().decode()).strip()
if 'No syntax errors' in result:
    print(f"    OK  {result}")
else:
    print(f"    WARN  {result}")

ssh.close()

# 7. Site HTTP Check
print("\n[7] Site HTTP Status...")
for url in ['https://scholarb.org', 'https://scholarb.org/crm']:
    try:
        r = requests.get(url, timeout=10)
        print(f"    {r.status_code}  {url}")
    except Exception as e:
        print(f"    FAIL  {url} — {e}")

print("\n" + "=" * 60)
print("VERIFICATION COMPLETE")
print("=" * 60)
