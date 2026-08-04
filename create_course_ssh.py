import paramiko
import json
import time

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']

# Generate a unique slug based on timestamp
timestamp = int(time.time())
slug = f"new-test-course-for-instructor-{timestamp}"
title = "New Test Course for Instructor"

sql = f"""
INSERT INTO {db_conf['db_name']}.webinars (
    teacher_id, creator_id, category_id, type, private, slug, status, created_at, updated_at
) VALUES (
    1015, 1015, 528, 'course', 0, '{slug}', 'is_draft', {timestamp}, {timestamp}
);

SET @webinar_id = LAST_INSERT_ID();

INSERT INTO {db_conf['db_name']}.webinar_translations (
    webinar_id, locale, title, seo_description, summary, description
) VALUES (
    @webinar_id, 'en', '{title}', 'Test course', 'Test course', 'Test course description'
);

SELECT @webinar_id AS new_course_id;
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

print("Executing course creation SQL query...")
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}'")
stdin.write(sql)
stdin.close()

out = stdout.read().decode().strip()
err = stderr.read().decode().strip()

print("STDOUT:")
print(out)
if err:
    print("STDERR:")
    print(err)

ssh.close()
print("Done!")
