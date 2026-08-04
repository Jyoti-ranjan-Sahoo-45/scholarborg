import urllib.request
import urllib.parse

def main():
    url = "https://scholarb.org/zoho/verify-lead"
    
    # We must retrieve the current cookies (like session cookies) if any,
    # but since this is a simple stateless check, we can just send the POST request.
    # Note: Laravel web routes are protected by CSRF. Wait! The verify-lead POST route
    # is a standard web route and DOES require CSRF token!
    # So we cannot easily make a direct POST request from python unless we fetch the GET page first,
    # extract the CSRF token from the form, and send it back with a CookieJar.
    # Let's write the python script to handle this properly using urllib's HTTPCookieProcessor.
    
    import http.cookiejar
    # Parse manual using string splits
    cj = http.cookiejar.CookieJar()
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))
    urllib.request.install_opener(opener)
    
    # 1. Fetch GET /zoho/verify-lead?email=plmpl.test@gmail.com
    get_url = f"{url}?email=plmpl.test@gmail.com"
    print(f"Fetching GET page: {get_url}")
    try:
        with urllib.request.urlopen(get_url) as response:
            html = response.read().decode('utf-8')
            
        # Parse CSRF token
        csrf_token = ""
        for line in html.split('\n'):
            if 'name="_token"' in line or 'csrf-token' in line:
                # Extract value
                parts = line.split('value="')
                if len(parts) > 1:
                    csrf_token = parts[1].split('"')[0]
                    break
        
        if not csrf_token:
            # Try to find csrf_token from layout metas
            parts = html.split('name="csrf-token" content="')
            if len(parts) > 1:
                csrf_token = parts[1].split('"')[0]
                
        print(f"Extracted CSRF Token: {csrf_token}")
        
        # 2. Query database for current OTP code
        # Since we are running on the local system, we can't query the DB directly inside this script
        # unless we parse from DB or pass it. We'll pass the OTP code as a script parameter or we hardcode it.
        # Let's prompt for OTP code in console or read it.
        import sys
        if len(sys.argv) < 2:
            print("Usage: python test_otp_verify.py <otp_code>")
            return
            
        otp_code = sys.argv[1]
        
        # 3. Post verification
        payload = {
            '_token': csrf_token,
            'email': 'plmpl.test@gmail.com',
            'otp': otp_code
        }
        
        post_data = urllib.parse.urlencode(payload).encode('utf-8')
        req = urllib.request.Request(url, data=post_data, method='POST')
        req.add_header('Content-Type', 'application/x-www-form-urlencoded')
        
        print(f"Submitting OTP code {otp_code}...")
        with urllib.request.urlopen(req) as response:
            status_code = response.getcode()
            print(f"Response Status Code: {status_code}")
            # Laravel redirects to / on success, let's see where we land
            print(f"Final Landed URL: {response.geturl()}")
            
    except Exception as e:
        print(f"Error: {e}")

if __name__ == '__main__':
    main()
