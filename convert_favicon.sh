#!/bin/bash
# Script to convert favicon.ico to all required formats

# Ensure ImageMagick is installed
echo "This script requires ImageMagick to be installed."
echo "Please make sure you have it installed before running this script."
echo "You can download it from https://imagemagick.org/script/download.php"
echo "Or use your package manager (e.g., apt-get install imagemagick)"

# Create directory if it doesn't exist
mkdir -p public/img/favicon

# Convert favicon.ico to all required formats
echo "Converting favicon.ico to all required formats..."

# Extract the icon from favicon.ico and create all necessary sizes
convert public/favicon.ico -resize 16x16 public/img/favicon/favicon-16x16.png
convert public/favicon.ico -resize 32x32 public/img/favicon/favicon-32x32.png
convert public/favicon.ico -resize 180x180 public/img/favicon/apple-touch-icon.png
convert public/favicon.ico -resize 192x192 public/img/favicon/android-chrome-192x192.png
convert public/favicon.ico -resize 512x512 public/img/favicon/android-chrome-512x512.png
convert public/favicon.ico -resize 150x150 public/img/favicon/mstile-150x150.png

# Create a Safari pinned tab SVG (this is a simple placeholder, you may want to customize it)
echo '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><path fill="#171e60" d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0z"/></svg>' > public/img/favicon/safari-pinned-tab.svg

echo "Conversion complete. Favicon files created in public/img/favicon/"
echo "Now you can use these files in your website." 