import paramiko
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']
lms_conf = config['lms']

# The correct settings dictionary
settings_dict = {
    "agora_resolution": None,
    "agora_max_bitrate": "2260",
    "agora_min_bitrate": "1130",
    "agora_frame_rate": "15",
    "agora_live_streaming": "0",
    "agora_chat": "0",
    "agora_in_free_courses": "0",
    "agora_for_meeting": "0",
    "meeting_live_stream_type": "single",
    "course_live_stream_type": "single",
    "agora_app_id": None,
    "agora_app_certificate": None,
    "new_interactive_file": "0",
    "timezone_in_register": "1",
    "timezone_in_create_webinar": "1",
    "sequence_content_status": "0",
    "webinar_assignment_status": "0",
    "webinar_private_content_status": "0",
    "disable_view_content_after_user_register": "0",
    "course_forum_status": "0",
    "direct_classes_payment_button_status": "1",
    "direct_bundles_payment_button_status": "1",
    "direct_products_payment_button_status": "1",
    "cookie_settings_status": "0",
    "mobile_app_status": "0",
    "maintenance_status": "0",
    "maintenance_access_key": "key",
    "extra_time_to_join_status": "1",
    "extra_time_to_join_default_value": "20",
    "show_other_register_method": "1",
    "show_certificate_additional_in_register": "0",
    "show_google_login_button": "0",
    "show_facebook_login_button": "0",
    "google_client_id": None,
    "google_client_secret": None,
    "facebook_client_id": None,
    "facebook_client_secret": None,
    "show_live_chat_widget": "0",
    "cashback_active": "0",
    "display_cashback_notice_in_the_product_page": "0",
    "display_minimum_amount_cashback_notices": "0",
    "available_session_apis": ["local", "big_blue_button", "zoom", "agora", "jitsi", "microsoft_teams"],
    "available_sources": ["upload", "youtube", "vimeo", "external_link", "google_drive", "iframe", "s3", "secure_host"],
    "bunny_configs": [],
    "select_the_role_during_registration": ["teacher", "organization"],
    "waitlist_status": "0",
    "upcoming_courses_status": "0",
    "user_register_form": None,
    "instructor_register_form": None,
    "organization_register_form": None,
    "become_instructor_form": None,
    "become_organization_form": None,
    "frontend_coupons_status": "1",
    "frontend_coupons_display_type": "before_content",
    "course_notes_status": "0",
    "course_notes_attachment": "0",
    "course_recent_reviews_status": "0",
    "zoom_client_id": None,
    "zoom_client_secret": None,
    "zoom_account_id": None,
    "bigbluebutton_server_base_url": None,
    "bigbluebutton_security_salt": None,
    "jitsi_live_url": None,
    "microsoft_teams_client_id": "8879ec02-43e0-4108-b1a3-1e06558ed2f7",
    "microsoft_teams_client_secret": "oaZ8Q~F4BVjqujDzIH8TriQXOsnfDafX_pGNaa.F",
    "microsoft_teams_tenant_id": "5455bc11-263a-427d-a7e9-b7503852ec1b",
    "microsoft_teams_fallback_email": "admin@scholarb.org"
}

new_settings_json = json.dumps(settings_dict, ensure_ascii=False)

# Escape single quotes inside the SQL value (e.g. for strings)
escaped_json = new_settings_json.replace("'", "\\'")

sql_update = (
    f"UPDATE {db_conf['db_name']}.setting_translations "
    f"SET value = '{escaped_json}' "
    f"WHERE setting_id = 31 AND locale = 'en';\n"
)

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

print("Connected! Executing mysql update via stdin to preserve quotes...")
# We invoke mysql without -e, and write the query directly to standard input
stdin, stdout, stderr = ssh.exec_command(f"mysql -u {db_conf['db_user']} -p'{db_conf['db_password']}' {db_conf['db_name']}")
stdin.write(sql_update)
stdin.close()

err = stderr.read().decode().strip()
if err:
    print("MySQL Output/Warning:", err)
else:
    print("Database updated successfully!")

# Clear cache
print("\nClearing Laravel cache...")
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
print("\nDone! Settings repaired successfully.")
