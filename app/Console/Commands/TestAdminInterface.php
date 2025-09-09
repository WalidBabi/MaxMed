<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

class TestAdminInterface extends Command
{
    protected $signature = 'admin:test-interface';
    protected $description = 'Test admin interface data';

    public function handle()
    {
        $this->info('🧪 Testing Admin Interface Data');
        $this->info('==================================');
        $this->newLine();

        // Test role loading with permission counts
        $this->info('📊 Testing Role Loading:');
        $roles = Role::withCount(['users', 'permissions'])->where('is_active', true)->take(5)->get();
        
        foreach ($roles as $role) {
            $this->line("   • {$role->display_name}:");
            $this->line("     - Users: {$role->users_count}");
            $this->line("     - Permissions: {$role->permissions_count}");
            $this->line("     - Legacy Permissions Array: " . count($role->permissions ?? []));
            $this->newLine();
        }

        // Test specific role details
        $this->info('🔍 Testing Specific Role (Admin):');
        $adminRole = Role::with(['users', 'permissions'])->where('name', 'admin')->first();
        
        if ($adminRole) {
            $this->line("   Role: {$adminRole->display_name}");
            $this->line("   Description: {$adminRole->description}");
            $this->line("   Users: {$adminRole->users->count()}");
            $this->line("   New Permissions: {$adminRole->permissions->count()}");
            $this->line("   Legacy Permissions: " . count($adminRole->permissions ?? []));
            
            $this->newLine();
            $this->line("   Permission Categories:");
            $categoryGroups = $adminRole->permissions->groupBy('category');
            foreach ($categoryGroups as $category => $categoryPermissions) {
                $categoryName = Permission::getCategories()[$category] ?? ucfirst($category);
                $this->line("     - {$categoryName}: {$categoryPermissions->count()} permissions");
            }
        }
        $this->newLine();

        // Test permission system
        $this->info('⚙️  Testing Permission System:');
        $this->line("   Total Permissions in DB: " . Permission::count());
        $this->line("   Active Permissions: " . Permission::where('is_active', true)->count());
        $this->line("   Permission Categories: " . count(Permission::getCategories()));
        $this->newLine();

        // Test role-permission relationships
        $this->info('🔗 Testing Role-Permission Relationships:');
        $totalRolePermissions = \DB::table('role_permissions')->count();
        $this->line("   Total Role-Permission Assignments: {$totalRolePermissions}");
        
        $rolesWithPermissions = Role::has('permissions')->count();
        $this->line("   Roles with Permissions: {$rolesWithPermissions}");
        $this->newLine();

        $this->info('✅ Admin Interface Test Complete!');
        $this->info('The admin interface should now show the correct permission counts.');
        
        return 0;
    }
}
