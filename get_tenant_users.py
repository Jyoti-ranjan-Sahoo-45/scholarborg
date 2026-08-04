import urllib.request
import urllib.parse
import json

# Load configuration
with open('d:/scholarborg/config.json') as f:
    config = json.load(f)

ssh_conf = config['ssh']
db_conf = config['lms']['database']

# Since we don't have the DB loaded locally, let's hardcode the credentials from show_settings.py
clientId = "8879ec02-43e0-4108-b1a3-1e06558ed2f7"
clientSecret = "oaZ8Q~F4BVjqujDzIH8TriQXOsnfDafX_pGNaa.F"
tenantId = "5455bc11-263a-427d-a7e9-b7503852ec1b"

# 1. Get Access Token
token_url = f"https://login.microsoftonline.com/{tenantId}/oauth2/v2.0/token"
data = urllib.parse.urlencode({
    'grant_type': 'client_credentials',
    'client_id': clientId,
    'client_secret': clientSecret,
    'scope': 'https://graph.microsoft.com/.default',
}).encode('utf-8')

req = urllib.request.Request(token_url, data=data, headers={'Content-Type': 'application/x-www-form-urlencoded'})

try:
    with urllib.request.urlopen(req) as response:
        res_data = json.loads(response.read().decode())
        accessToken = res_data.get('access_token')
except Exception as token_err:
    print("Error retrieving access token:", token_err)
    exit(1)

if not accessToken:
    print("Access token not found in response.")
    exit(1)

print("Access token obtained successfully!\n")

# 2. Get Users List from Microsoft Graph API
users_url = "https://graph.microsoft.com/v1.0/users"
req_users = urllib.request.Request(users_url, headers={'Authorization': f'Bearer {accessToken}'})

try:
    with urllib.request.urlopen(req_users) as response:
        res_data = json.loads(response.read().decode())
        users = res_data.get('value', [])
        
        print(f"Found {len(users)} users in the Azure AD Tenant:\n")
        print(f"{'Display Name':<30} | {'User Principal Name (UPN)':<45} | {'User ID (GUID)':<36}")
        print("-" * 118)
        for user in users:
            name = user.get('displayName', 'N/A')
            upn = user.get('userPrincipalName', 'N/A')
            guid = user.get('id', 'N/A')
            print(f"{name:<30} | {upn:<45} | {guid:<36}")
except Exception as graph_err:
    print("Error querying Microsoft Graph API /users:", graph_err)
    # Check if we can read the response body for detailed error info
    if hasattr(graph_err, 'read'):
        print(graph_err.read().decode())
