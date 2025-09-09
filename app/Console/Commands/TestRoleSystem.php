<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;

class TestRoleSystem extends Command
{
    protected $signature = 'roles:test';
    protected $description = 'Test the role and permission system';

    public function handle()
    {
        $this->info('🧪 Testing Role and Permission System');
        $this->newLine();

        // Test 1: Check if roles and permissions exist
        $roleCount = Role::count();
        $permissionCount = Permission::count();
        
        $this->info("📊 System Statistics:");
        $this->line("   Roles: {$roleCount}");
        $this->line("   Permissions: {$permissionCount}");
        $this->newLine();

        if ($roleCount === 0 || $permissionCount === 0) {
            $this->error('❌ No roles or permissions found. Please run the seeder first.');
            return 1;
        }

        // Test 2: Check specific roles
        $testRoles = ['super_admin', 'sales_manager', 'sales_rep', 'supplier', 'viewer'];
        $this->info("🔍 Testing Role Creation:");
        
        foreach ($testRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $permCount = $role->permissions()->count();
                $this->line("   ✅ {$role->display_name}: {$permCount} permissions");
            } else {
                $this->line("   ❌ {$roleName}: Not found");
            }
        }
        $this->newLine();

        // Test 3: Test permission assignments
        $this->info("🔐 Testing Permission Assignments:");
        
        $salesManager = Role::where('name', 'sales_manager')->first();
        if ($salesManager) {
            $testPermissions = ['orders.create', 'customers.view', 'users.delete', 'system.settings'];
            foreach ($testPermissions as $perm) {
                $has = $salesManager->hasPermission($perm);
                $status = $has ? '✅' : '❌';
                $this->line("   {$status} Sales Manager -> {$perm}");
            }
        }
        $this->newLine();

        // Test 4: Test user permissions
        $this->info("👤 Testing User Permissions:");
        
        $user = User::with('role')->first();
        if ($user && $user->role) {
            $this->line("   User: {$user->name} ({$user->role->display_name})");
            $testPermissions = ['dashboard.view', 'products.view', 'users.create'];
            foreach ($testPermissions as $perm) {
                $has = $user->hasPermission($perm);
                $status = $has ? '✅' : '❌';
                $this->line("   {$status} {$perm}");
            }
        } else {
            $this->line("   ⚠️  No users found or user has no role assigned");
        }
        $this->newLine();

        // Test 5: Test permission categories
        $this->info("📂 Testing Permission Categories:");
        $categories = Permission::getCategories();
        foreach ($categories as $key => $name) {
            $count = Permission::where('category', $key)->count();
            $this->line("   📁 {$name}: {$count} permissions");
        }
        $this->newLine();

        // Test 6: Test role methods
        $this->info("⚙️  Testing Role Methods:");
        $testRole = Role::where('name', 'sales_rep')->first();
        if ($testRole) {
            // Test hasAnyPermission
            $hasAny = $testRole->hasAnyPermission(['orders.create', 'nonexistent.permission']);
            $this->line("   " . ($hasAny ? '✅' : '❌') . " hasAnyPermission(['orders.create', 'nonexistent.permission'])");
            
            // Test hasAllPermissions
            $hasAll = $testRole->hasAllPermissions(['orders.create', 'customers.view']);
            $this->line("   " . ($hasAll ? '✅' : '❌') . " hasAllPermissions(['orders.create', 'customers.view'])");
        }
        $this->newLine();

        $this->info('🎉 Role and Permission System Test Complete!');
        
        return 0;
    }
}
