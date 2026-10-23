Add-Type -AssemblyName System.Drawing

$srcPath = "c:\jyoti\scholarborg\The Education Challenge in India.png"
$img = [System.Drawing.Bitmap]::FromFile($srcPath)

function Crop-Image($x, $y, $w, $h, $destName) {
    $rect = New-Object System.Drawing.Rectangle($x, $y, $w, $h)
    $cropped = $img.Clone($rect, $img.PixelFormat)
    $destPath = "c:\jyoti\scholarborg\scholarborg\frontend\src\assets\$destName"
    
    # Save as high quality JPEG
    $encoder = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.FormatDescription -eq "JPEG" }
    $encoderParams = New-Object System.Drawing.Imaging.EncoderParameters(1)
    $encoderParams.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, [long]95)
    
    $cropped.Save($destPath, $encoder, $encoderParams)
    $cropped.Dispose()
    Write-Host "Saved $destName ($w x $h) from ($x, $y)"
}

# 1. Hero Girl portrait (without the left text, retaining the classroom context and the girl looking up)
# In 1536x1024:
# Navbar is ~0-65. Hero text is left ~0-650.
# The girl photo occupies ~x=650 to 1536, y=65 to 385.
Crop-Image 680 65 850 320 "edu_hero_girl.jpg"

# 2. Five challenge cards photos:
# Let's inspect the cards row:
# Total width 1536. Margins ~90 on left and right, remaining ~1350 / 5 = ~270 per card.
# Cards are at approximately y=596 to 690 (height ~95px)
# Let's verify card X positions:
Crop-Image 542 596 167 92 "edu_challenge_access.jpg"
Crop-Image 722 596 168 92 "edu_challenge_affordability.jpg"
Crop-Image 906 596 168 92 "edu_challenge_support.jpg"
Crop-Image 1089 596 168 92 "edu_challenge_digital.jpg"
Crop-Image 1273 596 168 92 "edu_challenge_financial.jpg"

# 3. Bottom left response photo:
# Group of students smiling over books
Crop-Image 0 790 710 234 "edu_response_students.jpg"

$img.Dispose()
