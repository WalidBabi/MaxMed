#!/bin/bash

# Emergency Fix Script for MaxMed Production 500 Errors
# Run this script on your production server to fix common issues

echo "🚨 EMERGENCY FIX SCRIPT FOR MAXMED PRODUCTION"
echo "=============================================="
echo "This script will attempt to fix common 500 error causes."
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel project root."
    exit 1
fi

echo "📋 Step 1: Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

echo ""
echo "📋 Step 2: Checking database connection..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected successfully'; } catch(Exception \$e) { echo 'Database error: ' . \$e->getMessage(); }"

echo ""
echo "📋 Step 3: Running migrations..."
php artisan migrate --force

echo ""
echo "📋 Step 4: Creating sessions table if missing..."
php artisan session:table
php artisan migrate --force

echo ""
echo "📋 Step 5: Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/

echo ""
echo "📋 Step 6: Checking for admin users..."
php artisan tinker --execute="
\$adminCount = App\Models\User::whereHas('role', function(\$q) { \$q->where('name', 'admin'); })->count();
if (\$adminCount === 0) {
    echo 'No admin users found. Creating default admin...';
    \$role = App\Models\Role::where('name', 'admin')->first();
    if (!\$role) {
        \$role = App\Models\Role::create(['name' => 'admin', 'permissions' => json_encode(['*'])]);
    }
    \$user = App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@maxmedme.com',
        'password' => bcrypt('admin123'),
        'role_id' => \$role->id
    ]);
    echo 'Default admin created: admin@maxmedme.com / admin123';
} else {
    echo 'Admin users found: ' . \$adminCount;
}
"

echo ""
echo "📋 Step 7: Checking server configuration..."
php artisan app:check-server-config

echo ""
echo "📋 Step 8: Testing emergency diagnostic route..."
curl -s https://maxmedme.com/emergency-diagnostic | head -20

echo ""
echo "📋 Step 9: Testing health check route..."
curl -s https://maxmedme.com/health | head -20

echo ""
echo "✅ Emergency fix script completed!"
echo ""
echo "🔍 Next steps:"
echo "1. Try accessing https://maxmedme.com/login"
echo "2. Check logs: tail -f storage/logs/laravel.log"
echo "3. Check critical errors: tail -f storage/logs/critical-errors.log"
echo "4. If still having issues, check: tail -f storage/logs/production-debug.log"
echo ""
echo "📞 If the issue persists, contact your system administrator." 