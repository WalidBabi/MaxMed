<?php

namespace App\Providers;

use App\Services\FeatureAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Custom Blade directive for feature access
        Blade::if('canAccessFeature', function (string $feature) {
            if (!Auth::check()) {
                return false;
            }
            
            return FeatureAccessService::canAccess(Auth::user(), $feature);
        });
        
        // Custom Blade directive for multiple features (OR logic)
        Blade::if('canAccessAnyFeature', function (...$features) {
            if (!Auth::check()) {
                return false;
            }
            
            $user = Auth::user();
            foreach ($features as $feature) {
                if (FeatureAccessService::canAccess($user, $feature)) {
                    return true;
                }
            }
            
            return false;
        });
    }
}
