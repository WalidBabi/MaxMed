<?php

namespace App\Console\Commands;

use App\Services\PermissionManagementService;
use Illuminate\Console\Command;

class GenerateControllerPermissionsCommand extends Command
{
    protected $signature = 'permission:generate-controller 
                            {controller : The controller name (e.g., products, users)}
                            {--actions=view,create,edit,delete : Comma-separated list of actions}
                            {--middleware : Generate middleware code}';

    protected $description = 'Generate permissions and middleware code for a controller';

    public function handle(PermissionManagementService $permissionService)
    {
        $controller = $this->argument('controller');
        $actions = explode(',', $this->option('actions'));
        $generateMiddleware = $this->option('middleware');

        $this->info("🚀 Generating permissions for controller: {$controller}");

        try {
            // Generate permissions
            $permissions = $permissionService->generateControllerPermissions($controller, $actions);

            $this->info("✅ Generated " . count($permissions) . " permissions:");
            foreach ($permissions as $permission) {
                $this->line("  - {$permission->name} ({$permission->display_name})");
            }

            // Generate middleware code if requested
            if ($generateMiddleware) {
                $this->newLine();
                $this->info("📝 Middleware code for your controller:");
                $this->line("```php");
                $this->line($permissionService->generateMiddlewareCode($controller, $actions));
                $this->line("```");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to generate permissions: " . $e->getMessage());
            return 1;
        }
    }
}
