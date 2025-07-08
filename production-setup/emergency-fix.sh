#!/bin/bash

# Emergency Production Fix Script for MaxMed Login 500 Error
# Run this script on your production server to fix common login issues

echo "🔧 Emergency Production Fix Script for MaxMed"
echo "============================================="
echo ""

# Check if we're in a Laravel project directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: This script must be run from the Laravel project root directory"
    echo "Please navigate to your Laravel project directory and run this script again"
    exit 1
fi

echo "✅ Laravel project detected"
echo ""

# Function to run commands with error handling
run_command() {
    local cmd="$1"
    local description="$2"
    
    echo "🔄 $description..."
    if eval "$cmd"; then
        echo "✅ $description completed"
    else
        echo "❌ $description failed"
        return 1
    fi
    echo ""
}

# 1. Clear all caches
run_command "php artisan cache:clear" "Clearing application cache"
run_command "php artisan config:clear" "Clearing configuration cache"
run_command "php artisan route:clear" "Clearing route cache"
run_command "php artisan view:clear" "Clearing view cache"

# 2. Check and fix database connection
echo "🔍 Checking database connection..."
if php artisan tinker --execute="echo DB::connection()->getPdo() ? 'connected' : 'disconnected';" 2>/dev/null | grep -q "connected"; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed"
    echo "Please check your .env file database configuration"
    echo "Required variables: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
    exit 1
fi
echo ""

# 3. Run migrations
run_command "php artisan migrate --force" "Running database migrations"

# 4. Create sessions table if it doesn't exist
if ! php artisan tinker --execute="echo Schema::hasTable('sessions') ? 'exists' : 'missing';" 2>/dev/null | grep -q "exists"; then
    run_command "php artisan session:table" "Creating sessions table"
    run_command "php artisan migrate --force" "Running sessions migration"
else
    echo "✅ Sessions table already exists"
fi
echo ""

# 5. Set proper file permissions
echo "🔧 Setting file permissions..."
if command -v sudo &> /dev/null; then
    sudo chown -R www-data:www-data storage 2>/dev/null || sudo chown -R apache:apache storage 2>/dev/null || echo "⚠️ Could not set storage ownership"
    sudo chown -R www-data:www-data bootstrap/cache 2>/dev/null || sudo chown -R apache:apache bootstrap/cache 2>/dev/null || echo "⚠️ Could not set cache ownership"
    sudo chmod -R 775 storage 2>/dev/null || echo "⚠️ Could not set storage permissions"
    sudo chmod -R 775 bootstrap/cache 2>/dev/null || echo "⚠️ Could not set cache permissions"
else
    chmod -R 775 storage 2>/dev/null || echo "⚠️ Could not set storage permissions"
    chmod -R 775 bootstrap/cache 2>/dev/null || echo "⚠️ Could not set cache permissions"
fi
echo "✅ File permissions set"
echo ""

# 6. Check for admin users
echo "🔍 Checking for admin users..."
ADMIN_COUNT=$(php artisan tinker --execute="echo App\Models\User::whereHas('role', function(\$q) { \$q->where('name', 'admin'); })->count();" 2>/dev/null | grep -E '^[0-9]+$' || echo "0")

if [ "$ADMIN_COUNT" -eq 0 ]; then
    echo "⚠️ No admin users found. Creating default admin user..."
    
    # Create admin role and user
    php artisan tinker --execute="
        try {
            \$adminRole = App\Models\Role::firstOrCreate(
                ['name' => 'admin'],
                [
                    'display_name' => 'Administrator',
                    'permissions' => ['dashboard.view'],
                    'is_active' => true
                ]
            );
            
            \$admin = App\Models\User::create([
                'name' => 'Admin',
                'email' => 'admin@maxmedme.com',
                'password' => bcrypt('admin123'),
                'role_id' => \$adminRole->id,
                'email_verified_at' => now()
            ]);
            
            echo '✅ Admin user created successfully';
            echo 'Email: admin@maxmedme.com';
            echo 'Password: admin123';
            echo '⚠️ Please change this password immediately after login!';
        } catch (Exception \$e) {
            echo '❌ Failed to create admin user: ' . \$e->getMessage();
        }
    " 2>/dev/null
else
    echo "✅ Found $ADMIN_COUNT admin user(s)"
fi
echo ""

# 7. Check environment configuration
echo "🔍 Checking environment configuration..."
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    
    # Check critical variables
    if grep -q "APP_KEY=" .env && ! grep -q "APP_KEY=base64:" .env; then
        echo "⚠️ APP_KEY not set. Generating new key..."
        php artisan key:generate --force
    fi
    
    if grep -q "APP_DEBUG=true" .env; then
        echo "⚠️ APP_DEBUG is enabled in production (security risk)"
        echo "Consider setting APP_DEBUG=false in your .env file"
    fi
else
    echo "❌ .env file not found"
    echo "Please create a .env file with proper configuration"
fi
echo ""

# 8. Test login functionality
echo "🧪 Testing login functionality..."
if php artisan tinker --execute="echo 'Laravel is working correctly';" 2>/dev/null | grep -q "Laravel is working correctly"; then
    echo "✅ Laravel application is working"
else
    echo "❌ Laravel application has issues"
fi
echo ""

# 9. Final status
echo "📊 Final Status Report"
echo "======================"

# Check if sessions table is working
if php artisan tinker --execute="echo DB::table('sessions')->count();" 2>/dev/null | grep -q -E '^[0-9]+$'; then
    echo "✅ Sessions table is working"
else
    echo "❌ Sessions table has issues"
fi

# Check if cache is working
if php artisan tinker --execute="Cache::put('test', 'working', 1); echo Cache::get('test');" 2>/dev/null | grep -q "working"; then
    echo "✅ Cache is working"
else
    echo "❌ Cache has issues"
fi

# Check if admin user exists
if [ "$ADMIN_COUNT" -gt 0 ]; then
    echo "✅ Admin user exists"
else
    echo "❌ No admin user found"
fi

echo ""
echo "🎉 Emergency fix completed!"
echo ""
echo "Next steps:"
echo "1. Try logging in at https://maxmedme.com/login"
echo "2. If admin user was created, use: admin@maxmedme.com / admin123"
echo "3. Change the default password immediately"
echo "4. Check the logs if issues persist: tail -f storage/logs/laravel.log"
echo ""
echo "If you still experience issues, run: php artisan app:check-production-issue" 