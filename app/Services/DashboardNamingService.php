<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class DashboardNamingService
{
    /**
     * Get the appropriate dashboard name based on user role and permissions
     */
    public static function getDashboardName(string $dashboardType): string
    {
        $user = Auth::user();
        
        if (!$user) {
            return self::getDefaultName($dashboardType);
        }

        $roleName = $user->role?->name ?? '';
        $displayName = $user->role?->display_name ?? '';

        switch ($dashboardType) {
            case 'admin':
                return self::getAdminDashboardName($roleName, $displayName, $user);
            case 'crm':
                return self::getCrmDashboardName($roleName, $displayName, $user);
            case 'supplier':
                return self::getSupplierDashboardName($roleName, $displayName, $user);
            default:
                return self::getDefaultName($dashboardType);
        }
    }

    /**
     * Get admin dashboard name based on role
     */
    private static function getAdminDashboardName(string $roleName, string $displayName, $user): string
    {
        // Super admin gets full admin access
        if ($user->hasRole('super_admin')) {
            return 'Admin';
        }

        // Role-specific naming
        switch ($roleName) {
            case 'one-task-role-create-leads':
                return 'Dashboard';
            case 'sales_rep':
                return 'Sales';
            case 'purchasing_crm_assistant':
                return 'Purchasing';
            case 'customer_service':
                return 'Support';
            case 'inventory_manager':
                return 'Inventory';
            case 'accountant':
                return 'Finance';
            default:
                // Use display name if available, otherwise generic
                if ($displayName) {
                    return $displayName;
                }
                return 'Dashboard';
        }
    }

    /**
     * Get CRM dashboard name based on role
     */
    private static function getCrmDashboardName(string $roleName, string $displayName, $user): string
    {
        // Super admin gets full CRM access
        if ($user->hasRole('super_admin')) {
            return 'CRM';
        }

        // Role-specific naming
        switch ($roleName) {
            case 'one-task-role-create-leads':
                return 'CRM';
            case 'sales_rep':
                return 'Sales CRM';
            case 'purchasing_crm_assistant':
                return 'Purchasing CRM';
            case 'customer_service':
                return 'Support CRM';
            default:
                // Use display name if available, otherwise generic
                if ($displayName) {
                    return $displayName . ' CRM';
                }
                return 'CRM';
        }
    }

    /**
     * Get supplier dashboard name based on role
     */
    private static function getSupplierDashboardName(string $roleName, string $displayName, $user): string
    {
        // Super admin gets full supplier access
        if ($user->hasRole('super_admin')) {
            return 'Supplier';
        }

        // Role-specific naming
        switch ($roleName) {
            case 'supplier':
                return 'My Portal';
            case 'supplier_admin':
                return 'Supplier Admin';
            default:
                return 'Supplier';
        }
    }

    /**
     * Get default name if no specific logic applies
     */
    private static function getDefaultName(string $dashboardType): string
    {
        switch ($dashboardType) {
            case 'admin':
                return 'Dashboard';
            case 'crm':
                return 'CRM';
            case 'supplier':
                return 'Supplier';
            default:
                return 'Dashboard';
        }
    }

    /**
     * Get portal header name for sidebar display
     */
    public static function getPortalHeaderName(string $portalType): string
    {
        $user = Auth::user();
        
        if (!$user) {
            return self::getDefaultPortalName($portalType);
        }

        $roleName = $user->role?->name ?? '';
        $displayName = $user->role?->display_name ?? '';

        switch ($portalType) {
            case 'admin':
                return self::getAdminPortalHeaderName($roleName, $displayName, $user);
            case 'crm':
                return self::getCrmPortalHeaderName($roleName, $displayName, $user);
            case 'supplier':
                return self::getSupplierPortalHeaderName($roleName, $displayName, $user);
            default:
                return self::getDefaultPortalName($portalType);
        }
    }

    /**
     * Get admin portal header name based on role
     */
    private static function getAdminPortalHeaderName(string $roleName, string $displayName, $user): string
    {
        // Super admin gets full admin access
        if ($user->hasRole('super_admin')) {
            return 'Admin';
        }

        // Role-specific naming
        switch ($roleName) {
            case 'one-task-role-create-leads':
                return 'Leads';
            case 'sales_rep':
                return 'Sales';
            case 'purchasing_crm_assistant':
                return 'Purchasing';
            case 'customer_service':
                return 'Support';
            case 'inventory_manager':
                return 'Inventory';
            case 'accountant':
                return 'Finance';
            default:
                // Use display name if available, otherwise generic
                if ($displayName) {
                    return $displayName;
                }
                return 'Admin';
        }
    }

    /**
     * Get CRM portal header name based on role
     */
    private static function getCrmPortalHeaderName(string $roleName, string $displayName, $user): string
    {
        // Super admin gets full CRM access
        if ($user->hasRole('super_admin')) {
            return 'CRM';
        }

        // Role-specific naming
        switch ($roleName) {
            case 'one-task-role-create-leads':
                return 'Leads';
            case 'sales_rep':
                return 'Sales CRM';
            case 'purchasing_crm_assistant':
                return 'Purchasing CRM';
            case 'customer_service':
                return 'Support CRM';
            default:
                // Use display name if available, otherwise generic
                if ($displayName) {
                    return $displayName . ' CRM';
                }
                return 'CRM';
        }
    }

    /**
     * Get supplier portal header name based on role
     */
    private static function getSupplierPortalHeaderName(string $roleName, string $displayName, $user): string
    {
        // Super admin gets full supplier access
        if ($user->hasRole('super_admin')) {
            return 'Supplier';
        }

        // Role-specific naming
        switch ($roleName) {
            case 'supplier':
                return 'My Portal';
            case 'supplier_admin':
                return 'Supplier Admin';
            default:
                return 'Supplier';
        }
    }

    /**
     * Get default portal name if no specific logic applies
     */
    private static function getDefaultPortalName(string $portalType): string
    {
        switch ($portalType) {
            case 'admin':
                return 'Admin';
            case 'crm':
                return 'CRM';
            case 'supplier':
                return 'Supplier';
            default:
                return 'Portal';
        }
    }

    /**
     * Get all available dashboard names for the current user
     */
    public static function getAvailableDashboards(): array
    {
        $user = Auth::user();
        $dashboards = [];

        if (!$user) {
            return $dashboards;
        }

        // Admin Dashboard - Only for super admin
        if ($user->hasRole('super_admin') || $user->isAdmin()) {
            $dashboards[] = [
                'name' => self::getDashboardName('admin'),
                'url' => \route('admin.dashboard'),
                'type' => 'admin'
            ];
        }

        // CRM Dashboard
        if ($user->hasPermission('crm.access') || $user->isAdmin()) {
            $dashboards[] = [
                'name' => self::getDashboardName('crm'),
                'url' => \route('crm.dashboard'),
                'type' => 'crm'
            ];
        }

        // Supplier Dashboard
        if ($user->hasPermission('supplier.products.view') || $user->isAdmin()) {
            $dashboards[] = [
                'name' => self::getDashboardName('supplier'),
                'url' => \route('supplier.dashboard'),
                'type' => 'supplier'
            ];
        }

        return $dashboards;
    }
}
