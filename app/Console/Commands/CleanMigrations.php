<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CleanMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:clean {--backup} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up messy migration files and reorganize them properly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting Migration Cleanup Process...');

        // Step 1: Backup if requested
        if ($this->option('backup')) {
            $this->backupMigrations();
        }

        // Step 2: Identify problematic migrations
        $problematicMigrations = $this->identifyProblematicMigrations();
        
        if (empty($problematicMigrations)) {
            $this->info('✅ No problematic migrations found!');
            return;
        }

        // Step 3: Show what will be cleaned
        $this->warn('🚨 Found ' . count($problematicMigrations) . ' problematic migration files:');
        foreach ($problematicMigrations as $migration) {
            $this->line("   - {$migration}");
        }

        // Step 4: Confirm action
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to clean these migration files?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        // Step 5: Clean the migrations
        $this->cleanMigrations($problematicMigrations);

        // Step 6: Create clean migration structure
        $this->createCleanMigrations();

        $this->info('✅ Migration cleanup completed successfully!');
        $this->info('📝 Next steps:');
        $this->info('   1. Review the new migration files');
        $this->info('   2. Run: php artisan migrate');
        $this->info('   3. Test your application');
    }

    private function backupMigrations()
    {
        $backupDir = database_path('migrations_backup_' . date('Y_m_d_H_i_s'));
        File::makeDirectory($backupDir, 0755, true);
        
        $migrationFiles = File::files(database_path('migrations'));
        foreach ($migrationFiles as $file) {
            File::copy($file->getPathname(), $backupDir . '/' . $file->getFilename());
        }
        
        $this->info("📁 Backup created at: {$backupDir}");
    }

    private function identifyProblematicMigrations()
    {
        $migrationDir = database_path('migrations');
        $files = File::files($migrationDir);
        $problematic = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            
            // Check for problematic patterns
            if (
                // Files without proper timestamp format
                !preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_/', $filename) ||
                
                // Files with temp/test names
                str_contains($filename, 'temp_') ||
                str_contains($filename, 'test_') ||
                
                // Files without proper extension
                !str_ends_with($filename, '.php') ||
                
                // Duplicate migration names (different timestamps, same content)
                $this->isDuplicateMigration($filename)
            ) {
                $problematic[] = $filename;
            }
        }

        return $problematic;
    }

    private function isDuplicateMigration($filename)
    {
        // Extract migration name without timestamp
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $filename);
        
        // Check if there are other files with similar names
        $migrationDir = database_path('migrations');
        $files = File::files($migrationDir);
        $similarCount = 0;
        
        foreach ($files as $file) {
            $otherName = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $file->getFilename());
            if ($name === $otherName) {
                $similarCount++;
            }
        }
        
        return $similarCount > 1;
    }

    private function cleanMigrations($problematicMigrations)
    {
        $cleanedDir = database_path('migrations_cleaned_' . date('Y_m_d_H_i_s'));
        File::makeDirectory($cleanedDir, 0755, true);

        foreach ($problematicMigrations as $migration) {
            $oldPath = database_path('migrations/' . $migration);
            $newPath = $cleanedDir . '/' . $migration;
            
            // Move instead of delete (safer)
            File::move($oldPath, $newPath);
            $this->line("   ✅ Moved: {$migration}");
        }

        $this->info("📁 Problematic migrations moved to: {$cleanedDir}");
    }

    private function createCleanMigrations()
    {
        $this->info('🔨 Creating clean migration structure...');

        // Check what tables already exist
        $existingTables = $this->getExistingTables();
        
        // Create base migrations for missing essential structure
        $this->createEssentialMigrations($existingTables);
    }

    private function getExistingTables()
    {
        try {
            return DB::connection()->getDoctrineSchemaManager()->listTableNames();
        } catch (\Exception $e) {
            $this->warn('Could not get existing tables: ' . $e->getMessage());
            return [];
        }
    }

    private function createEssentialMigrations($existingTables)
    {
        $timestamp = now()->format('Y_m_d_His');
        
        // Only create migrations for tables that don't exist
        $essentialMigrations = [
            'add_slug_to_products_table' => !in_array('products', $existingTables) || !$this->columnExists('products', 'slug'),
            'add_slug_to_categories_table' => !in_array('categories', $existingTables) || !$this->columnExists('categories', 'slug'),
        ];

        $counter = 0;
        foreach ($essentialMigrations as $migrationName => $needed) {
            if ($needed) {
                $counter++;
                $newTimestamp = now()->addSeconds($counter)->format('Y_m_d_His');
                $this->createMigrationFile($newTimestamp, $migrationName);
            }
        }
    }

    private function columnExists($table, $column)
    {
        try {
            return DB::getSchemaBuilder()->hasColumn($table, $column);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createMigrationFile($timestamp, $migrationName)
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $migrationName)));
        $filename = "{$timestamp}_{$migrationName}.php";
        $filepath = database_path("migrations/{$filename}");

        $stub = $this->getMigrationStub($className, $migrationName);
        File::put($filepath, $stub);
        
        $this->line("   ✅ Created: {$filename}");
    }

    private function getMigrationStub($className, $migrationName)
    {
        if (str_contains($migrationName, 'add_slug_to_products')) {
            return $this->getProductSlugMigrationStub($className);
        } elseif (str_contains($migrationName, 'add_slug_to_categories')) {
            return $this->getCategorySlugMigrationStub($className);
        }

        return $this->getBasicMigrationStub($className);
    }

    private function getProductSlugMigrationStub($className)
    {
        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'slug')) {
            Schema::table('products', function (Blueprint \$table) {
                \$table->string('slug')->nullable()->unique()->after('name');
                \$table->index('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'slug')) {
            Schema::table('products', function (Blueprint \$table) {
                \$table->dropIndex(['slug']);
                \$table->dropColumn('slug');
            });
        }
    }
};";
    }

    private function getCategorySlugMigrationStub($className)
    {
        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'slug')) {
            Schema::table('categories', function (Blueprint \$table) {
                \$table->string('slug')->nullable()->unique()->after('name');
                \$table->index('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'slug')) {
            Schema::table('categories', function (Blueprint \$table) {
                \$table->dropIndex(['slug']);
                \$table->dropColumn('slug');
            });
        }
    }
};";
    }

    private function getBasicMigrationStub($className)
    {
        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add your migration logic here
    }

    public function down(): void
    {
        // Add your rollback logic here
    }
};";
    }
}
