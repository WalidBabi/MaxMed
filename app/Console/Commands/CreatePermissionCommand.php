<?php

namespace App\Console\Commands;

use App\Services\PermissionManagementService;
use Illuminate\Console\Command;

class CreatePermissionCommand extends Command
{
    protected $signature = 'permission:create 
                            {name : The permission name (e.g., products.create)}
                            {display_name : The display name for the permission}
                            {--description= : Description of the permission}
                            {--category=system : Category for the permission}
                            {--active : Make the permission active}';

    protected $description = 'Create a new permission dynamically';

    public function handle(PermissionManagementService $permissionService)
    {
        $name = $this->argument('name');
        $displayName = $this->argument('display_name');
        $description = $this->option('description');
        $category = $this->option('category');
        $isActive = $this->option('active') ?? true;

        // Validate permission name format
        if (!preg_match('/^[a-z_]+\.[a-z_]+$/', $name)) {
            $this->error('Permission name must be in format: category.action (e.g., products.create)');
            return 1;
        }

        try {
            $permission = $permissionService->createPermission([
                'name' => $name,
                'display_name' => $displayName,
                'description' => $description,
                'category' => $category,
                'is_active' => $isActive,
            ]);

            $this->info("✅ Permission created successfully!");
            $this->line("ID: {$permission->id}");
            $this->line("Name: {$permission->name}");
            $this->line("Display Name: {$permission->display_name}");
            $this->line("Category: {$permission->category}");
            $this->line("Active: " . ($permission->is_active ? 'Yes' : 'No'));

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to create permission: " . $e->getMessage());
            return 1;
        }
    }
}
