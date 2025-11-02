#!/bin/bash

# Laravel Scheduler Setup Script for EC2
# Run this script step by step or copy commands manually

echo "=== Step 1: Find Your Laravel Application ==="
echo "Current directory: $(pwd)"
echo ""
echo "Checking if Laravel app is in /var/www..."
if [ -f "/var/www/artisan" ]; then
    echo "✅ Found Laravel at /var/www"
    APP_PATH="/var/www"
elif [ -f "/var/www/html/artisan" ]; then
    echo "✅ Found Laravel at /var/www/html"
    APP_PATH="/var/www/html"
elif [ -f "/var/www/laravel/artisan" ]; then
    echo "✅ Found Laravel at /var/www/laravel"
    APP_PATH="/var/www/laravel"
else
    echo "⚠️  Could not find artisan file. Please navigate to your Laravel root directory."
    echo "Looking for artisan file..."
    find /var/www -name "artisan" -type f 2>/dev/null | head -5
    exit 1
fi

echo ""
echo "=== Step 2: Find PHP Path ==="
PHP_PATH=$(which php)
if [ -z "$PHP_PATH" ]; then
    PHP_PATH="/usr/bin/php"
    echo "⚠️  Could not find php, defaulting to $PHP_PATH"
else
    echo "✅ Found PHP at: $PHP_PATH"
fi

echo ""
echo "=== Step 3: Verify PHP Version ==="
$PHP_PATH -v

echo ""
echo "=== Step 4: Test Schedule Command ==="
cd $APP_PATH
$PHP_PATH artisan schedule:list

echo ""
echo "=== Step 5: Manual Test Run ==="
echo "Testing if schedule:run works..."
$PHP_PATH artisan schedule:run

echo ""
echo "=== Step 6: Setup Cron Job ==="
echo ""
echo "Which user runs your web server?"
echo "1) www-data (most common)"
echo "2) nginx"
echo "3) apache"
echo "4) root (not recommended for production, but OK for testing)"
read -p "Enter choice (1-4): " user_choice

case $user_choice in
    1) CRON_USER="www-data" ;;
    2) CRON_USER="nginx" ;;
    3) CRON_USER="apache" ;;
    4) CRON_USER="root" ;;
    *) CRON_USER="www-data" ;;
esac

echo ""
echo "Setting up cron for user: $CRON_USER"
echo ""
echo "Cron command that will be added:"
echo "* * * * * cd $APP_PATH && $PHP_PATH artisan schedule:run >> /dev/null 2>&1"
echo ""

read -p "Add this cron job? (y/n): " confirm
if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
    # Create or update cron job
    (crontab -u $CRON_USER -l 2>/dev/null | grep -v "artisan schedule:run"; echo "* * * * * cd $APP_PATH && $PHP_PATH artisan schedule:run >> /dev/null 2>&1") | crontab -u $CRON_USER -
    
    echo "✅ Cron job added successfully!"
    echo ""
    echo "=== Step 7: Verify Cron Job ==="
    echo "Current cron jobs for $CRON_USER:"
    crontab -u $CRON_USER -l
    echo ""
    echo "✅ Setup complete!"
    echo ""
    echo "The scheduler will now run every minute."
    echo "Your expense notifications will run daily at the configured hour."
    echo ""
    echo "To check if it's working, wait a minute and check logs:"
    echo "  tail -f $APP_PATH/storage/logs/laravel.log"
else
    echo "Setup cancelled. Run this script again when ready."
fi

