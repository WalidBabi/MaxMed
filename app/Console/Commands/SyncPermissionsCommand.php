<?php

namespace App\Console\Commands;

use App\Services\PermissionManagementService;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permission:sync 
                            {--dry-run : Show what would be created without actually creating}
                            {--force : Force sync without confirmation}';

    protected $description = 'Sync permissions with routes automatically';

    public function handle(PermissionManagementService $permissionService)
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("🔍 Scanning routes for permission middleware...");

        try {
            $result = $permissionService->syncPermissionsWithRoutes();

            if ($dryRun) {
                $this->info("📋 DRY RUN - Would create/update permissions:");
                $this->line("Permissions found: " . count($result['permissions']));
                $this->line("Would create: " . $result['created']);
                $this->line("Would update: " . $result['updated']);
                
                if (!empty($result['permissions'])) {
                    $this->newLine();
                    $this->info("Permissions that would be created:");
                    foreach (array_unique($result['permissions']) as $permission) {
                        $this->line("  - {$permission}");
                    }
                }
            } else {
                if (!$force && ($result['created'] > 0 || $result['updated'] > 0)) {
                    if (!$this->confirm("Create {$result['created']} new permissions and update {$result['updated']} existing ones?")) {
                        $this->info("Operation cancelled.");
                        return 0;
                    }
                }

                $this->info("✅ Sync completed!");
                $this->line("Permissions found: " . count($result['permissions']));
                $this->line("Created: " . $result['created']);
                $this->line("Updated: " . $result['updated']);

                if (!empty($result['permissions'])) {
                    $this->newLine();
                    $this->info("Permissions processed:");
                    foreach (array_unique($result['permissions']) as $permission) {
                        $this->line("  - {$permission}");
                    }
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to sync permissions: " . $e->getMessage());
            return 1;
        }
    }
}
