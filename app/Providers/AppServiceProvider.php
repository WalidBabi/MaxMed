<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Share navigation categories with all views
        View::composer('*', function ($view) {
            $navCategories = Category::whereNull('parent_id')
                ->with(['subcategories' => function($query) {
                    $query->orderBy('name', 'asc');
                }])
                ->orderBy('name', 'asc')
                ->get();
            
            $view->with('navCategories', $navCategories);
        });
        
        // Add this debugging code
        if (app()->environment('production')) {
            app()->singleton('request-logger', function() {
                return new class {
                    public function logRequest(Request $request) {
                        Log::info('Request details', [
                            'method' => $request->method(),
                            'url' => $request->fullUrl(),
                            'has_csrf' => $request->has('_token'),
                            'headers' => $request->headers->all()
                        ]);
                    }
                };
            });
            
            app()->terminating(function() {
                Log::info('Response status', [
                    'status' => http_response_code()
                ]);
            });
        }
    }
}
