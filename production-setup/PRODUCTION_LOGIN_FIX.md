# Production Login 500 Error Fix Guide

## Problem Description
You're experiencing a 500 Internal Server Error when trying to login to the admin panel in production at `https://maxmedme.com/login`.

## Quick Diagnostic Steps

### 1. Check Laravel Logs
```bash
# SSH into your production server
ssh your-server

# Check Laravel logs
tail -f /path/to/your/laravel/storage/logs/laravel.log

# Check for recent errors
grep -i "error\|exception" /path/to/your/laravel/storage/logs/laravel.log | tail -20
```

### 2. Run Diagnostic Command
```bash
# Navigate to your Laravel project directory
cd /path/to/your/laravel/project

# Run the diagnostic command
php artisan app:check-production-issue

# If issues are found, run with fix option
php artisan app:check-production-issue --fix
```

## Common Causes and Solutions

### 1. Database Connection Issues
**Symptoms**: Database connection errors in logs
**Solution**:
```bash
# Check database connection
php artisan tinker
DB::connection()->getPdo();
exit

# If connection fails, check your .env file:
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### 2. Missing Database Tables
**Symptoms**: "Table doesn't exist" errors
**Solution**:
```bash
# Run migrations
php artisan migrate --force

# If migrations fail, check migration status
php artisan migrate:status

# Reset and re-run if needed (WARNING: This will lose data)
php artisan migrate:fresh --seed
```

### 3. Session Configuration Issues
**Symptoms**: Session-related errors
**Solution**:
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check session table exists
php artisan session:table
php artisan migrate --force
```

### 4. Missing Environment Variables
**Symptoms**: Configuration errors
**Solution**: Ensure these variables are set in your `.env` file:
```env
APP_NAME="MaxMed UAE"
APP_ENV=production
APP_DEBUG=false
APP_KEY=your-32-character-key
APP_URL=https://maxmedme.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database

# For reCAPTCHA (if using)
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key
```

### 5. Storage Permissions
**Symptoms**: Permission denied errors
**Solution**:
```bash
# Set proper permissions
sudo chown -R www-data:www-data /path/to/your/laravel/storage
sudo chown -R www-data:www-data /path/to/your/laravel/bootstrap/cache
sudo chmod -R 775 /path/to/your/laravel/storage
sudo chmod -R 775 /path/to/your/laravel/bootstrap/cache
```

### 6. Missing Admin Users
**Symptoms**: No admin users in database
**Solution**:
```bash
# Create admin user via tinker
php artisan tinker

# Create admin role if it doesn't exist
$adminRole = App\Models\Role::firstOrCreate(
    ['name' => 'admin'],
    [
        'display_name' => 'Administrator',
        'permissions' => ['dashboard.view'],
        'is_active' => true
    ]
);

# Create admin user
$admin = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@maxmedme.com',
    'password' => bcrypt('your-secure-password'),
    'role_id' => $adminRole->id,
    'email_verified_at' => now()
]);

exit
```

## Emergency Fix Script

Create and run this script if you need immediate access:

```bash
#!/bin/bash
# emergency-fix.sh

echo "🔧 Emergency Production Fix Script"
echo "=================================="

# Navigate to Laravel directory
cd /path/to/your/laravel/project

# Clear all caches
echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Create sessions table if missing
echo "Creating sessions table..."
php artisan session:table
php artisan migrate --force

# Set permissions
echo "Setting permissions..."
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Create admin user if none exists
echo "Checking admin users..."
ADMIN_COUNT=$(php artisan tinker --execute="echo App\Models\User::whereHas('role', function(\$q) { \$q->where('name', 'admin'); })->count();" 2>/dev/null)

if [ "$ADMIN_COUNT" -eq 0 ]; then
    echo "Creating default admin user..."
    php artisan tinker --execute="
        \$adminRole = App\Models\Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator', 'permissions' => ['dashboard.view'], 'is_active' => true]);
        App\Models\User::create(['name' => 'Admin', 'email' => 'admin@maxmedme.com', 'password' => bcrypt('admin123'), 'role_id' => \$adminRole->id, 'email_verified_at' => now()]);
        echo 'Admin user created: admin@maxmedme.com / admin123';
    "
fi

echo "✅ Emergency fix completed!"
```

## Production Environment Checklist

### Before Deployment
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate `APP_KEY` if not set
- [ ] Configure database connection
- [ ] Set up proper file permissions
- [ ] Configure session and cache drivers
- [ ] Set up reCAPTCHA keys (if using)

### After Deployment
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan storage:link`
- [ ] Clear all caches
- [ ] Test login functionality
- [ ] Verify admin user exists
- [ ] Check error logs

## Monitoring and Prevention

### 1. Set up Log Monitoring
```bash
# Monitor logs in real-time
tail -f storage/logs/laravel.log

# Set up log rotation
sudo logrotate -f /etc/logrotate.d/laravel
```

### 2. Health Check Endpoint
Add this route to check system health:
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => Cache::get('health_check') ? 'working' : 'not working',
        'timestamp' => now()
    ]);
});
```

### 3. Regular Maintenance
```bash
# Add to crontab for daily maintenance
0 2 * * * cd /path/to/your/laravel && php artisan cache:clear
0 3 * * * cd /path/to/your/laravel && php artisan queue:restart
```

## Contact Information
If you continue to experience issues after following this guide, please:
1. Check the Laravel logs for specific error messages
2. Run the diagnostic command: `php artisan app:check-production-issue`
3. Contact your system administrator with the error details

## Security Notes
- Always use strong passwords for admin accounts
- Keep your Laravel version updated
- Regularly backup your database
- Monitor access logs for suspicious activity
- Use HTTPS in production
- Consider implementing rate limiting on login attempts 