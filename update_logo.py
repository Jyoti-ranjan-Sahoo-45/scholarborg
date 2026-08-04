import json
import paramiko
import os

def main():
    config_path = 'd:/scholarborg/config.json'
    with open(config_path, 'r') as f:
        config = json.load(f)
        
    ssh_conf = config['ssh']
    db_conf = config['lms']['database']
    remote_path = config['lms']['remote_path']
    
    print("Connecting to SSH...")
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
    
    # Step 1: Fetch the current general settings json
    print("Fetching current general settings from database...")
    select_sql = (
        f"SELECT st.value FROM {db_conf['db_name']}.setting_translations st "
        f"JOIN {db_conf['db_name']}.settings s ON s.id = st.setting_id "
        f"WHERE s.name = 'general' AND st.locale = 'en';"
    )
    
    cmd = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -N -e \"{select_sql}\""
    stdin, stdout, stderr = ssh.exec_command(cmd)
    current_val_str = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    
    if err:
        print("Error fetching settings:", err)
        ssh.close()
        return
        
    if not current_val_str:
        print("No general settings found in the database!")
        ssh.close()
        return
        
    print("\nCurrent settings in database:")
    print(current_val_str)
    
    try:
        settings_dict = json.loads(current_val_str)
    except Exception as e:
        print("Failed to parse settings JSON:", e)
        ssh.close()
        return
        
    # Step 2: Modify the logo and favicon paths
    settings_dict['logo'] = '/assets/default/img/scholarblogo.png'
    settings_dict['dark_mode_logo'] = '/assets/default/img/scholarblogo.png'
    settings_dict['fav_icon'] = '/assets/default/img/favicon.png'
    
    updated_val_str = json.dumps(settings_dict)
    print("\nUpdated settings to be saved:")
    print(updated_val_str)
    
    # Escape quotes and backslashes for mysql query argument
    escaped_val_str = updated_val_str.replace('\\', '\\\\').replace('"', '\\"')
    
    # Step 3: Update the database
    print("\nUpdating database...")
    update_sql = (
        f"UPDATE {db_conf['db_name']}.setting_translations st "
        f"JOIN {db_conf['db_name']}.settings s ON s.id = st.setting_id "
        f"SET st.value = '{escaped_val_str}' "
        f"WHERE s.name = 'general' AND st.locale = 'en';"
    )
    
    update_cmd = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' -e \"{update_sql}\""
    stdin, stdout, stderr = ssh.exec_command(update_cmd)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    
    if err:
        print("Error updating settings database:", err)
    else:
        print("Database updated successfully!")
        
    # Step 4: Clear Laravel caches
    print("\nClearing Laravel cache on server...")
    for artisan_cmd in ['cache:clear', 'view:clear', 'config:clear']:
        cmd_to_run = f"php {remote_path}/artisan {artisan_cmd}"
        _, a_out, a_err = ssh.exec_command(cmd_to_run)
        print(f"artisan {artisan_cmd} :", a_out.read().decode().strip() or a_err.read().decode().strip())
        
    ssh.close()
    print("\nDone!")

if __name__ == '__main__':
    main()
