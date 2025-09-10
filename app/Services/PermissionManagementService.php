<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PermissionManagementService
{
    /**
     * Create a new permission dynamically
     */
    public function createPermission(array $data): Permission
    {
        $permission = Permission::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? 'system',
            'is_active' => $data['is_active'] ?? true,
        ]);

        Log::info('Permission created dynamically', [
            'permission_id' => $permission->id,
            'permission_name' => $permission->name,
            'created_by' => auth()->id(),
        ]);

        return $permission;
    }

    /**
     * Update an existing permission
     */
    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update([
            'display_name' => $data['display_name'] ?? $permission->display_name,
            'description' => $data['description'] ?? $permission->description,
            'category' => $data['category'] ?? $permission->category,
            'is_active' => $data['is_active'] ?? $permission->is_active,
        ]);

        Log::info('Permission updated dynamically', [
            'permission_id' => $permission->id,
            'permission_name' => $permission->name,
            'updated_by' => auth()->id(),
        ]);

        return $permission;
    }

    /**
     * Delete a permission (soft delete by deactivating)
     */
    public function deletePermission(Permission $permission): bool
    {
        // Don't actually delete, just deactivate to maintain referential integrity
        $permission->update(['is_active' => false]);

        Log::info('Permission deactivated', [
            'permission_id' => $permission->id,
            'permission_name' => $permission->name,
            'deactivated_by' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Generate permissions for a controller automatically
     */
    public function generateControllerPermissions(string $controllerName, array $actions = null): array
    {
        $actions = $actions ?? ['view', 'create', 'edit', 'delete'];
        $permissions = [];
        $category = Str::slug($controllerName);

        foreach ($actions as $action) {
            $permissionName = "{$category}.{$action}";
            $displayName = ucfirst($category) . ' ' . ucfirst($action);
            
            $permission = Permission::updateOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => $displayName,
                    'description' => "Permission to {$action} {$category}",
                    'category' => $category,
                    'is_active' => true,
                ]
            );

            $permissions[] = $permission;
        }

        return $permissions;
    }

    /**
     * Generate middleware code for a controller
     */
    public function generateMiddlewareCode(string $controllerName, array $actions = null): string
    {
        $actions = $actions ?? ['view', 'create', 'edit', 'delete'];
        $category = Str::slug($controllerName);
        $code = "public function __construct()\n{\n";
        $code .= "    \$this->middleware('auth');\n";

        foreach ($actions as $action) {
            $permissionName = "{$category}.{$action}";
            $method = $this->getMethodForAction($action);
            $code .= "    \$this->middleware('permission:{$permissionName}')->only(['{$method}']);\n";
        }

        $code .= "}";
        return $code;
    }

    /**
     * Get method name for action
     */
    private function getMethodForAction(string $action): string
    {
        $mapping = [
            'view' => 'index,show',
            'create' => 'create,store',
            'edit' => 'edit,update',
            'delete' => 'destroy',
        ];

        return $mapping[$action] ?? $action;
    }

    /**
     * Sync permissions with routes automatically
     */
    public function syncPermissionsWithRoutes(): array
    {
        $routes = app('router')->getRoutes();
        $permissions = [];
        $created = 0;
        $updated = 0;

        foreach ($routes as $route) {
            $middleware = $route->gatherMiddleware();
            $permissionMiddleware = collect($middleware)->filter(function ($m) {
                return is_string($m) && str_starts_with($m, 'permission:');
            });

            foreach ($permissionMiddleware as $middleware) {
                $permissionName = str_replace('permission:', '', $middleware);
                
                if (!Permission::where('name', $permissionName)->exists()) {
                    $parts = explode('.', $permissionName);
                    $category = $parts[0] ?? 'system';
                    $action = $parts[1] ?? 'access';
                    
                    Permission::create([
                        'name' => $permissionName,
                        'display_name' => ucfirst($category) . ' ' . ucfirst($action),
                        'description' => "Auto-generated permission for {$permissionName}",
                        'category' => $category,
                        'is_active' => true,
                    ]);
                    $created++;
                } else {
                    $updated++;
                }
                
                $permissions[] = $permissionName;
            }
        }

        return [
            'permissions' => $permissions,
            'created' => $created,
            'updated' => $updated,
        ];
    }

    /**
     * Get all permission categories
     */
    public function getCategories(): array
    {
        return Permission::distinct('category')
            ->where('is_active', true)
            ->pluck('category')
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Get permissions by category
     */
    public function getPermissionsByCategory(string $category = null): array
    {
        $query = Permission::where('is_active', true);
        
        if ($category) {
            $query->where('category', $category);
        }
        
        return $query->orderBy('name')->get()->groupBy('category')->toArray();
    }

    /**
     * Assign permission to role
     */
    public function assignPermissionToRole(Role $role, string $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            return false;
        }

        $role->permissions()->syncWithoutDetaching([$permission->id]);
        
        Log::info('Permission assigned to role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
            'assigned_by' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Remove permission from role
     */
    public function removePermissionFromRole(Role $role, string $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            return false;
        }

        $role->permissions()->detach($permission->id);
        
        Log::info('Permission removed from role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
            'removed_by' => auth()->id(),
        ]);

        return true;
    }
}
