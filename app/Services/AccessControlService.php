<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
        'admin',
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
        // Super admin has access to everything
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Gather all user's role names (many-to-many + legacy single role)
        $userRoleNames = [];
        try {
            if (Schema::hasTable('role_user')) {
                $userRoleNames = $user->roles()->pluck('name')->toArray();
            }
        } catch (\Throwable $e) {
            // ignore and fallback to legacy role
        }
        if ($user->role) {
            $userRoleNames[] = $user->role->name;
        }
        $userRoleNames = array_unique($userRoleNames);
        if (empty($userRoleNames)) {
            return false;
        }

        // Find best (highest) priority among user's roles
        $userPriorities = [];
        foreach ($userRoleNames as $name) {
            if (isset(self::$roleHierarchy[$name])) {
                $userPriorities[] = self::$roleHierarchy[$name];
            }
        }
        if (empty($userPriorities)) {
            return false;
        }
        $bestPriority = min($userPriorities);

        // Allow inheritance from lower-priority roles only
        foreach (self::$roleHierarchy as $roleName => $priority) {
            if ($priority > $bestPriority) {
                $role = Role::where('name', $roleName)->first();
                if ($role && $role->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
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
        // Check via many-to-many roles
        try {
            if (Schema::hasTable('role_user') && $user->roles()->whereIn('name', self::$adminRoles)->exists()) {
                return true;
            }
        } catch (\Throwable $e) {
            // ignore and fallback to legacy
        }

        // Legacy fallback: single role relation
        if ($user->role) {
            return in_array($user->role->name, self::$adminRoles);
        }

        return false;
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
        $names = [];
        $displayNames = [];
        try {
            if (Schema::hasTable('role_user')) {
                $names = $user->roles()->pluck('name')->toArray();
                $displayNames = $user->roles()->pluck('display_name')->toArray();
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // include legacy single role if present
        if ($user->role) {
            $names[] = $user->role->name;
            $displayNames[] = $user->role->display_name;
        }

        if (empty($names)) {
            return [
                'name' => 'No Role',
                'display_name' => 'No Role Assigned',
                'is_admin' => false,
                'priority' => 999
            ];
        }

        // Determine best priority among roles
        $priorities = array_map(function ($n) {
            return self::$roleHierarchy[$n] ?? 999;
        }, $names);
        $bestPriority = min($priorities);

        return [
            'name' => implode(',', array_unique($names)),
            'display_name' => implode(',', array_unique(array_filter($displayNames))),
            'is_admin' => self::isAdminRole($user),
            'priority' => $bestPriority
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
            'user_roles' => (function () use ($user) {
                try {
                    return Schema::hasTable('role_user') ? $user->roles()->pluck('name') : collect();
                } catch (\Throwable $e) {
                    return collect();
                }
            })(),
            'permission' => $permission,
            'granted' => $granted,
            'context' => $context,
            'timestamp' => now()->toISOString()
        ]);
    }
}
