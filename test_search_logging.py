import urllib.request
import urllib.parse

def main():
    url = "https://scholarb.org/course/Accessibility-in-UI-UX-Design"
    headers = {
        'User-Agent': 'Mozilla/5.0 (Linux; Android 13; SM-S901B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36'
    }
    
    print(f"Sending search request to {url} with iPhone User-Agent...")
    req = urllib.request.Request(url, headers=headers)
    try:
        with urllib.request.urlopen(req) as response:
            status_code = response.getcode()
            print(f"Response Status: {status_code}")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == '__main__':
    main()
