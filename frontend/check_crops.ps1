Add-Type -AssemblyName System.Drawing

$srcPath = "c:\jyoti\scholarborg\The Education Challenge in India.png"
$img = [System.Drawing.Bitmap]::FromFile($srcPath)

# Let's inspect where the card images actually start and end
# By scanning along a vertical line around x=600 to find where the card image starts and ends
Write-Host "Image total size: $($img.Width) x $($img.Height)"

# Let's write out an HTML preview file to visually inspect the crops!
$html = @"
<!DOCTYPE html>
<html>
<body style="background: #111; color: #fff; font-family: sans-serif; padding: 20px;">
  <h2>Extracted Assets from Mockup</h2>
  <h3>Hero Girl</h3>
  <img src="edu_hero_girl.jpg" style="border: 2px solid cyan; max-width: 800px; display: block; margin-bottom: 20px;" />
  
  <h3>5 Challenge Cards</h3>
  <div style="display: flex; gap: 10px;">
    <div><p>1. Access</p><img src="edu_challenge_access.jpg" style="border: 2px solid cyan; width: 168px;" /></div>
    <div><p>2. Affordability</p><img src="edu_challenge_affordability.jpg" style="border: 2px solid cyan; width: 168px;" /></div>
    <div><p>3. Support</p><img src="edu_challenge_support.jpg" style="border: 2px solid cyan; width: 168px;" /></div>
    <div><p>4. Digital</p><img src="edu_challenge_digital.jpg" style="border: 2px solid cyan; width: 168px;" /></div>
    <div><p>5. Financial</p><img src="edu_challenge_financial.jpg" style="border: 2px solid cyan; width: 168px;" /></div>
  </div>

  <h3>Bottom Response Students</h3>
  <img src="edu_response_students.jpg" style="border: 2px solid cyan; max-width: 700px; display: block; margin-top: 20px;" />
</body>
</html>
"@

$html | Out-File "c:\jyoti\scholarborg\scholarborg\frontend\src\assets\preview_crops.html" -Encoding utf8
Write-Host "Created preview_crops.html"
$img.Dispose()
