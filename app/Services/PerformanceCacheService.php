<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PerformanceCacheService
{
    /**
     * Cache frequently accessed data
     */
    public static function cacheFrequentData()
    {
        // Cache user permissions
        if (auth()->check()) {
            $user = auth()->user();
            $cacheKey = "user_permissions_{$user->id}";
            
            if (!Cache::has($cacheKey)) {
                $permissions = $user->getAllPermissions();
                Cache::put($cacheKey, $permissions, 3600); // 1 hour
            }
        }

        // Cache navigation menus
        $menuCacheKey = 'navigation_menus';
        if (!Cache::has($menuCacheKey)) {
            // Cache menu structure
            $menus = [
                'admin' => self::getAdminMenu(),
                'crm' => self::getCrmMenu(),
                'supplier' => self::getSupplierMenu()
            ];
            Cache::put($menuCacheKey, $menus, 1800); // 30 minutes
        }
    }

    /**
     * Get cached user permissions
     */
    public static function getCachedUserPermissions($userId)
    {
        return Cache::get("user_permissions_{$userId}");
    }

    /**
     * Clear user-specific cache
     */
    public static function clearUserCache($userId)
    {
        Cache::forget("user_permissions_{$userId}");
    }

    /**
     * Cache database query results
     * Note: Do not pass closures that contain references to $this or other non-serializable objects
     */
    public static function cacheQuery($key, $callback, $ttl = 300)
    {
        // Check if callback is a closure and warn about potential serialization issues
        if ($callback instanceof \Closure) {
            \Log::warning('PerformanceCacheService::cacheQuery called with closure. This may cause serialization errors if the closure contains references to $this or other non-serializable objects.');
        }
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Get admin menu structure
     */
    private static function getAdminMenu()
    {
        return [
            'dashboard' => ['name' => 'Dashboard', 'route' => 'admin.dashboard'],
            'sales' => ['name' => 'Sales Management', 'route' => 'admin.quotes.index'],
            'purchasing' => ['name' => 'Purchase Orders', 'route' => 'admin.purchase-orders.index'],
            'products' => ['name' => 'Products', 'route' => 'admin.products.index'],
            'suppliers' => ['name' => 'Suppliers', 'route' => 'admin.suppliers.index'],
        ];
    }

    /**
     * Get CRM menu structure
     */
    private static function getCrmMenu()
    {
        return [
            'dashboard' => ['name' => 'Dashboard', 'route' => 'crm.dashboard'],
            'leads' => ['name' => 'Sales Pipeline', 'route' => 'crm.leads.index'],
            'customers' => ['name' => 'Customer Management', 'route' => 'crm.customers.index'],
            'marketing' => ['name' => 'Marketing', 'route' => 'crm.marketing.dashboard'],
        ];
    }

    /**
     * Get supplier menu structure
     */
    private static function getSupplierMenu()
    {
        return [
            'dashboard' => ['name' => 'Dashboard', 'route' => 'supplier.dashboard'],
            'products' => ['name' => 'My Products', 'route' => 'supplier.products.index'],
            'orders' => ['name' => 'Orders', 'route' => 'supplier.orders.index'],
            'profile' => ['name' => 'Profile', 'route' => 'supplier.profile'],
        ];
    }

    /**
     * Optimize database connections
     */
    public static function optimizeDatabase()
    {
        // Set connection timeout
        config(['database.connections.mysql.options' => [
            \PDO::ATTR_TIMEOUT => 5,
            \PDO::ATTR_PERSISTENT => false,
        ]]);

        // Enable query caching if available
        if (config('database.default') === 'mysql') {
            DB::statement('SET SESSION query_cache_type = ON');
        }
    }
}
