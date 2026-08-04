"""
Deploy all changed LMS files with automatic SSH retry.
"""
import json, paramiko, os, time

with open('config.json') as f:
    c = json.load(f)

s  = c['ssh']
lp = 'd:\\scholarborg\\lms'
rp = c['lms']['remote_path']

# All files changed in this session
FILES = [
    'app/Http/Controllers/Admin/AppointmentsController.php',
    'app/Http/Controllers/Panel/MeetingController.php',
    'app/Http/Controllers/Web/MeetingController.php',
    'app/Http/Controllers/Web/InstructorFinderController.php',
    'resources/views/admin/appointments/create.blade.php',
    'resources/views/admin/appointments/edit.blade.php',
    'resources/views/admin/appointments/lists.blade.php',
    'resources/views/admin/settings/general/features.blade.php',
    'resources/views/design_1/panel/meeting/settings/tabs/basic_settings.blade.php',
    'resources/views/design_1/web/users/meeting/steps/step_1.blade.php',
    'resources/views/design_1/web/users/meeting/steps/step_2.blade.php',
    'resources/views/design_1/web/users/profile/includes/left_side.blade.php',
    'resources/views/design_1/web/instructor_finder/lists/top_filters.blade.php',
    'routes/custom_admin.php',
    'resources/js/design_1/parts/profile_reserve_meeting.js',
    'public/assets/design_1/js/parts/profile_reserve_meeting.min.js',
    'app/Http/Controllers/Panel/ReserveMeetingController.php',
    'resources/views/design_1/panel/meeting/modals/create_session.blade.php',
    'resources/js/design_1/panel/meeting_requests.js',
    'public/assets/design_1/js/panel/meeting_requests.min.js',
]

ARTISAN_CMDS = [
    f'php {rp}/artisan view:clear',
    f'php {rp}/artisan cache:clear',
    f'php {rp}/artisan config:clear',
]

MAX_RETRIES = 10

for attempt in range(1, MAX_RETRIES + 1):
    try:
        print(f"\nAttempt {attempt}/{MAX_RETRIES} — Connecting to {s['host']}:{s['port']}...")
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(s['host'], port=s['port'], username=s['username'],
                    password=s['password'], look_for_keys=False,
                    allow_agent=False, timeout=30)
        print("Connected!\n")

        sftp = ssh.open_sftp()

        print("Uploading files...")
        for rel in FILES:
            local  = os.path.join(lp, rel.replace('/', os.sep))
            remote = f"{rp}/{rel}"
            if os.path.exists(local):
                sftp.put(local, remote)
                print(f"  OK  {rel}")
            else:
                print(f"  SKIP (not found locally): {rel}")

        sftp.close()

        print("\nClearing caches...")
        for cmd in ARTISAN_CMDS:
            _, out, err = ssh.exec_command(cmd)
            result = out.read().decode().strip() or err.read().decode().strip()
            print(f"  {cmd.split('artisan ')[1]}: {result}")

        print("\nVerifying key files on server...")
        checks = [
            (f"{rp}/resources/views/design_1/panel/meeting/settings/tabs/basic_settings.blade.php", 'allow_demo'),
            (f"{rp}/app/Http/Controllers/Web/MeetingController.php", 'is_demo'),
            (f"{rp}/resources/views/design_1/web/instructor_finder/lists/top_filters.blade.php", 'demo_meetings'),
        ]
        for path, token in checks:
            _, out, _ = ssh.exec_command(f"grep -c '{token}' {path}")
            count = out.read().decode().strip()
            status = "VERIFIED" if int(count or 0) > 0 else "MISSING!"
            print(f"  [{status}] {os.path.basename(path)} — '{token}'")

        ssh.close()
        print("\n=== DEPLOY COMPLETE ===")
        break

    except Exception as e:
        print(f"  Failed: {e}")
        if attempt < MAX_RETRIES:
            wait = min(attempt * 10, 60)
            print(f"  Retrying in {wait}s...")
            time.sleep(wait)
        else:
            print("\nAll retries failed. Use Hostinger File Manager.")
