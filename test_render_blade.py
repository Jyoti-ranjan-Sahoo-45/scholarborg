"""
Fetch step 2 page directly via HTTP request using a mock logged-in session,
or check if we can inspect the generated HTML for is_demo input.
"""
import requests

# Let's get the page. Since it requires authentication (redirects to login if not logged in),
# we need to simulate the login or check if the HTML is generated.
# But wait, we can also check the controller output or run a quick artisan command to render the view with mocks!
# Let's run a php script that renders the view 'design_1.web.users.meeting.steps.step_2' with a mocked meeting and request.
import json, paramiko

with open('config.json') as f:
    c = json.load(f)

s = c['ssh']
p = c['lms']['remote_path']

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'],
            look_for_keys=False, allow_agent=False, timeout=30)

# We will run a tinker command to render the step_2 blade file with is_demo=1 request
tinker_cmd = (
    "request()->merge(['is_demo' => 1]); "
    "$meeting = \\App\\Models\\Meeting::first(); "
    "$time = \\App\\Models\\MeetingTime::first(); "
    "$instructor = \\App\\User::find(923); "
    "print(view('design_1.web.users.meeting.steps.step_2', ["
    "  'meeting' => $meeting, "
    "  'selectedTime' => $time, "
    "  'selectedDate' => time(), "
    "  'allowInPersonType' => true, "
    "  'instructor' => $instructor"
    "])->render());"
)

cmd = f"php {p}/artisan tinker --execute=\"{tinker_cmd}\""
_, out, err = ssh.exec_command(cmd)
html = out.read().decode('utf-8', errors='ignore')
print("HTML checks:")
print("Has 'input type=\"hidden\" name=\"is_demo\" value=\"1\"':", "name=\"is_demo\" value=\"1\"" in html)
print("Has 'is_demo=1' in back link:", "is_demo=1" in html)

ssh.close()
