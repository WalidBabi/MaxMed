<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

class RolePermissionReport extends Command
{
    protected $signature = 'roles:report {role_name?}';
    protected $description = 'Generate detailed role and permission report';

    public function handle()
    {
        $roleName = $this->argument('role_name');

        if ($roleName) {
            return $this->showSpecificRole($roleName);
        }

        return $this->showAllRoles();
    }

    private function showSpecificRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return 1;
        }

        $this->info("🔐 DETAILED ROLE REPORT: {$role->display_name}");
        $this->info("=" . str_repeat("=", strlen($role->display_name) + 25));
        $this->newLine();

        $this->line("📋 Basic Information:");
        $this->line("   Name: {$role->name}");
        $this->line("   Display Name: {$role->display_name}");
        $this->line("   Description: {$role->description}");
        $this->line("   Status: " . ($role->is_active ? 'Active' : 'Inactive'));
        $this->line("   Users Assigned: " . $role->users()->count());
        $this->newLine();

        $permissions = $role->permissions()->where('is_active', true)->get();
        $this->line("🔑 Permissions ({$permissions->count()}):");
        
        $groupedPermissions = $permissions->groupBy('category');
        
        foreach ($groupedPermissions as $category => $categoryPermissions) {
            $categoryName = Permission::getCategories()[$category] ?? ucfirst($category);
            $this->line("   📁 {$categoryName} ({$categoryPermissions->count()}):");
            
            foreach ($categoryPermissions as $permission) {
                $this->line("     ✅ {$permission->display_name} ({$permission->name})");
            }
            $this->newLine();
        }

        return 0;
    }

    private function showAllRoles()
    {
        $this->info('📊 COMPREHENSIVE ROLE & PERMISSION REPORT');
        $this->info('=========================================');
        $this->newLine();

        $roles = Role::with(['permissions', 'users'])->where('is_active', true)->get();
        
        // Summary table
        $this->info('📋 ROLE SUMMARY:');
        $headers = ['Role Name', 'Display Name', 'Permissions', 'Users', 'Business Function'];
        $data = [];
        
        foreach ($roles as $role) {
            $data[] = [
                $role->name,
                $role->display_name,
                $role->permissions()->count(),
                $role->users()->count(),
                $this->getBusinessFunction($role->name)
            ];
        }
        
        $this->table($headers, $data);
        $this->newLine();

        // Detailed breakdown by business area
        $this->info('🏢 BUSINESS AREA BREAKDOWN:');
        $this->newLine();

        $businessAreas = [
            'Executive & Administration' => ['super_admin', 'admin', 'business_admin'],
            'Sales & Customer Management' => ['sales_manager', 'sales_rep', 'sales-rep', 'customer_service_manager', 'support'],
            'Operations & Inventory' => ['manager', 'inventory_manager', 'operations_manager'],
            'Procurement & Purchasing' => ['purchasing', 'purchasing_manager'],
            'Marketing & Content' => ['marketing_manager', 'content-editor', 'crm_manager', 'crm-administrator'],
            'Financial Management' => ['financial_manager'],
            'External Partners' => ['supplier'],
            'System Access' => ['viewer', 'api_user'],
        ];

        foreach ($businessAreas as $area => $roleNames) {
            $this->line("📂 {$area}:");
            
            foreach ($roleNames as $roleName) {
                $role = $roles->where('name', $roleName)->first();
                if ($role) {
                    $permCount = $role->permissions()->count();
                    $userCount = $role->users()->count();
                    $this->line("   • {$role->display_name}: {$permCount} permissions, {$userCount} users");
                }
            }
            $this->newLine();
        }

        // Permission distribution analysis
        $this->info('📊 PERMISSION DISTRIBUTION ANALYSIS:');
        $categories = Permission::getCategories();
        
        foreach ($categories as $categoryKey => $categoryName) {
            $categoryPermissions = Permission::where('category', $categoryKey)->where('is_active', true)->get();
            $this->line("📁 {$categoryName} ({$categoryPermissions->count()} permissions):");
            
            foreach ($roles->take(5) as $role) { // Show top 5 roles
                $rolePermissionsInCategory = $role->permissions()->where('category', $categoryKey)->count();
                if ($rolePermissionsInCategory > 0) {
                    $percentage = round(($rolePermissionsInCategory / $categoryPermissions->count()) * 100);
                    $this->line("   • {$role->display_name}: {$rolePermissionsInCategory}/{$categoryPermissions->count()} ({$percentage}%)");
                }
            }
            $this->newLine();
        }

        // Security analysis
        $this->info('🔒 SECURITY ANALYSIS:');
        
        $superAdminRole = $roles->where('name', 'super_admin')->first();
        $viewerRole = $roles->where('name', 'viewer')->first();
        $supplierRole = $roles->where('name', 'supplier')->first();
        
        if ($superAdminRole && $viewerRole) {
            $this->line("   📈 Permission Range: {$viewerRole->permissions()->count()} (Viewer) to {$superAdminRole->permissions()->count()} (Super Admin)");
        }
        
        $adminRoles = $roles->whereIn('name', ['super_admin', 'admin', 'business_admin']);
        $avgAdminPermissions = $adminRoles->avg(function ($role) {
            return $role->permissions()->count();
        });
        
        $this->line("   🔑 Average Admin Permissions: " . round($avgAdminPermissions));
        
        if ($supplierRole) {
            $supplierPermissions = $supplierRole->permissions()->count();
            $this->line("   🏭 Supplier Permissions: {$supplierPermissions} (External access limited)");
        }
        
        $this->newLine();
        
        $this->info('✅ Report complete! Use "php artisan roles:report [role_name]" for detailed role analysis.');
        
        return 0;
    }

    private function getBusinessFunction($roleName)
    {
        $functions = [
            'super_admin' => 'System Administration',
            'admin' => 'General Administration',
            'business_admin' => 'Business Operations',
            'manager' => 'Operations Management',
            'sales_manager' => 'Sales Leadership',
            'sales_rep' => 'Sales Operations',
            'sales-rep' => 'Sales Operations',
            'purchasing' => 'Procurement',
            'purchasing_manager' => 'Procurement Leadership',
            'support' => 'Customer Support',
            'customer_service_manager' => 'Support Leadership',
            'crm_manager' => 'CRM Operations',
            'crm-administrator' => 'CRM Administration',
            'content-editor' => 'Content Management',
            'marketing_manager' => 'Marketing Operations',
            'supplier' => 'External Supply',
            'viewer' => 'Read-only Access',
            'financial_manager' => 'Financial Operations',
            'inventory_manager' => 'Inventory Management',
            'api_user' => 'System Integration',
        ];

        return $functions[$roleName] ?? 'General Business';
    }
}
