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
        CanonicalDomainMiddleware::class,
        \App\Http\Middleware\SeoHeadersMiddleware::class,
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
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => AdminMiddleware::class,
        'supplier.category' => SupplierCategoryMiddleware::class,
        'supplier' => SupplierMiddleware::class,
        'supplier.badges' => SupplierBadgeCountsMiddleware::class,
        'prevent-back' => PreventBackHistory::class,
        'supplier.onboarding' => CheckSupplierOnboarding::class,
        'permission' => PermissionMiddleware::class,
        'role' => RoleMiddleware::class,
    ];
} 