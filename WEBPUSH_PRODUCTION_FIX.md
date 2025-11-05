# WebPush Production Fix Instructions

## Problem
The error `Class "Minishlink\WebPush\WebPush" not found` occurs because composer dependencies aren't installed on production.

## Solution

### Option 1: SSH into Production and Run

```bash
# Navigate to your project directory
cd /var/www/maxmed  # or wherever your project is

# Install/update composer dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan cache:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
```

### Option 2: Use the Deployment Script

If you have SSH access:
```bash
# Copy the script to production and run it
chmod +x deploy-webpush-fix.sh
./deploy-webpush-fix.sh
```

## Verify Fix

1. Check if the vendor directory exists:
   ```bash
   ls -la vendor/minishlink/web-push
   ```

2. Test the push notification endpoint:
   - Visit: `https://maxmedme.com/push/test`
   - Make sure you're logged in
   - Send a test notification

3. Check logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```
   
   You should NOT see the "Class not found" error anymore.

## What Was Fixed

1. ✅ Removed user behavior tracking from `/push/test` page
2. ✅ Fixed WebPush class not found (requires composer install on production)

## Next Steps After Fix

1. Run `composer install` on production (or deploy with dependencies)
2. Clear caches: `php artisan config:clear`
3. Test push notifications on your Samsung device
4. Check logs to confirm no more errors



