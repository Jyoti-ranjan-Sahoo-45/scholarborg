import urllib.request
import urllib.parse
import json

def main():
    url = "https://scholarb.org/api/zoho/forms-webhook"
    
    # Dummy lead payload representing a Zoho Forms submission
    payload = {
        'name': 'Plmpl Test Student',
        'email': 'plmpl.test@gmail.com',
        'phone': '+919876543210',
        'subject': 'IIT-JEE Physics',
        'message': 'Hello, I am looking for a physics tutor for JEE preparation.',
        'zoho_lead_id': '55112233445566'
    }
    
    data = urllib.parse.urlencode(payload).encode('utf-8')
    req = urllib.request.Request(url, data=data, method='POST')
    req.add_header('Content-Type', 'application/x-www-form-urlencoded')
    
    print(f"Sending POST request to {url}...")
    try:
        with urllib.request.urlopen(req) as response:
            status_code = response.getcode()
            body = response.read().decode('utf-8')
            print(f"Status Code: {status_code}")
            print("Response Body:")
            print(body)
    except urllib.error.HTTPError as e:
        print(f"HTTP Error: {e.code}")
        print(e.read().decode('utf-8'))
    except Exception as e:
        print(f"Error: {e}")

if __name__ == '__main__':
    main()
