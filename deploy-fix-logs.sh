#!/bin/bash

# Deployment script to fix log permissions and ensure proper logging

echo "=== Fixing Log Permissions ==="

# Navigate to the application directory
cd /path/to/your/laravel/app

# Ensure storage/logs directory exists with proper permissions
mkdir -p storage/logs
chmod 755 storage/logs

# Create log files with proper permissions
touch storage/logs/laravel.log
touch storage/logs/custom.log
touch storage/logs/test.log
touch storage/logs/production-debug.log
touch storage/logs/critical-errors.log

# Set proper permissions for log files
chmod 664 storage/logs/*.log

# Set ownership to web server user (adjust as needed)
# For Apache: www-data
# For Nginx: www-data or nginx
# chown www-data:www-data storage/logs/*.log

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "=== Log permissions fixed successfully ==="
echo "=== Laravel caches cleared ==="

# Test logging
echo "=== Testing logging functionality ==="
php artisan tinker --execute="Log::info('Log test from deployment script');"

echo "=== Deployment script completed ===" 