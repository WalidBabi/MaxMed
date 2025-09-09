<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Gate;

class RolePermissionSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::createDefaultPermissions();
        
        // Run the comprehensive role seeder
        $this->artisan('db:seed', ['--class' => 'ComprehensiveRoleSeeder']);
    }

    /** @test */
    public function permissions_are_created_correctly()
    {
        $permissions = Permission::all();
        
        $this->assertGreaterThan(100, $permissions->count());
        
        // Check specific permissions exist
        $this->assertTrue(Permission::where('name', 'users.view')->exists());
        $this->assertTrue(Permission::where('name', 'products.create')->exists());
        $this->assertTrue(Permission::where('name', 'orders.manage_status')->exists());
        $this->assertTrue(Permission::where('name', 'supplier.products.view')->exists());
    }

    /** @test */
    public function roles_are_created_with_correct_permissions()
    {
        $superAdmin = Role::where('name', 'super_admin')->first();
        $salesRep = Role::where('name', 'sales_rep')->first();
        $supplier = Role::where('name', 'supplier')->first();

        $this->assertNotNull($superAdmin);
        $this->assertNotNull($salesRep);
        $this->assertNotNull($supplier);

        // Super admin should have all permissions
        $allPermissions = Permission::where('is_active', true)->count();
        $this->assertEquals($allPermissions, $superAdmin->permissions()->count());

        // Sales rep should have specific permissions
        $this->assertTrue($salesRep->hasPermission('orders.create'));
        $this->assertTrue($salesRep->hasPermission('customers.view'));
        $this->assertFalse($salesRep->hasPermission('users.delete'));

        // Supplier should have supplier-specific permissions
        $this->assertTrue($supplier->hasPermission('supplier.products.view'));
        $this->assertTrue($supplier->hasPermission('supplier.products.create'));
        $this->assertFalse($supplier->hasPermission('users.view'));
    }

    /** @test */
    public function user_can_check_permissions_through_role()
    {
        $role = Role::where('name', 'sales_rep')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($user->hasPermission('orders.create'));
        $this->assertTrue($user->hasPermission('customers.view'));
        $this->assertFalse($user->hasPermission('users.delete'));
        $this->assertFalse($user->hasPermission('system.settings'));
    }

    /** @test */
    public function user_can_check_multiple_permissions()
    {
        $role = Role::where('name', 'sales_manager')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        // Has any permission
        $this->assertTrue($user->hasAnyPermission(['orders.create', 'nonexistent.permission']));
        $this->assertFalse($user->hasAnyPermission(['nonexistent.permission', 'another.nonexistent']));

        // Has all permissions
        $this->assertTrue($user->hasAllPermissions(['orders.create', 'customers.view']));
        $this->assertFalse($user->hasAllPermissions(['orders.create', 'system.settings']));
    }

    /** @test */
    public function role_can_be_assigned_and_revoked_permissions()
    {
        $role = Role::create([
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'description' => 'Role for testing',
            'is_active' => true,
        ]);

        $permission = Permission::where('name', 'products.view')->first();

        // Initially no permissions
        $this->assertFalse($role->hasPermission('products.view'));

        // Give permission
        $role->givePermissionTo($permission);
        $role->refresh();
        $this->assertTrue($role->hasPermission('products.view'));

        // Revoke permission
        $role->revokePermissionTo($permission);
        $role->refresh();
        $this->assertFalse($role->hasPermission('products.view'));
    }

    /** @test */
    public function role_can_sync_permissions()
    {
        $role = Role::create([
            'name' => 'test_role_sync',
            'display_name' => 'Test Role Sync',
            'description' => 'Role for testing sync',
            'is_active' => true,
        ]);

        $permissions = Permission::whereIn('name', ['products.view', 'orders.view', 'customers.view'])->get();

        // Sync permissions
        $role->syncPermissions($permissions->pluck('name')->toArray());
        $role->refresh();

        $this->assertTrue($role->hasPermission('products.view'));
        $this->assertTrue($role->hasPermission('orders.view'));
        $this->assertTrue($role->hasPermission('customers.view'));
        $this->assertFalse($role->hasPermission('users.delete'));

        // Sync with different permissions
        $role->syncPermissions(['products.view', 'users.view']);
        $role->refresh();

        $this->assertTrue($role->hasPermission('products.view'));
        $this->assertTrue($role->hasPermission('users.view'));
        $this->assertFalse($role->hasPermission('orders.view')); // Should be removed
        $this->assertFalse($role->hasPermission('customers.view')); // Should be removed
    }

    /** @test */
    public function gates_work_correctly_for_permissions()
    {
        $role = Role::where('name', 'sales_manager')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user);

        $this->assertTrue(Gate::allows('orders.create'));
        $this->assertTrue(Gate::allows('customers.view'));
        $this->assertFalse(Gate::allows('system.settings'));
        $this->assertFalse(Gate::allows('users.delete'));
    }

    /** @test */
    public function super_admin_has_access_to_everything()
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $user = User::factory()->create(['role_id' => $superAdminRole->id]);

        $this->actingAs($user);

        // Should have access to any permission
        $this->assertTrue(Gate::allows('users.delete'));
        $this->assertTrue(Gate::allows('system.settings'));
        $this->assertTrue(Gate::allows('products.create'));
        $this->assertTrue(Gate::allows('any.random.permission'));
    }

    /** @test */
    public function middleware_blocks_unauthorized_access()
    {
        $role = Role::where('name', 'viewer')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user);

        // Should be blocked from accessing admin user management
        $response = $this->get(route('admin.users.create'));
        $response->assertStatus(403);
    }

    /** @test */
    public function middleware_allows_authorized_access()
    {
        $role = Role::where('name', 'business_admin')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user);

        // Should have access to user management
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function supplier_can_only_access_own_products()
    {
        $supplierRole = Role::where('name', 'supplier')->first();
        $supplier = User::factory()->create(['role_id' => $supplierRole->id]);
        
        $otherSupplier = User::factory()->create(['role_id' => $supplierRole->id]);

        $this->actingAs($supplier);

        // Create a product for the supplier
        $ownProduct = \App\Models\Product::factory()->create(['supplier_id' => $supplier->id]);
        $otherProduct = \App\Models\Product::factory()->create(['supplier_id' => $otherSupplier->id]);

        // Should be able to view own product
        $this->assertTrue(Gate::allows('view-product', $ownProduct));
        $this->assertTrue(Gate::allows('edit-product', $ownProduct));

        // Should not be able to view other supplier's product
        $this->assertFalse(Gate::allows('edit-product', $otherProduct));
    }

    /** @test */
    public function role_permissions_are_cached_properly()
    {
        $role = Role::where('name', 'sales_rep')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        // First check - should query database
        $hasPermission1 = $user->hasPermission('orders.create');

        // Second check - should use cached result
        $hasPermission2 = $user->hasPermission('orders.create');

        $this->assertTrue($hasPermission1);
        $this->assertTrue($hasPermission2);
        $this->assertEquals($hasPermission1, $hasPermission2);
    }

    /** @test */
    public function inactive_permissions_are_not_granted()
    {
        $permission = Permission::where('name', 'products.view')->first();
        $permission->update(['is_active' => false]);

        $role = Role::create([
            'name' => 'test_inactive_perm',
            'display_name' => 'Test Inactive Permission',
            'description' => 'Testing inactive permissions',
            'is_active' => true,
        ]);

        $role->givePermissionTo($permission);
        $user = User::factory()->create(['role_id' => $role->id]);

        // Should not have permission because it's inactive
        $this->assertFalse($user->hasPermission('products.view'));
    }

    /** @test */
    public function permission_categories_are_properly_organized()
    {
        $categories = Permission::getCategories();

        $this->assertIsArray($categories);
        $this->assertArrayHasKey('users', $categories);
        $this->assertArrayHasKey('products', $categories);
        $this->assertArrayHasKey('orders', $categories);
        $this->assertArrayHasKey('crm', $categories);
        $this->assertArrayHasKey('system', $categories);

        // Check that permissions are properly categorized
        $userPermissions = Permission::getByCategory('users');
        $this->assertGreaterThan(0, $userPermissions->count());
        
        $productPermissions = Permission::getByCategory('products');
        $this->assertGreaterThan(0, $productPermissions->count());
    }
}
