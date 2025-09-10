<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DynamicPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Creating dynamic permissions...');

        // Create permission management permissions
        $permissionPermissions = [
            [
                'name' => 'permissions.view',
                'display_name' => 'View Permissions',
                'description' => 'View system permissions',
                'category' => 'permissions',
            ],
            [
                'name' => 'permissions.create',
                'display_name' => 'Create Permissions',
                'description' => 'Create new permissions',
                'category' => 'permissions',
            ],
            [
                'name' => 'permissions.edit',
                'display_name' => 'Edit Permissions',
                'description' => 'Edit existing permissions',
                'category' => 'permissions',
            ],
            [
                'name' => 'permissions.delete',
                'display_name' => 'Delete Permissions',
                'description' => 'Delete/deactivate permissions',
                'category' => 'permissions',
            ],
        ];

        foreach ($permissionPermissions as $permissionData) {
            Permission::updateOrCreate(
                ['name' => $permissionData['name']],
                array_merge($permissionData, ['is_active' => true])
            );
        }

        $this->command->info('✅ Created permission management permissions');

        // Assign permission management permissions to super admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $permissionIds = Permission::whereIn('name', array_column($permissionPermissions, 'name'))
                ->pluck('id')
                ->toArray();
            
            $superAdmin->permissions()->syncWithoutDetaching($permissionIds);
            $this->command->info('✅ Assigned permission management permissions to Super Admin');
        }

        // Create some example dynamic permissions
        $examplePermissions = [
            // Blog Management
            [
                'name' => 'blog.view',
                'display_name' => 'View Blog Posts',
                'description' => 'View blog posts and articles',
                'category' => 'blog',
            ],
            [
                'name' => 'blog.create',
                'display_name' => 'Create Blog Posts',
                'description' => 'Create new blog posts',
                'category' => 'blog',
            ],
            [
                'name' => 'blog.edit',
                'display_name' => 'Edit Blog Posts',
                'description' => 'Edit existing blog posts',
                'category' => 'blog',
            ],
            [
                'name' => 'blog.delete',
                'display_name' => 'Delete Blog Posts',
                'description' => 'Delete blog posts',
                'category' => 'blog',
            ],

            // Settings Management
            [
                'name' => 'settings.view',
                'display_name' => 'View Settings',
                'description' => 'View system settings',
                'category' => 'settings',
            ],
            [
                'name' => 'settings.edit',
                'display_name' => 'Edit Settings',
                'description' => 'Modify system settings',
                'category' => 'settings',
            ],

            // Reports Management
            [
                'name' => 'reports.view',
                'display_name' => 'View Reports',
                'description' => 'View system reports',
                'category' => 'reports',
            ],
            [
                'name' => 'reports.create',
                'display_name' => 'Create Reports',
                'description' => 'Generate new reports',
                'category' => 'reports',
            ],
            [
                'name' => 'reports.export',
                'display_name' => 'Export Reports',
                'description' => 'Export reports to various formats',
                'category' => 'reports',
            ],
        ];

        foreach ($examplePermissions as $permissionData) {
            Permission::updateOrCreate(
                ['name' => $permissionData['name']],
                array_merge($permissionData, ['is_active' => true])
            );
        }

        $this->command->info('✅ Created example dynamic permissions');

        // Show summary
        $totalPermissions = Permission::count();
        $activePermissions = Permission::where('is_active', true)->count();
        $categories = Permission::distinct('category')->count();

        $this->command->info("📊 Permission Summary:");
        $this->command->line("  Total Permissions: {$totalPermissions}");
        $this->command->line("  Active Permissions: {$activePermissions}");
        $this->command->line("  Categories: {$categories}");
    }
}
