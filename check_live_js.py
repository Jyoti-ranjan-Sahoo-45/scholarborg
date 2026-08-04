"""
Download and check the contents of the live minified JS on the website.
"""
import requests

url = "https://scholarb.org/assets/design_1/js/parts/profile_reserve_meeting.min.js"
print("Fetching:", url)
r = requests.get(url, headers={"User-Agent": "Mozilla/5.0"})
content = r.text

print(f"Status Code: {r.status_code}")
print(f"Content Length: {len(content)} bytes")
print("Has 'is_demo' inside JS:", "is_demo" in content)
print()

# Print the section around js-proceed-to-booking-btn
index = content.find("js-proceed-to-booking-btn")
if index != -1:
    print("Found proceed button code around index:", index)
    print(content[index-100:index+300])
else:
    print("proceed button code NOT found!")
