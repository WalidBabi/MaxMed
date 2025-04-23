# PowerShell script to convert favicon.ico to all required formats

# Ensure ImageMagick is installed
Write-Host "This script requires ImageMagick to be installed."
Write-Host "Please make sure you have it installed before running this script."
Write-Host "You can download it from https://imagemagick.org/script/download.php"

# Create directory if it doesn't exist
if (-not (Test-Path -Path "public\img\favicon")) {
    New-Item -Path "public\img\favicon" -ItemType Directory -Force
}

# Check if ImageMagick is installed
try {
    $magickPath = (Get-Command magick -ErrorAction Stop).Source
    Write-Host "ImageMagick found at: $magickPath"
} catch {
    Write-Host "ImageMagick not found in PATH. Please install it or add it to your PATH."
    Write-Host "You can download it from https://imagemagick.org/script/download.php"
    exit 1
}

# Convert favicon.ico to all required formats
Write-Host "Converting favicon.ico to all required formats..."

# Extract the icon from favicon.ico and create all necessary sizes
& magick convert "public\favicon.ico" -resize 16x16 "public\img\favicon\favicon-16x16.png"
& magick convert "public\favicon.ico" -resize 32x32 "public\img\favicon\favicon-32x32.png"
& magick convert "public\favicon.ico" -resize 180x180 "public\img\favicon\apple-touch-icon.png"
& magick convert "public\favicon.ico" -resize 192x192 "public\img\favicon\android-chrome-192x192.png"
& magick convert "public\favicon.ico" -resize 512x512 "public\img\favicon\android-chrome-512x512.png"
& magick convert "public\favicon.ico" -resize 150x150 "public\img\favicon\mstile-150x150.png"

# Create a Safari pinned tab SVG (this is a simple placeholder, you may want to customize it)
$svgContent = @'
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><path fill="#171e60" d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0z"/></svg>
'@

Set-Content -Path "public\img\favicon\safari-pinned-tab.svg" -Value $svgContent

Write-Host "Conversion complete. Favicon files created in public\img\favicon\"
Write-Host "Now you can use these files in your website." 