import paramiko
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']
lms_conf = config['lms']

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

# 1. Fetch current settings JSON string
sql_select = (
    f"SELECT st.value "
    f"FROM {db_conf['db_name']}.settings s "
    f"JOIN {db_conf['db_name']}.setting_translations st ON s.id = st.setting_id "
    f"WHERE s.name = 'features' AND st.locale = 'en' LIMIT 1;"
)
cmd_select = f"mysql -N -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_select}\""

print("Fetching current settings from database...")
stdin, stdout, stderr = ssh.exec_command(cmd_select)
settings_json = stdout.read().decode().strip()
err = stderr.read().decode().strip()

if not settings_json:
    print("Error: Could not retrieve settings JSON. Details:", err)
    ssh.close()
    exit(1)

# 2. Parse and modify fallback email
try:
    settings_dict = json.loads(settings_json)
except Exception as parse_err:
    print("Error parsing settings JSON:", parse_err)
    print("Raw output:", settings_json)
    ssh.close()
    exit(1)

old_email = settings_dict.get('microsoft_teams_fallback_email')
settings_dict['microsoft_teams_fallback_email'] = 'admin@scholarb.org'
new_settings_json = json.dumps(settings_dict, ensure_ascii=False)

print(f"Modifying Fallback Email: {old_email} -> admin@scholarb.org")

# 3. Update setting in database
# Escape single quotes in JSON string for mysql query
escaped_json = new_settings_json.replace("'", "\\'")
sql_update = (
    f"UPDATE {db_conf['db_name']}.setting_translations "
    f"SET value = '{escaped_json}' "
    f"WHERE setting_id = 31 AND locale = 'en';"
)
cmd_update = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{sql_update}\""

print("Updating database...")
stdin, stdout, stderr = ssh.exec_command(cmd_update)
err = stderr.read().decode().strip()
if err:
    print("Warning during update:", err)
else:
    print("Database updated successfully!")

# 4. Clear Laravel cache
print("\nClearing Laravel cache on remote server...")
commands = [
    f"php {lms_conf['remote_path']}/artisan cache:clear",
    f"php {lms_conf['remote_path']}/artisan config:clear"
]

for cmd in commands:
    print(f"Executing: {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode().strip()
    err_out = stderr.read().decode().strip()
    if out:
        print(out)
    if err_out:
         print("Error:", err_out)

ssh.close()
print("\nDone! Settings updated and cache cleared successfully.")
