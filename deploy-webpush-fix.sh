#!/bin/bash
# WebPush Production Deployment Fix Script
# Run this on your production server after deploying

echo "🔧 Fixing WebPush deployment on production..."

# Navigate to project directory (adjust path as needed)
cd /path/to/maxmed || exit 1

# Install/update composer dependencies
echo "📦 Installing composer dependencies..."
composer install --no-dev --optimize-autoloader

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ WebPush deployment fix complete!"
echo ""
echo "📋 Verify the fix:"
echo "1. Check if vendor/minishlink/web-push exists"
echo "2. Check logs to ensure WebPush class is found"
echo "3. Test push notifications at https://maxmedme.com/push/test"

