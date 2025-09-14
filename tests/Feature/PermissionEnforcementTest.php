<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Services\PermissionEnforcementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PermissionEnforcementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $superAdmin;
    protected $admin;
    protected $supplier;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test roles and users
        $this->createTestRolesAndUsers();
    }

    private function createTestRolesAndUsers()
    {
        // Create roles
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
            'description' => 'Full system access',
            'is_active' => true
        ]);

        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrative access',
            'is_active' => true
        ]);

        $supplierRole = Role::create([
            'name' => 'supplier',
            'display_name' => 'Supplier',
            'description' => 'Supplier access',
            'is_active' => true
        ]);

        $customerRole = Role::create([
            'name' => 'customer',
            'display_name' => 'Customer',
            'description' => 'Customer access',
            'is_active' => true
        ]);

        // Create permissions
        Permission::createDefaultPermissions();

        // Assign permissions to roles
        $this->assignPermissionsToRoles($superAdminRole, $adminRole, $supplierRole, $customerRole);

        // Create test users
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $superAdminRole->id,
            'email_verified_at' => now()
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now()
        ]);

        $this->supplier = User::create([
            'name' => 'Supplier User',
            'email' => 'supplier@test.com',
            'password' => bcrypt('password'),
            'role_id' => $supplierRole->id,
            'email_verified_at' => now()
        ]);

        $this->customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role_id' => $customerRole->id,
            'email_verified_at' => now()
        ]);
    }

    private function assignPermissionsToRoles($superAdminRole, $adminRole, $supplierRole, $customerRole)
    {
        $allPermissions = Permission::where('is_active', true)->get();

        // Super admin gets all permissions
        $superAdminRole->permissions()->attach($allPermissions->pluck('id'));

        // Admin gets most permissions except system admin ones
        $adminPermissions = $allPermissions->filter(function($permission) {
            return !in_array($permission->name, [
                'system.maintenance',
                'system.backup',
                'users.impersonate'
            ]);
        });
        $adminRole->permissions()->attach($adminPermissions->pluck('id'));

        // Supplier gets supplier-specific permissions
        $supplierPermissions = $allPermissions->filter(function($permission) {
            return strpos($permission->name, 'supplier.') === 0 || 
                   in_array($permission->name, [
                       'dashboard.view',
                       'products.view',
                       'suppliers.view'
                   ]);
        });
        $supplierRole->permissions()->attach($supplierPermissions->pluck('id'));

        // Customer gets basic permissions
        $customerPermissions = $allPermissions->filter(function($permission) {
            return in_array($permission->name, [
                'dashboard.view',
                'products.view',
                'orders.view_own',
                'orders.create',
                'customers.view'
            ]);
        });
        $customerRole->permissions()->attach($customerPermissions->pluck('id'));
    }

    /** @test */
    public function super_admin_has_all_permissions()
    {
        $allPermissions = Permission::where('is_active', true)->get();
        
        foreach ($allPermissions as $permission) {
            $this->assertTrue(
                $this->superAdmin->hasPermission($permission->name),
                "Super admin should have permission: {$permission->name}"
            );
        }
    }

    /** @test */
    public function admin_has_appropriate_permissions()
    {
        $adminPermissions = [
            'dashboard.view', 'dashboard.analytics',
            'users.view', 'users.create', 'users.edit',
            'roles.view', 'roles.create', 'roles.edit',
            'products.view', 'products.create', 'products.edit',
            'orders.view', 'orders.view_all', 'orders.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'crm.access', 'crm.leads.view', 'crm.leads.create',
            'analytics.view', 'reports.generate'
        ];

        foreach ($adminPermissions as $permission) {
            $this->assertTrue(
                $this->admin->hasPermission($permission),
                "Admin should have permission: {$permission}"
            );
        }

        // Admin should NOT have system admin permissions
        $systemPermissions = [
            'system.maintenance',
            'system.backup',
            'users.impersonate'
        ];

        foreach ($systemPermissions as $permission) {
            $this->assertFalse(
                $this->admin->hasPermission($permission),
                "Admin should NOT have permission: {$permission}"
            );
        }
    }

    /** @test */
    public function supplier_has_supplier_permissions()
    {
        $supplierPermissions = [
            'dashboard.view',
            'supplier.dashboard',
            'supplier.products.view',
            'supplier.products.create',
            'supplier.products.edit',
            'supplier.orders.view',
            'supplier.orders.manage',
            'supplier.inquiries.view',
            'supplier.inquiries.respond'
        ];

        foreach ($supplierPermissions as $permission) {
            $this->assertTrue(
                $this->supplier->hasPermission($permission),
                "Supplier should have permission: {$permission}"
            );
        }

        // Supplier should NOT have admin permissions
        $adminPermissions = [
            'users.create', 'users.edit', 'users.delete',
            'roles.create', 'roles.edit', 'roles.delete',
            'system.settings', 'system.maintenance'
        ];

        foreach ($adminPermissions as $permission) {
            $this->assertFalse(
                $this->supplier->hasPermission($permission),
                "Supplier should NOT have permission: {$permission}"
            );
        }
    }

    /** @test */
    public function customer_has_limited_permissions()
    {
        $customerPermissions = [
            'dashboard.view',
            'products.view',
            'orders.view_own',
            'orders.create',
            'customers.view'
        ];

        foreach ($customerPermissions as $permission) {
            $this->assertTrue(
                $this->customer->hasPermission($permission),
                "Customer should have permission: {$permission}"
            );
        }

        // Customer should NOT have admin or supplier permissions
        $restrictedPermissions = [
            'users.create', 'users.edit', 'users.delete',
            'roles.create', 'roles.edit', 'roles.delete',
            'suppliers.create', 'suppliers.edit',
            'crm.access', 'crm.leads.view',
            'analytics.view', 'reports.generate'
        ];

        foreach ($restrictedPermissions as $permission) {
            $this->assertFalse(
                $this->customer->hasPermission($permission),
                "Customer should NOT have permission: {$permission}"
            );
        }
    }

    /** @test */
    public function permission_middleware_blocks_unauthorized_access()
    {
        // Test that admin routes require proper permissions
        $this->actingAs($this->customer);
        
        // Customer should not access admin routes
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);

        $response = $this->get('/admin/roles');
        $response->assertStatus(403);
    }

    /** @test */
    public function permission_middleware_allows_authorized_access()
    {
        // Test that admin can access admin routes
        $this->actingAs($this->admin);
        
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);

        $response = $this->get('/admin/roles');
        $response->assertStatus(200);
    }

    /** @test */
    public function supplier_can_access_supplier_area()
    {
        $this->actingAs($this->supplier);
        
        // Test supplier dashboard access
        $response = $this->get('/supplier/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function customer_cannot_access_admin_areas()
    {
        $this->actingAs($this->customer);
        
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/roles',
            '/admin/products',
            '/admin/orders',
            '/admin/customers',
            '/admin/suppliers',
            '/admin/crm',
            '/supplier/dashboard'
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(403);
        }
    }

    /** @test */
    public function permission_enforcement_service_works()
    {
        $results = PermissionEnforcementService::testUserPermissions($this->admin);
        
        $this->assertIsArray($results);
        $this->assertArrayHasKey('user_id', $results);
        $this->assertArrayHasKey('permissions_tested', $results);
        $this->assertArrayHasKey('permissions_passed', $results);
        $this->assertArrayHasKey('permissions_failed', $results);
        $this->assertArrayHasKey('details', $results);
        
        $this->assertGreaterThan(0, $results['permissions_tested']);
        $this->assertGreaterThan(0, $results['permissions_passed']);
    }

    /** @test */
    public function permission_audit_report_generates_successfully()
    {
        $report = PermissionEnforcementService::generateAuditReport();
        
        $this->assertIsArray($report);
        $this->assertArrayHasKey('timestamp', $report);
        $this->assertArrayHasKey('user_tests', $report);
        $this->assertArrayHasKey('controller_tests', $report);
        $this->assertArrayHasKey('route_tests', $report);
        $this->assertArrayHasKey('ui_tests', $report);
        $this->assertArrayHasKey('recommendations', $report);
    }

    /** @test */
    public function has_any_permission_works_correctly()
    {
        // Test with permissions the admin has
        $this->assertTrue($this->admin->hasAnyPermission([
            'users.view', 'users.create', 'nonexistent.permission'
        ]));

        // Test with permissions the admin doesn't have
        $this->assertFalse($this->admin->hasAnyPermission([
            'nonexistent.permission1', 'nonexistent.permission2'
        ]));

        // Test with mixed permissions
        $this->assertTrue($this->admin->hasAnyPermission([
            'users.view', 'system.maintenance' // admin has users.view but not system.maintenance
        ]));
    }

    /** @test */
    public function has_all_permissions_works_correctly()
    {
        // Test with permissions the admin has
        $this->assertTrue($this->admin->hasAllPermissions([
            'users.view', 'users.create'
        ]));

        // Test with permissions the admin doesn't have
        $this->assertFalse($this->admin->hasAllPermissions([
            'users.view', 'system.maintenance' // admin has users.view but not system.maintenance
        ]));

        // Test with all nonexistent permissions
        $this->assertFalse($this->admin->hasAllPermissions([
            'nonexistent.permission1', 'nonexistent.permission2'
        ]));
    }

    /** @test */
    public function role_permission_relationships_work()
    {
        $role = $this->admin->role;
        $this->assertNotNull($role);
        
        $permissions = $role->permissions;
        $this->assertGreaterThan(0, $permissions->count());
        
        $this->assertTrue($role->hasPermission('users.view'));
        $this->assertFalse($role->hasPermission('system.maintenance'));
    }

    /** @test */
    public function permission_logging_works()
    {
        PermissionEnforcementService::logPermissionAccess(
            $this->admin,
            'test.permission',
            true,
            'test_context'
        );
        
        // This test mainly ensures the method doesn't throw exceptions
        $this->assertTrue(true);
    }

    /** @test */
    public function permission_enforcement_throws_proper_exceptions()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
        PermissionEnforcementService::enforcePermission('nonexistent.permission');
    }
}
