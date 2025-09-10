<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateMiddlewareCommand extends Command
{
    protected $signature = 'middleware:generate 
                            {controller : The controller name (e.g., products, users)}
                            {--actions=view,create,edit,delete : Comma-separated list of actions}
                            {--file= : Path to controller file to update}
                            {--dry-run : Show what would be generated without making changes}';

    protected $description = 'Generate middleware code for a controller';

    public function handle()
    {
        $controller = $this->argument('controller');
        $actions = explode(',', $this->option('actions'));
        $filePath = $this->option('file');
        $dryRun = $this->option('dry-run');

        $this->info("🚀 Generating middleware for controller: {$controller}");

        // Generate middleware code
        $middlewareCode = $this->generateMiddlewareCode($controller, $actions);
        
        $this->newLine();
        $this->info("📝 Generated middleware code:");
        $this->line("```php");
        $this->line($middlewareCode);
        $this->line("```");

        // If file path is provided, update the file
        if ($filePath) {
            if ($dryRun) {
                $this->info("🔍 DRY RUN - Would update file: {$filePath}");
            } else {
                $this->updateControllerFile($filePath, $middlewareCode);
            }
        } else {
            $this->newLine();
            $this->info("💡 To automatically update a controller file, use the --file option:");
            $this->line("php artisan middleware:generate {$controller} --file=app/Http/Controllers/Admin/{$controller}Controller.php");
        }

        return 0;
    }

    private function generateMiddlewareCode(string $controller, array $actions): string
    {
        $category = Str::slug($controller);
        $code = "public function __construct()\n{\n";
        $code .= "    \$this->middleware('auth');\n";

        foreach ($actions as $action) {
            $permissionName = "{$category}.{$action}";
            $method = $this->getMethodForAction($action);
            $code .= "    \$this->middleware('permission:{$permissionName}')->only(['{$method}']);\n";
        }

        $code .= "}";
        return $code;
    }

    private function getMethodForAction(string $action): string
    {
        $mapping = [
            'view' => 'index,show',
            'create' => 'create,store',
            'edit' => 'edit,update',
            'delete' => 'destroy',
            'approve' => 'approve',
            'manage' => 'manage',
        ];

        return $mapping[$action] ?? $action;
    }

    private function updateControllerFile(string $filePath, string $middlewareCode): void
    {
        if (!File::exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return;
        }

        $content = File::get($filePath);
        
        // Check if __construct method already exists
        if (preg_match('/public function __construct\(\)\s*\{[^}]*\}/s', $content)) {
            $this->warn("⚠️  Controller already has a __construct method. Please update manually.");
            return;
        }

        // Find the class declaration and add the constructor after it
        $pattern = '/(class\s+\w+Controller\s+extends\s+\w+\s*\{)/';
        $replacement = "$1\n\n    {$middlewareCode}\n";
        
        $updatedContent = preg_replace($pattern, $replacement, $content);
        
        if ($updatedContent !== $content) {
            File::put($filePath, $updatedContent);
            $this->info("✅ Updated controller file: {$filePath}");
        } else {
            $this->error("❌ Failed to update controller file. Please check the file structure.");
        }
    }
}
