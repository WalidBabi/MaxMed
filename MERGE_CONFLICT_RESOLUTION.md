# Merge Conflict Resolution Guide

## Issue

You have a merge conflict in `app/Http/Kernel.php` on the production server when pulling from the GitHub repository.

## Resolution Steps

### 1. Check the Conflict

```bash
cd ~/MaxMed
sudo git status
```

You'll see:
```
Unmerged paths:
  both modified:   app/Http/Kernel.php
```

### 2. View the Conflict

```bash
sudo nano app/Http/Kernel.php
```

Look for conflict markers:
```
<<<<<<< HEAD
// Your local changes
=======
// Incoming changes
>>>>>>> branch-name
```

### 3. Resolve the Conflict

The correct version should have both:
1. The import: `use App\Http\Middleware\AuthenticatePushToken;`
2. The middleware registration: `'push.token' => AuthenticatePushToken::class,`

**Full correct content:**

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
```

### 4. Mark Conflict as Resolved

```bash
sudo git add app/Http/Kernel.php
```

### 5. Complete the Merge

```bash
sudo git commit -m "Merge: Resolve conflict in Kernel.php, add AuthenticatePushToken middleware"
```

### 6. Verify Everything is Correct

```bash
sudo git status
```

Should show:
```
On branch main
Your branch is ahead of 'origin/main' by XXX commits.
nothing to commit, working tree clean
```

### 7. Run Migration (IMPORTANT!)

```bash
php artisan migrate
```

This will create the `push_notification_tokens` table.

### 8. Clear Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### 9. Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 10. Verify the Middleware File Exists

```bash
ls -la app/Http/Middleware/AuthenticatePushToken.php
```

Should show the file exists. If not, check that it was committed.

## Quick Resolution Script

```bash
#!/bin/bash

# Navigate to project
cd ~/MaxMed

# Check conflict
echo "Checking merge status..."
sudo git status

# If there's a conflict in Kernel.php, resolve it
echo "Resolving conflict in app/Http/Kernel.php..."
# Edit the file and remove conflict markers (use the full correct version above)
sudo nano app/Http/Kernel.php

# Stage the resolved file
sudo git add app/Http/Kernel.php

# Complete merge
echo "Completing merge..."
sudo git commit -m "Merge: Resolve conflict in Kernel.php, add AuthenticatePushToken middleware"

# Run migration
echo "Running migrations..."
php artisan migrate

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Optimize
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Done!"
```

## Alternative: Accept Theirs

If you want to accept the GitHub version completely:

```bash
cd ~/MaxMed
sudo git checkout --theirs app/Http/Kernel.php
sudo git add app/Http/Kernel.php
sudo git commit -m "Merge: Accept remote Kernel.php"
```

**BUT** - You'll need to add the AuthenticatePushToken middleware manually after this.

## Alternative: Accept Ours

If you want to keep your local version:

```bash
cd ~/MaxMed
sudo git checkout --ours app/Http/Kernel.php
sudo git add app/Http/Kernel.php
sudo git commit -m "Merge: Keep local Kernel.php"
```

## Verify Middleware Registration

After resolving, verify the middleware is registered:

```bash
php artisan route:list | grep push.token
```

Or check the Kernel.php file:

```bash
grep -n "push.token" app/Http/Kernel.php
```

Should show line 99: `'push.token' => AuthenticatePushToken::class,`

## Check for Other Conflicts

```bash
sudo git diff --check
```

Should return nothing if no conflicts remain.

## Troubleshooting

### Issue: Git says "fatal: Exiting because of an unresolved conflict"

**Solution:** Make sure you ran `git add app/Http/Kernel.php` after resolving.

### Issue: "Class 'App\Http\Middleware\AuthenticatePushToken' not found"

**Solution:** 
1. Verify file exists: `ls -la app/Http/Middleware/AuthenticatePushToken.php`
2. Run `composer dump-autoload`
3. Clear caches as above

### Issue: Migration fails

**Solution:**
```bash
php artisan migrate:status
# If table already exists, it's okay
# If migration is pending, run: php artisan migrate
```

## Success Indicators

✅ `git status` shows "working tree clean"  
✅ `ls app/Http/Middleware/AuthenticatePushToken.php` shows file  
✅ `grep "push.token" app/Http/Kernel.php` shows the line  
✅ Migration completes successfully  
✅ No errors in logs  

## Next Steps

After resolving the conflict:
1. Test push notifications
2. Monitor logs: `tail -f storage/logs/laravel.log`
3. Check database: `SELECT * FROM push_notification_tokens LIMIT 5;`
4. Read the full documentation: `SECURE_PUSH_NOTIFICATIONS.md`

