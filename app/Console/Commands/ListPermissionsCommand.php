<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Services\PermissionManagementService;
use Illuminate\Console\Command;

class ListPermissionsCommand extends Command
{
    protected $signature = 'permission:list 
                            {--category= : Filter by category}
                            {--active : Show only active permissions}
                            {--inactive : Show only inactive permissions}
                            {--format=table : Output format (table, json, csv)}';

    protected $description = 'List all permissions with filtering options';

    public function handle(PermissionManagementService $permissionService)
    {
        $category = $this->option('category');
        $active = $this->option('active');
        $inactive = $this->option('inactive');
        $format = $this->option('format');

        $query = Permission::query();

        if ($category) {
            $query->where('category', $category);
        }

        if ($active) {
            $query->where('is_active', true);
        } elseif ($inactive) {
            $query->where('is_active', false);
        }

        $permissions = $query->orderBy('category')->orderBy('name')->get();

        if ($permissions->isEmpty()) {
            $this->info("No permissions found with the specified criteria.");
            return 0;
        }

        switch ($format) {
            case 'json':
                $this->line($permissions->toJson(JSON_PRETTY_PRINT));
                break;
            case 'csv':
                $this->outputCsv($permissions);
                break;
            default:
                $this->outputTable($permissions);
        }

        $this->newLine();
        $this->info("Total permissions: " . $permissions->count());
        
        if ($category) {
            $this->info("Category: {$category}");
        }

        return 0;
    }

    private function outputTable($permissions)
    {
        $headers = ['ID', 'Name', 'Display Name', 'Category', 'Active', 'Created'];
        $rows = [];

        foreach ($permissions as $permission) {
            $rows[] = [
                $permission->id,
                $permission->name,
                $permission->display_name,
                $permission->category,
                $permission->is_active ? '✅' : '❌',
                $permission->created_at->format('Y-m-d'),
            ];
        }

        $this->table($headers, $rows);
    }

    private function outputCsv($permissions)
    {
        $this->line('ID,Name,Display Name,Category,Active,Created');
        
        foreach ($permissions as $permission) {
            $this->line(sprintf(
                '%d,%s,%s,%s,%s,%s',
                $permission->id,
                $permission->name,
                $permission->display_name,
                $permission->category,
                $permission->is_active ? 'Yes' : 'No',
                $permission->created_at->format('Y-m-d')
            ));
        }
    }
}
