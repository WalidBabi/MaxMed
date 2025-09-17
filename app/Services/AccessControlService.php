<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class AccessControlService
{
    /**
     * Role hierarchy for permission inheritance
     */
    private static $roleHierarchy = [
        'super_admin' => 1,
        'system_admin' => 2,
        'business_admin' => 3,
        'operations_manager' => 4,
        'sales_manager' => 5,
        'purchasing_manager' => 5,
        'customer_service_manager' => 5,
        'marketing_manager' => 5,
        'content_manager' => 5,
        'inventory_manager' => 5,
        'financial_manager' => 5,
        'sales_rep' => 6,
        'purchasing_assistant' => 6,
        'purchasing_employee' => 6,
        'customer_service_rep' => 6,
        'supplier' => 7,
        'viewer' => 8,
    ];

    /**
     * Admin roles that have elevated access
     */
    private static $adminRoles = [
        'super_admin',
        'system_admin',
        'business_admin',
        'operations_manager',
        'purchasing_manager',
        'purchasing_assistant'
    ];

    /**
     * Check if user can access a specific permission
     */
    public static function canAccess(User $user, string $permission): bool
    {
        try {
            // 1. Super admin can do everything
            if ($user->hasRole('super_admin')) {
                return true;
            }

            // 2. Check direct permission
            if ($user->hasPermission($permission)) {
                return true;
            }

            // 3. Check role hierarchy inheritance
            if (self::hasRoleHierarchyAccess($user, $permission)) {
                return true;
            }

            // 4. Check admin override for admin permissions
            if (self::isAdminPermission($permission) && self::isAdminRole($user)) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('AccessControlService::canAccess - Error checking permission', [
                'user_id' => $user->id,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if user can access admin area
     */
    public static function canAccessAdmin(User $user): bool
    {
        return self::canAccess($user, 'admin.dashboard.access') || 
               self::canAccess($user, 'purchasing.dashboard.access') ||
               self::isAdminRole($user);
    }

    /**
     * Check if user can access CRM
     */
    public static function canAccessCrm(User $user): bool
    {
        return self::canAccess($user, 'crm.access');
    }

    /**
     * Check if user can access supplier area
     */
    public static function canAccessSupplier(User $user): bool
    {
        return self::canAccess($user, 'supplier.dashboard') || 
               $user->hasRole('supplier');
    }

    /**
     * Check if user has role hierarchy access
     * FIXED: Proper role hierarchy that only allows higher roles to inherit
     * permissions from lower roles, not the other way around.
     */
    private static function hasRoleHierarchyAccess(User $user, string $permission): bool
    {
        if (!$user->role) {
            return false;
        }

        // Super admin has access to everything
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // If user's role is not in the hierarchy, they don't inherit permissions
        if (!isset(self::$roleHierarchy[$user->role->name])) {
            return false;
        }

        $userRolePriority = self::$roleHierarchy[$user->role->name];
        
        // SECURITY FIX: Only allow higher priority roles (lower numbers) to inherit 
        // permissions from lower priority roles (higher numbers)
        // This means admins can access supplier functions, but suppliers can't access admin functions
        foreach (self::$roleHierarchy as $roleName => $priority) {
            if ($priority > $userRolePriority) { // Changed from < to >
                $role = Role::where('name', $roleName)->first();
                if ($role && $role->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
        
        /* ORIGINAL PROBLEMATIC CODE:
        if (!$user->role) {
            return false;
        }

        // If user's role is not in the hierarchy, they don't inherit permissions
        if (!isset(self::$roleHierarchy[$user->role->name])) {
            return false;
        }

        $userRolePriority = self::$roleHierarchy[$user->role->name];
        
        // Check if any higher priority role has this permission
        foreach (self::$roleHierarchy as $roleName => $priority) {
            if ($priority < $userRolePriority) {
                $role = Role::where('name', $roleName)->first();
                if ($role && $role->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
        */
    }

    /**
     * Check if permission is an admin permission
     */
    private static function isAdminPermission(string $permission): bool
    {
        return str_starts_with($permission, 'admin.') || 
               str_starts_with($permission, 'dashboard.') ||
               in_array($permission, ['analytics.view', 'reports.generate']);
    }

    /**
     * Check if user has admin role
     */
    private static function isAdminRole(User $user): bool
    {
        if (!$user->role) {
            return false;
        }

        return in_array($user->role->name, self::$adminRoles);
    }

    /**
     * Get user's effective permissions (including inherited)
     */
    public static function getEffectivePermissions(User $user): array
    {
        $permissions = [];

        if (!$user->role) {
            return $permissions;
        }

        // Get direct permissions
        $directPermissions = $user->getAllPermissions();
        foreach ($directPermissions as $permission) {
            $permissions[] = $permission->name;
        }

        // Get inherited permissions from role hierarchy
        $userRolePriority = self::$roleHierarchy[$user->role->name] ?? 999;
        
        foreach (self::$roleHierarchy as $roleName => $priority) {
            if ($priority < $userRolePriority) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $rolePermissions = $role->permissions()->where('is_active', true)->get();
                    foreach ($rolePermissions as $permission) {
                        if (!in_array($permission->name, $permissions)) {
                            $permissions[] = $permission->name;
                        }
                    }
                }
            }
        }

        return array_unique($permissions);
    }

    /**
     * Check if user can perform action on resource
     */
    public static function canPerformAction(User $user, string $action, string $resource = null): bool
    {
        $permission = $resource ? "{$resource}.{$action}" : $action;
        return self::canAccess($user, $permission);
    }

    /**
     * Get user's role display information
     */
    public static function getUserRoleInfo(User $user): array
    {
        if (!$user->role) {
            return [
                'name' => 'No Role',
                'display_name' => 'No Role Assigned',
                'is_admin' => false,
                'priority' => 999
            ];
        }

        return [
            'name' => $user->role->name,
            'display_name' => $user->role->display_name,
            'is_admin' => self::isAdminRole($user),
            'priority' => self::$roleHierarchy[$user->role->name] ?? 999
        ];
    }

    /**
     * Log access attempt for security auditing
     */
    public static function logAccessAttempt(User $user, string $permission, bool $granted, string $context = ''): void
    {
        Log::info('Access Control - Permission Check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role?->name,
            'permission' => $permission,
            'granted' => $granted,
            'context' => $context,
            'timestamp' => now()->toISOString()
        ]);
    }
}
