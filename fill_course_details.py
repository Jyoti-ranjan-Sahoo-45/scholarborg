import paramiko
import json
import time

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']

timestamp = int(time.time())
start_date = timestamp + 3600  # starts in 1 hour
session_date = timestamp + 7200  # meeting in 2 hours

# We will connect and run the queries sequentially or write a script to insert them and fetch IDs
# Let's construct a python script to run on the remote machine or execute multiple statements via SSH.
# Using python directly on the remote server is very powerful because we can retrieve last_insert_id easily.
# Let's check if python is available on the remote server by trying to write the python script to the remote server or running it via ssh.
# Actually, we can run MySQL statements using SET variables in a single multi-statement query block!
sql = f"""
-- 1. Update webinar details to make it active and priced
UPDATE {db_conf['db_name']}.webinars
SET status = 'active',
    price = 15.00,
    duration = 120,
    capacity = 50,
    support = 1,
    certificate = 1,
    forum = 1,
    start_date = {start_date},
    timezone = 'UTC',
    updated_at = {timestamp}
WHERE id = 2065;

-- 2. Create the webinar chapter
INSERT INTO {db_conf['db_name']}.webinar_chapters (
    user_id, webinar_id, `order`, check_all_contents_pass, status, created_at
) VALUES (
    1015, 2065, 1, 0, 'active', {timestamp}
);

SET @chapter_id = LAST_INSERT_ID();

INSERT INTO {db_conf['db_name']}.webinar_chapter_translations (
    webinar_chapter_id, locale, title
) VALUES (
    @chapter_id, 'en', 'Chapter 1: Getting Started'
);

-- 3. Create a Live Session (Meeting)
INSERT INTO {db_conf['db_name']}.sessions (
    creator_id, webinar_id, chapter_id, date, duration, link, extra_time_to_join, session_api, status, created_at, updated_at
) VALUES (
    1015, 2065, @chapter_id, {session_date}, 60, 'https://teams.microsoft.com/l/meetup-join/dummy', 10, 'local', 'active', {timestamp}, {timestamp}
);

SET @session_id = LAST_INSERT_ID();

INSERT INTO {db_conf['db_name']}.session_translations (
    session_id, locale, title, description
) VALUES (
    @session_id, 'en', 'Live Q&A Session', 'Ask anything about the course and interact live!'
);

INSERT INTO {db_conf['db_name']}.webinar_chapter_items (
    user_id, chapter_id, item_id, type, `order`, created_at
) VALUES (
    1015, @chapter_id, @session_id, 'session', 1, {timestamp}
);

-- 4. Create a Downloadable File Lesson
INSERT INTO {db_conf['db_name']}.files (
    creator_id, webinar_id, chapter_id, accessibility, downloadable, storage, file, volume, file_type, `order`, status, created_at, updated_at
) VALUES (
    1015, 2065, @chapter_id, 'free', 1, 'external_link', 'https://scholarb.org/uploads/lessons/sample.pdf', '1.2MB', 'pdf', 2, 'active', {timestamp}, {timestamp}
);

SET @file_id = LAST_INSERT_ID();

INSERT INTO {db_conf['db_name']}.file_translations (
    file_id, locale, title, description
) VALUES (
    @file_id, 'en', 'Course Syllabus & Introduction', 'Download the introductory PDF and syllabus.'
);

INSERT INTO {db_conf['db_name']}.webinar_chapter_items (
    user_id, chapter_id, item_id, type, `order`, created_at
) VALUES (
    1015, @chapter_id, @file_id, 'file', 2, {timestamp}
);

SELECT 'SUCCESS' AS result, @chapter_id AS chapter_id, @session_id AS session_id, @file_id AS file_id;
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

print("Executing SQL queries to populate the course...")
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
