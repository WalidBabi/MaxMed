#!/bin/bash

echo "=== Fixing Production Issues ==="

# Clear all caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Check database connection
echo "Checking database connection..."
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful';"

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Fix Complete ==="
echo "Please test the login functionality now." 