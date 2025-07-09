<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load helper functions
        require_once app_path('Helpers/DateHelper.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Set default timezone for Carbon
        date_default_timezone_set('Asia/Dubai');
        
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
    }
}
