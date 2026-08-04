"""
Download the profile page HTML and check if 'Book a Free Demo' button is present.
"""
import requests

url = "https://scholarb.org/users/Robert-Ransdell"
print("Fetching:", url)
r = requests.get(url, headers={"User-Agent": "Mozilla/5.0"})
html = r.text

print(f"Status Code: {r.status_code}")
print("Has 'Book a Free Demo' link:", "Book a Free Demo" in html)
print("Has 'is_demo=1' query param:", "is_demo=1" in html)

# Let's search for the link block
index = html.find("Book a Free Demo")
if index != -1:
    print("\nButton HTML snippet:")
    print(html[index-150:index+150])
else:
    print("\nButton NOT found in the HTML!")
