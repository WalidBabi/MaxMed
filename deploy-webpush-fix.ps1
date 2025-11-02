# WebPush Production Deployment Fix Script (PowerShell)
# Run this on your production server after deploying

Write-Host "🔧 Fixing WebPush deployment on production..." -ForegroundColor Cyan

# Navigate to project directory (adjust path as needed)
$projectPath = "/path/to/maxmed"  # UPDATE THIS PATH

if (!(Test-Path $projectPath)) {
    Write-Host "❌ Project path not found: $projectPath" -ForegroundColor Red
    Write-Host "Please update the script with the correct path." -ForegroundColor Yellow
    exit 1
}

Set-Location $projectPath

# Install/update composer dependencies
Write-Host "📦 Installing composer dependencies..." -ForegroundColor Yellow
composer install --no-dev --optimize-autoloader

# Clear all caches
Write-Host "🧹 Clearing caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
Write-Host "⚡ Optimizing for production..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "✅ WebPush deployment fix complete!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Verify the fix:" -ForegroundColor Cyan
Write-Host "1. Check if vendor/minishlink/web-push exists"
Write-Host "2. Check logs to ensure WebPush class is found"
Write-Host "3. Test push notifications at https://maxmedme.com/push/test"

