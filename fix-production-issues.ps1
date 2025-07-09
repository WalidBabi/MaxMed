Write-Host "=== Fixing Production Issues ===" -ForegroundColor Green

# Clear all caches
Write-Host "Clearing caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Run migrations
Write-Host "Running migrations..." -ForegroundColor Yellow
php artisan migrate --force

# Check database connection
Write-Host "Checking database connection..." -ForegroundColor Yellow
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful';"

# Optimize for production
Write-Host "Optimizing for production..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "=== Fix Complete ===" -ForegroundColor Green
Write-Host "Please test the login functionality now." -ForegroundColor Cyan 