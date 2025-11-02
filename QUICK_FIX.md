# Quick Fix for Merge Conflict on Production Server

## Run These Commands

Copy and paste these commands on your production server:

```bash
cd ~/MaxMed

# Download the fix script
curl -O https://raw.githubusercontent.com/WalidBabi/MaxMed/main/fix-merge-conflict.sh

# OR if you prefer manual steps, continue below:

# ==========================================
# MANUAL FIX (if script doesn't work)
# ==========================================

# 1. Create the correct Kernel.php file
sudo nano app/Http/Kernel.php

# Delete ALL content and paste the correct version below,
# then save: Ctrl+O, Enter, Ctrl+X

# 2. Stage the resolved file
sudo git add app/Http/Kernel.php

# 3. Complete the merge
sudo git commit -m "Merge: Resolve conflict in Kernel.php"

# 4. Run migration
php artisan migrate --force

# 5. Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# 6. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Check status
sudo git status
```

## Correct Kernel.php Content

Copy this entire file:

```php
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
    protected $middleware = [
        TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

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
```

## Verify Success

Run these checks:

```bash
# 1. Git should show clean tree
sudo git status

# 2. Middleware file should exist
ls -la app/Http/Middleware/AuthenticatePushToken.php

# 3. Middleware should be registered
grep -n "push.token" app/Http/Kernel.php

# Expected output: line 99: 'push.token' => AuthenticatePushToken::class,
```

## Common Issues

### "fatal: Exiting because of an unresolved conflict"
```bash
# Make sure you added the file
sudo git add app/Http/Kernel.php
sudo git commit
```

### "Class AuthenticatePushToken not found"
```bash
# Clear and rebuild
composer dump-autoload
php artisan config:clear
php artisan config:cache
```

### Migration says table exists
That's fine - it means migration already ran.

## Done!

After these steps, your server should be updated with the secure push notification system.

For full details, see: `SECURE_PUSH_NOTIFICATIONS.md`

