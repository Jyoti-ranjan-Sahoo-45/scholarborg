Add-Type -AssemblyName System.Drawing

$srcPath = "c:\jyoti\scholarborg\The Education Challenge in India.png"
$img = [System.Drawing.Bitmap]::FromFile($srcPath)
Write-Host "Image Dimensions: $($img.Width) x $($img.Height)"
$img.Dispose()
