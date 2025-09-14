<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PermissionEnforcementService
{
    /**
     * Test all permissions for a given user
     */
    public static function testUserPermissions(User $user): array
    {
        $results = [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role' => $user->role?->name,
            'permissions_tested' => 0,
            'permissions_passed' => 0,
            'permissions_failed' => 0,
            'details' => []
        ];

        $allPermissions = Permission::where('is_active', true)->get();
        
        foreach ($allPermissions as $permission) {
            $results['permissions_tested']++;
            
            $hasPermission = $user->hasPermission($permission->name);
            
            if ($hasPermission) {
                $results['permissions_passed']++;
            } else {
                $results['permissions_failed']++;
            }
            
            $results['details'][] = [
                'permission' => $permission->name,
                'display_name' => $permission->display_name,
                'category' => $permission->category,
                'has_permission' => $hasPermission,
                'expected' => self::getExpectedPermissionForRole($user->role, $permission->name)
            ];
        }
        
        return $results;
    }

    /**
     * Test permission enforcement across all controllers
     */
    public static function testControllerPermissionEnforcement(): array
    {
        $results = [
            'controllers_tested' => 0,
            'controllers_with_permissions' => 0,
            'controllers_without_permissions' => 0,
            'details' => []
        ];

        $controllerPath = app_path('Http/Controllers');
        $controllers = glob($controllerPath . '/**/*.php');
        
        foreach ($controllers as $controllerFile) {
            $controllerName = str_replace([$controllerPath . '/', '.php'], '', $controllerFile);
            $controllerClass = 'App\\Http\\Controllers\\' . str_replace('/', '\\', $controllerName);
            
            if (class_exists($controllerClass)) {
                $results['controllers_tested']++;
                
                // Check if controller has permission middleware in constructor
                $reflection = new \ReflectionClass($controllerClass);
                $constructor = $reflection->getConstructor();
                
                $hasPermissions = false;
                $permissionMethods = [];
                
                if ($constructor) {
                    $constructorCode = file_get_contents($controllerFile);
                    
                    // Look for permission middleware patterns
                    if (preg_match('/\$this->middleware\(\s*[\'"](permission:[^\'"]+)[\'"]/', $constructorCode, $matches)) {
                        $hasPermissions = true;
                        
                        // Extract all permission middleware calls
                        preg_match_all('/\$this->middleware\(\s*[\'"](permission:[^\'"]+)[\'"]/', $constructorCode, $allMatches);
                        $permissionMethods = array_unique($allMatches[1]);
                    }
                }
                
                if ($hasPermissions) {
                    $results['controllers_with_permissions']++;
                } else {
                    $results['controllers_without_permissions']++;
                }
                
                $results['details'][] = [
                    'controller' => $controllerClass,
                    'has_permissions' => $hasPermissions,
                    'permission_methods' => $permissionMethods
                ];
            }
        }
        
        return $results;
    }

    /**
     * Test route permission enforcement
     */
    public static function testRoutePermissionEnforcement(): array
    {
        $results = [
            'routes_tested' => 0,
            'routes_with_permissions' => 0,
            'routes_without_permissions' => 0,
            'details' => []
        ];

        $routes = \Route::getRoutes();
        
        foreach ($routes as $route) {
            if (strpos($route->uri(), 'admin') === 0) {
                $results['routes_tested']++;
                
                $middleware = $route->gatherMiddleware();
                $hasPermissionMiddleware = false;
                $permissions = [];
                
                foreach ($middleware as $mw) {
                    if (is_string($mw) && strpos($mw, 'permission:') === 0) {
                        $hasPermissionMiddleware = true;
                        $permissions[] = str_replace('permission:', '', $mw);
                    }
                }
                
                if ($hasPermissionMiddleware) {
                    $results['routes_with_permissions']++;
                } else {
                    $results['routes_without_permissions']++;
                }
                
                $results['details'][] = [
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'methods' => $route->methods(),
                    'has_permissions' => $hasPermissionMiddleware,
                    'permissions' => $permissions
                ];
            }
        }
        
        return $results;
    }

    /**
     * Test UI permission controls
     */
    public static function testUIPermissionControls(): array
    {
        $results = [
            'views_tested' => 0,
            'views_with_permissions' => 0,
            'views_without_permissions' => 0,
            'details' => []
        ];

        $viewPath = resource_path('views');
        $views = glob($viewPath . '/**/*.blade.php');
        
        foreach ($views as $viewFile) {
            $viewName = str_replace([$viewPath . '/', '.blade.php'], '', $viewFile);
            $results['views_tested']++;
            
            $content = file_get_contents($viewFile);
            $hasPermissionChecks = false;
            $permissionChecks = [];
            
            // Look for permission checks in blade templates
            if (preg_match_all('/@can\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
                $hasPermissionChecks = true;
                $permissionChecks = array_merge($permissionChecks, $matches[1]);
            }
            
            if (preg_match_all('/@cannot\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
                $hasPermissionChecks = true;
                $permissionChecks = array_merge($permissionChecks, $matches[1]);
            }
            
            if (preg_match_all('/hasPermission\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
                $hasPermissionChecks = true;
                $permissionChecks = array_merge($permissionChecks, $matches[1]);
            }
            
            if ($hasPermissionChecks) {
                $results['views_with_permissions']++;
            } else {
                $results['views_without_permissions']++;
            }
            
            $results['details'][] = [
                'view' => $viewName,
                'has_permissions' => $hasPermissionChecks,
                'permission_checks' => array_unique($permissionChecks)
            ];
        }
        
        return $results;
    }

    /**
     * Generate comprehensive permission audit report
     */
    public static function generateAuditReport(): array
    {
        Log::info('Starting comprehensive permission audit');
        
        $report = [
            'timestamp' => now()->toISOString(),
            'user_tests' => [],
            'controller_tests' => self::testControllerPermissionEnforcement(),
            'route_tests' => self::testRoutePermissionEnforcement(),
            'ui_tests' => self::testUIPermissionControls(),
            'recommendations' => []
        ];

        // Test permissions for each role
        $roles = Role::where('is_active', true)->get();
        foreach ($roles as $role) {
            $users = $role->users()->take(1)->get();
            if ($users->count() > 0) {
                $user = $users->first();
                $report['user_tests'][] = self::testUserPermissions($user);
            }
        }

        // Generate recommendations
        $report['recommendations'] = self::generateRecommendations($report);
        
        Log::info('Permission audit completed', ['report_summary' => [
            'controllers_without_permissions' => $report['controller_tests']['controllers_without_permissions'],
            'routes_without_permissions' => $report['route_tests']['routes_without_permissions'],
            'views_without_permissions' => $report['ui_tests']['views_without_permissions']
        ]]);
        
        return $report;
    }

    /**
     * Generate recommendations based on audit results
     */
    private static function generateRecommendations(array $report): array
    {
        $recommendations = [];
        
        if ($report['controller_tests']['controllers_without_permissions'] > 0) {
            $recommendations[] = [
                'type' => 'critical',
                'category' => 'controller_permissions',
                'message' => "{$report['controller_tests']['controllers_without_permissions']} controllers lack permission enforcement",
                'action' => 'Add permission middleware to all admin controllers'
            ];
        }
        
        if ($report['route_tests']['routes_without_permissions'] > 0) {
            $recommendations[] = [
                'type' => 'critical',
                'category' => 'route_permissions',
                'message' => "{$report['route_tests']['routes_without_permissions']} admin routes lack permission middleware",
                'action' => 'Add permission middleware to all admin routes'
            ];
        }
        
        if ($report['ui_tests']['views_without_permissions'] > 0) {
            $recommendations[] = [
                'type' => 'warning',
                'category' => 'ui_permissions',
                'message' => "{$report['ui_tests']['views_without_permissions']} views lack permission checks",
                'action' => 'Add permission checks to sensitive UI elements'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Get expected permission for a role (for testing)
     */
    private static function getExpectedPermissionForRole(?Role $role, string $permissionName): bool
    {
        if (!$role) return false;
        
        // Define expected permissions for each role
        $rolePermissions = [
            'super_admin' => true, // Super admin has all permissions
            'admin' => [
                'dashboard.view', 'dashboard.analytics', 'dashboard.admin',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                'products.view', 'products.create', 'products.edit', 'products.delete',
                'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
                'orders.view', 'orders.view_all', 'orders.edit', 'orders.delete',
                'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
                'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
                'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete',
                'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
                'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit',
                'analytics.view', 'reports.generate', 'reports.export',
                'system.settings', 'system.logs'
            ],
            'supplier' => [
                'dashboard.view', 'supplier.dashboard', 'supplier.products.view',
                'supplier.products.create', 'supplier.products.edit', 'supplier.products.delete',
                'supplier.orders.view', 'supplier.orders.manage',
                'supplier.inquiries.view', 'supplier.inquiries.respond',
                'supplier.feedback.create'
            ],
            'customer' => [
                'dashboard.view', 'products.view', 'orders.view_own', 'orders.create'
            ]
        ];
        
        if ($role->name === 'super_admin') {
            return true;
        }
        
        if (isset($rolePermissions[$role->name])) {
            return in_array($permissionName, $rolePermissions[$role->name]);
        }
        
        return false;
    }

    /**
     * Enforce permission on a controller method
     */
    public static function enforcePermission(string $permission): void
    {
        if (!auth()->check()) {
            abort(401, 'Authentication required');
        }
        
        if (!auth()->user()->hasPermission($permission)) {
            Log::warning('Permission enforcement failed', [
                'user_id' => auth()->id(),
                'permission' => $permission,
                'url' => request()->url(),
                'method' => request()->method()
            ]);
            
            abort(403, "Permission denied: {$permission}");
        }
    }

    /**
     * Log permission access attempt
     */
    public static function logPermissionAccess(User $user, string $permission, bool $granted, string $context = ''): void
    {
        Log::info('Permission access attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role' => $user->role?->name,
            'permission' => $permission,
            'granted' => $granted,
            'context' => $context,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ]);
    }
}
