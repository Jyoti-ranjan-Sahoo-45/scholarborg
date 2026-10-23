import paramiko
import json

def main():
    with open('config.json') as f:
        config = json.load(f)

    ssh_conf = config['ssh']
    db_conf = config['lms']['database']

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(hostname=ssh_conf['host'], port=ssh_conf['port'], username=ssh_conf['username'], password=ssh_conf['password'])

    # Get list of all tables
    cmd_tables = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' {db_conf['db_name']} -N -e \"SHOW TABLES\""
    stdin, stdout, stderr = ssh.exec_command(cmd_tables)
    tables = stdout.read().decode().strip().split('\n')
    
    search_queries = ["mathsgenie", "find your course", "genie"]
    
    print(f"Searching {len(tables)} tables for keywords...")
    for table in tables:
        if not table:
            continue
        # Get text/varchar columns for this table
        cmd_cols = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' {db_conf['db_name']} -N -e \"SHOW COLUMNS FROM {table}\""
        stdin, stdout, stderr = ssh.exec_command(cmd_cols)
        cols_info = stdout.read().decode().strip().split('\n')
        text_cols = []
        for col_line in cols_info:
            if not col_line:
                continue
            parts = col_line.split('\t')
            col_name = parts[0]
            col_type = parts[1]
            if any(t in col_type for t in ['varchar', 'text', 'char', 'longtext', 'mediumtext']):
                text_cols.append(col_name)
        
        if not text_cols:
            continue
            
        # For each column, check if contains search_queries
        for col in text_cols:
            for q in search_queries:
                cmd_search = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' {db_conf['db_name']} -N -e \"SELECT COUNT(*) FROM {table} WHERE \\`{col}\\` LIKE '%{q}%'\""
                stdin, stdout, stderr = ssh.exec_command(cmd_search)
                count = stdout.read().decode().strip()
                if count and count != '0':
                    print(f"Match found in Table: {table}, Column: {col} for query '{q}' (Count: {count})")
                    # Let's print a sample
                    cmd_sample = f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' {db_conf['db_name']} -N -e \"SELECT \\`{col}\\` FROM {table} WHERE \\`{col}\\` LIKE '%{q}%' LIMIT 1\""
                    stdin, stdout, _ = ssh.exec_command(cmd_sample)
                    sample = stdout.read().decode().strip()[:200]
                    print(f"  Sample: {sample}")

    ssh.close()

if __name__ == '__main__':
    main()
