import urllib.request

def check_site(url):
    print(f"Checking {url}...")
    try:
        req = urllib.request.Request(url, method='HEAD')
        # Add User-Agent to avoid generic crawler blocks
        req.add_header('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')
        with urllib.request.urlopen(req, timeout=15) as response:
            status = response.getcode()
            print(f"  Result: {status}")
            return status == 200
    except Exception as e:
        print(f"  Error: {e}")
        return False

def main():
    lms_ok = check_site("https://scholarb.org")
    crm_ok = check_site("https://scholarb.org/crm")
    
    if lms_ok and crm_ok:
        print("SUCCESS: Both environments returned 200 OK!")
    else:
        print("FAILURE: One or both environments returned non-200 or errored.")

if __name__ == '__main__':
    main()
