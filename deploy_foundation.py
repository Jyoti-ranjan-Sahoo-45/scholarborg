import os
import sys
import json
import paramiko
import requests

def deploy():
    with open('config.json') as f:
        config = json.load(f)

    s = config['ssh']
    remote_base = config['lms']['remote_path'] # /home/u181357068/domains/scholarb.org/public_html
    remote_public = os.path.join(remote_base, 'public').replace('\\', '/')
    local_dist = os.path.abspath('frontend/dist')

    print("=" * 60)
    print("DEPLOYING SCHOLARBEE FOUNDATION REACT APP TO PRODUCTION")
    print(f"Server: {s['host']}:{s['port']}")
    print(f"Domain: {s['domain']}")
    print("=" * 60)

    # 1. Connect SSH
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(s['host'], port=s['port'], username=s['username'], password=s['password'], timeout=30)
    sftp = ssh.open_sftp()
    print(" Connected via SSH & SFTP")

    # 2. Upload foundation-assets
    remote_assets_dir = f"{remote_public}/foundation-assets"
    try:
        sftp.mkdir(remote_assets_dir)
        print(f" Created remote dir: {remote_assets_dir}")
    except IOError:
        pass # Directory exists

    # 2. Upload index.html as foundation.html first
    local_html = os.path.join(local_dist, 'index.html')
    remote_html = f"{remote_public}/foundation.html"
    print(f" Uploading index.html -> {remote_html}...")
    sftp.put(local_html, remote_html, confirm=False)

    # 3. Upload foundation-assets
    local_assets_dir = os.path.join(local_dist, 'foundation-assets')
    for fname in os.listdir(local_assets_dir):
        l_path = os.path.join(local_assets_dir, fname)
        r_path = f"{remote_assets_dir}/{fname}"
        l_size = os.path.getsize(l_path)
        try:
            r_stat = sftp.stat(r_path)
            if r_stat.st_size > 0:
                print(f"  [Skipped] {fname} (already on server, {r_stat.st_size:,} bytes)")
                continue
        except IOError:
            pass
        print(f"  Uploading {fname} ({l_size:,} bytes)...")
        sftp.put(l_path, r_path, confirm=False)

    # Verify no rogue index.html exists in public_html or public
    try:
        sftp.remove(f"{remote_public}/index.html")
        print(" Removed conflicting public/index.html")
    except IOError:
        pass

    try:
        sftp.remove(f"{remote_base}/index.html")
        print(" Removed conflicting public_html/index.html")
    except IOError:
        pass

    # 4. Update routes/web.php
    print(" Updating routes/web.php...")
    remote_web_php = f"{remote_base}/routes/web.php"
    
    # Read remote web.php
    with sftp.open(remote_web_php, 'r') as f:
        web_content = f.read().decode('utf-8')

    # Backup if not already backed up
    try:
        sftp.stat(f"{remote_web_php}.bak")
    except IOError:
        with sftp.open(f"{remote_web_php}.bak", 'w') as f:
            f.write(web_content)
        print(" Created backup: web.php.bak")

    target_str = "Route::get('/', 'HomeController@index');"
    replacement_str = """// Scholarbee Foundation & Tutoring React App
    $foundationPaths = [
        '/',
        '/education-challenge',
        '/about',
        '/who-we-are',
        '/our-story',
        '/programmes',
        '/programmes/{sub}',
        '/impact',
        '/governance',
        '/donate',
        '/volunteer',
        '/careers',
        '/contact',
        '/tutoring',
        '/privacy',
        '/terms',
        '/cookies',
    ];

    foreach ($foundationPaths as $fPath) {
        Route::get($fPath, function () {
            if (file_exists(public_path('foundation.html'))) {
                return response()->file(public_path('foundation.html'));
            }
            return redirect('/panel');
        });
    }

    // Native LMS landing page backup accessible at /lms-home
    Route::get('/lms-home', 'HomeController@index');"""

    import re
    if target_str in web_content:
        new_web_content = web_content.replace(target_str, replacement_str, 1)
        with sftp.open(remote_web_php, 'w') as f:
            f.write(new_web_content)
        print(" Successfully updated routes/web.php with Foundation & Tutoring routes!")
    elif "foundationPaths" in web_content:
        pattern = r"// Scholarbee Foundation & Tutoring React App.*?Route::get\('/lms-home', 'HomeController@index'\);"
        new_web_content = re.sub(pattern, replacement_str, web_content, flags=re.DOTALL)
        with sftp.open(remote_web_php, 'w') as f:
            f.write(new_web_content)
        print(" Successfully refreshed foundationPaths in routes/web.php with /our-story and /who-we-are!")
    else:
        print(" WARNING: target string not found in web.php, please verify manually.")

    # 5. Clear Laravel Route & View Cache
    print(" Clearing Laravel route & view cache...")
    cmd = f"cd {remote_base} && php artisan route:clear && php artisan view:clear"
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print("   " + stdout.read().decode().strip())

    sftp.close()
    ssh.close()
    print(" SSH connection closed.")

    # 6. Verification
    print("\n" + "=" * 60)
    print("VERIFYING LIVE ENDPOINTS")
    print("=" * 60)

    urls = [
        ("Home (Scholarbee Foundation)", "https://scholarb.org/"),
        ("The Education Challenge Page", "https://scholarb.org/education-challenge"),
        ("Who We Are Page", "https://scholarb.org/about"),
        ("Our Story Page", "https://scholarb.org/our-story"),
        ("Supplementary Tutoring Hub", "https://scholarb.org/tutoring"),
        ("CRM System", "https://scholarb.org/crm"),
        ("Rocket LMS Login", "https://scholarb.org/login"),
        ("Rocket LMS Native Landing", "https://scholarb.org/lms-home"),
    ]

    all_passed = True
    for label, url in urls:
        try:
            r = requests.get(url, timeout=15, headers={'User-Agent': 'Mozilla/5.0'}, verify=False)
            status = r.status_code
            if status in [200, 301, 302]:
                print(f" [OK] {label}: HTTP {status} ({url})")
            else:
                print(f" [WARN] {label}: HTTP {status} ({url})")
                all_passed = False
        except Exception as e:
            print(f" [FAIL] {label}: {e} ({url})")
            all_passed = False

    if all_passed:
        print("\n ALL PRODUCTION CHECKS PASSED SUCCESSFULLY!")
    else:
        print("\n SOME CHECKS RETURNED WARNINGS. Check logs above.")

if __name__ == '__main__':
    deploy()
