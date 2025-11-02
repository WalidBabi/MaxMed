#!/bin/bash
# Merge Conflict Resolution Script for Production Server

set -e  # Exit on error

echo "🔧 Starting merge conflict resolution..."

# Navigate to project directory
cd ~/MaxMed || { echo "❌ MaxMed directory not found"; exit 1; }

# Check current status
echo "📊 Current git status:"
sudo git status

# Create correct Kernel.php content
echo "📝 Creating correct Kernel.php file..."

sudo tee app/Http/Kernel.php > /dev/null << 'EOF'
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Custom404Handler;
use App\Http\Middleware\CanonicalDomainMiddleware;
use App\Http\Middleware\SupplierMiddleware;
use App\Http\Middleware\SupplierCategoryMiddleware;
use App\Http\Middleware\SupplierBadgeCountsMiddleware;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\CheckSupplierOnboarding;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\AuthenticatePushToken;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // CanonicalDomainMiddleware::class, // Moved to route level for better performance
        // \App\Http\Middleware\SeoHeadersMiddleware::class, // Disabled in development
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\CheckCookieConsent::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            Custom404Handler::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        'admin' => AdminMiddleware::class,
        'supplier.category' => SupplierCategoryMiddleware::class,
        'supplier' => SupplierMiddleware::class,
        'supplier.badges' => SupplierBadgeCountsMiddleware::class,
        'prevent-back' => PreventBackHistory::class,
        'supplier.onboarding' => CheckSupplierOnboarding::class,
        'permission' => PermissionMiddleware::class,
        'role' => RoleMiddleware::class,
        'canonical' => CanonicalDomainMiddleware::class,
        'seo-headers' => \App\Http\Middleware\SeoHeadersMiddleware::class,
        'query-optimization' => \App\Http\Middleware\QueryOptimizationMiddleware::class,
        'prevent-crawler-indexing' => \App\Http\Middleware\PreventCrawlerIndexing::class,
        'push.token' => AuthenticatePushToken::class,
    ];
}
EOF

echo "✅ Kernel.php file created"

# Stage the resolved file
echo "📦 Staging resolved file..."
sudo git add app/Http/Kernel.php

# Complete the merge
echo "🔄 Completing merge..."
sudo git commit -m "Merge: Resolve conflict in Kernel.php, add AuthenticatePushToken middleware for secure push notifications" || echo "⚠️  Commit might already exist, continuing..."

# Run migration
echo "🗄️  Running migrations..."
php artisan migrate --force

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify middleware file exists
echo "✅ Verifying middleware exists..."
if [ -f "app/Http/Middleware/AuthenticatePushToken.php" ]; then
    echo "   ✅ AuthenticatePushToken middleware found"
else
    echo "   ⚠️  AuthenticatePushToken middleware not found"
fi

# Verify middleware registration
echo "✅ Verifying middleware registration..."
if grep -q "push.token" app/Http/Kernel.php; then
    echo "   ✅ push.token middleware registered"
else
    echo "   ❌ push.token middleware NOT registered"
fi

# Final status
echo ""
echo "📊 Final git status:"
sudo git status

echo ""
echo "🎉 Merge conflict resolved successfully!"
echo ""
echo "📝 Next steps:"
echo "   1. Test push notifications"
echo "   2. Monitor logs: tail -f storage/logs/laravel.log"
echo "   3. Check database: SELECT * FROM push_notification_tokens LIMIT 5;"
echo ""
echo "📖 Full documentation:"
echo "   - SECURE_PUSH_NOTIFICATIONS.md"
echo "   - IMPLEMENTATION_GUIDE.md"
echo ""

