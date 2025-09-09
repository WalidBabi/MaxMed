<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register dynamic gates for all permissions
        $this->registerPermissionGates();

        // Register super admin gate
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super_admin')) {
                return true; // Super admin can do everything
            }
        });

        // Register admin override for certain actions
        Gate::define('admin-override', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'system_admin', 'business_admin']);
        });

        // Register role-specific gates
        Gate::define('access-admin-area', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'system_admin', 'business_admin', 'operations_manager', 'purchasing_crm_assistant']) 
                   || $user->hasPermission('dashboard.view');
        });

        Gate::define('access-supplier-area', function (User $user) {
            return $user->hasRole('supplier') || $user->hasPermission('admin-override');
        });

        Gate::define('access-crm', function (User $user) {
            return $user->hasAnyRole(['sales_manager', 'sales_rep', 'customer_service_manager', 'customer_service_rep', 'purchasing_crm_assistant']) 
                   || $user->hasPermission('crm.access');
        });

        Gate::define('manage-users', function (User $user) {
            return $user->hasAnyPermission(['users.create', 'users.edit', 'users.delete']);
        });

        Gate::define('manage-roles', function (User $user) {
            return $user->hasAnyPermission(['roles.create', 'roles.edit', 'roles.delete']);
        });

        Gate::define('view-sensitive-data', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'business_admin', 'financial_manager']) 
                   || $user->hasPermission('customers.view_sensitive');
        });

        Gate::define('manage-financials', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'business_admin', 'financial_manager']) 
                   || $user->hasAnyPermission(['purchase_orders.view_financials', 'invoices.manage_payments']);
        });

        // Define resource-specific gates
        $this->registerResourceGates();
    }

    /**
     * Register dynamic gates for all permissions
     */
    protected function registerPermissionGates(): void
    {
        try {
            $permissions = Permission::where('is_active', true)->get();

            foreach ($permissions as $permission) {
                Gate::define($permission->name, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->name);
                });
            }
        } catch (\Exception $e) {
            // Handle case where permissions table doesn't exist yet (during migration)
        }
    }

    /**
     * Register resource-specific authorization gates
     */
    protected function registerResourceGates(): void
    {
        // Product management gates
        Gate::define('view-product', function (User $user, $product = null) {
            if ($user->hasPermission('products.view')) {
                return true;
            }

            // Suppliers can only view their own products
            if ($user->hasRole('supplier') && $product) {
                return $product->supplier_id === $user->id;
            }

            return false;
        });

        Gate::define('edit-product', function (User $user, $product = null) {
            if ($user->hasPermission('products.edit')) {
                return true;
            }

            // Suppliers can only edit their own products
            if ($user->hasRole('supplier') && $product && $user->hasPermission('supplier.products.edit')) {
                return $product->supplier_id === $user->id;
            }

            return false;
        });

        // Order management gates
        Gate::define('view-order', function (User $user, $order = null) {
            if ($user->hasPermission('orders.view_all')) {
                return true;
            }

            if ($user->hasPermission('orders.view_own') && $order) {
                return $order->user_id === $user->id;
            }

            // Suppliers can view orders for their products
            if ($user->hasRole('supplier') && $order && $user->hasPermission('supplier.orders.view')) {
                return $order->items()->whereHas('product', function ($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })->exists();
            }

            return false;
        });

        Gate::define('manage-order', function (User $user, $order = null) {
            if ($user->hasAnyPermission(['orders.edit', 'orders.manage_status'])) {
                return true;
            }

            // Suppliers can manage orders for their products
            if ($user->hasRole('supplier') && $order && $user->hasPermission('supplier.orders.manage')) {
                return $order->items()->whereHas('product', function ($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })->exists();
            }

            return false;
        });

        // Customer data access gates
        Gate::define('view-customer', function (User $user, $customer = null) {
            return $user->hasPermission('customers.view');
        });

        Gate::define('view-customer-sensitive', function (User $user, $customer = null) {
            return $user->hasPermission('customers.view_sensitive') || Gate::allows('view-sensitive-data');
        });

        // Supplier management gates
        Gate::define('manage-supplier-categories', function (User $user) {
            return $user->hasPermission('suppliers.manage_categories');
        });

        Gate::define('approve-supplier', function (User $user) {
            return $user->hasPermission('suppliers.approve');
        });

        // Financial gates
        Gate::define('view-financial-data', function (User $user) {
            return Gate::allows('manage-financials');
        });

        Gate::define('manage-pricing', function (User $user) {
            return $user->hasAnyPermission(['products.manage_pricing']);
        });

        // System administration gates
        Gate::define('access-system-settings', function (User $user) {
            return $user->hasPermission('system.settings');
        });

        Gate::define('view-system-logs', function (User $user) {
            return $user->hasPermission('system.logs');
        });

        Gate::define('perform-system-maintenance', function (User $user) {
            return $user->hasPermission('system.maintenance');
        });
    }
}
