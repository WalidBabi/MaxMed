<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class UserRedirectService
{
    /**
     * Get the appropriate redirect URL for a user based on their permissions
     */
    public static function getRedirectUrl(User $user = null): string
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return route('login');
        }

        // Permission-based redirect logic - check permissions in priority order
        $redirectRoutes = [
            'dashboard.view' => 'admin.dashboard',
            'purchasing.dashboard.access' => 'admin.dashboard',
            'purchase_orders.view' => 'admin.purchase-orders.index',
            'quotations.view' => 'admin.quotes.index',
            'orders.view' => 'admin.orders.index',
            'products.view' => 'admin.products.index',
            'crm.leads.view' => 'crm.dashboard',
            'suppliers.view' => 'admin.supplier-profiles.index',
            'supplier.dashboard' => 'supplier.dashboard',
            'news.manage' => 'admin.news.index',
            'feedback.view' => 'admin.feedback.index',
            'analytics.view' => 'admin.analytics.dashboard',
            'users.view' => 'admin.users.index',
            'roles.view' => 'admin.roles.index',
        ];

        foreach ($redirectRoutes as $permission => $route) {
            if (AccessControlService::canAccess($user, $permission)) {
                try {
                    return route($route);
                } catch (\Exception $e) {
                    // Route doesn't exist, continue to next
                    continue;
                }
            }
        }

        // Fallback: try to find any accessible route
        $fallbackRoutes = [
            'admin.purchase-orders.index',
            'admin.quotes.index', 
            'admin.orders.index',
            'admin.products.index',
            'crm.dashboard',
            'supplier.dashboard',
            'admin.news.index',
            'admin.feedback.index'
        ];

        foreach ($fallbackRoutes as $route) {
            try {
                // Check if user can access this route by checking the route name pattern
                if (self::canAccessRoute($user, $route)) {
                    return route($route);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Ultimate fallback
        return route('login');
    }

    /**
     * Check if user can access a specific route based on route name patterns
     */
    private static function canAccessRoute(User $user, string $routeName): bool
    {
        $routePatterns = [
            // Admin Dashboard
            'admin.dashboard' => ['dashboard.view', 'dashboard.admin'],
            
            // Sales & Orders
            'admin.quotes.index' => ['quotations.view'],
            'admin.orders.index' => ['orders.view'],
            'admin.invoices.index' => ['invoices.view'],
            
            // Purchasing
            'admin.purchase-orders.index' => ['purchase_orders.view'],
            
            // CRM
            'crm.dashboard' => ['crm.leads.view', 'crm.access'],
            
            // Products & Catalog
            'admin.products.index' => ['products.view'],
            'admin.categories.index' => ['categories.view'],
            'admin.brands.index' => ['brands.view'],
            
            // Suppliers
            'admin.supplier-profiles.index' => ['suppliers.view'],
            'admin.supplier-invitations.index' => ['suppliers.view'],
            'supplier.dashboard' => ['supplier.dashboard'],
            
            // Content Management
            'admin.news.index' => ['news.manage'],
            'admin.feedback.index' => ['feedback.view'],
            
            // Analytics
            'admin.analytics.dashboard' => ['analytics.view', 'dashboard.analytics'],
            
            // User Management
            'admin.users.index' => ['users.view'],
            'admin.roles.index' => ['roles.view'],
        ];

        if (isset($routePatterns[$routeName])) {
            foreach ($routePatterns[$routeName] as $permission) {
                if (AccessControlService::canAccess($user, $permission)) {
                    return true;
                }
            }
        }

        // Special case: Super admin can access everything
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return false;
    }

    /**
     * Get the appropriate page title for a user's landing page
     */
    public static function getLandingPageTitle(User $user = null): string
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return 'Login';
        }

        // Permission-based titles
        $permissionTitles = [
            'dashboard.view' => 'Dashboard',
            'purchasing.dashboard.access' => 'Purchasing Dashboard',
            'purchase_orders.view' => 'Purchase Orders',
            'quotations.view' => 'Quotes',
            'orders.view' => 'Sales Orders',
            'crm.leads.view' => 'CRM Dashboard',
            'suppliers.view' => 'Supplier Management',
            'products.view' => 'Product Catalog',
            'supplier.dashboard' => 'Supplier Dashboard',
            'news.manage' => 'Content Management',
            'feedback.view' => 'Feedback',
            'analytics.view' => 'Analytics',
            'users.view' => 'User Management',
            'roles.view' => 'Role Management',
        ];

        foreach ($permissionTitles as $permission => $title) {
            if (AccessControlService::canAccess($user, $permission)) {
                return $title;
            }
        }

        return 'Welcome';
    }

}
