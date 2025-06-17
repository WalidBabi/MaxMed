<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixMigrationState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix-state {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix migration state for existing tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing migration state...');

        // Get existing tables
        $existingTables = $this->getExistingTables();
        $this->info('Found existing tables: ' . implode(', ', $existingTables));

        // Get pending migrations
        $pendingMigrations = $this->getPendingMigrations();
        
        if (empty($pendingMigrations)) {
            $this->info('✅ No pending migrations found!');
            return;
        }

        $this->info('Pending migrations: ' . count($pendingMigrations));

        // Check which migrations correspond to existing tables
        $migrationsToMark = [];
        foreach ($pendingMigrations as $migration) {
            if ($this->shouldMarkAsRun($migration, $existingTables)) {
                $migrationsToMark[] = $migration;
            }
        }

        if (empty($migrationsToMark)) {
            $this->info('✅ No migrations need to be marked as run!');
            return;
        }

        $this->warn('Migrations to mark as run:');
        foreach ($migrationsToMark as $migration) {
            $this->line("   - {$migration}");
        }

        if (!$this->option('force') && !$this->confirm('Mark these migrations as run?')) {
            $this->info('Operation cancelled.');
            return;
        }

        // Mark migrations as run
        $this->markMigrationsAsRun($migrationsToMark);

        $this->info('✅ Migration state fixed successfully!');
        $this->info('Now you can run: php artisan migrate');
    }

    private function getExistingTables()
    {
        try {
            $tables = [];
            $results = DB::select('SHOW TABLES');
            $key = 'Tables_in_' . DB::getDatabaseName();
            
            foreach ($results as $result) {
                $tables[] = $result->$key;
            }
            
            return $tables;
        } catch (\Exception $e) {
            $this->error('Could not get existing tables: ' . $e->getMessage());
            return [];
        }
    }

    private function getPendingMigrations()
    {
        $migrationFiles = [];
        $migrationPath = database_path('migrations');
        
        if (is_dir($migrationPath)) {
            $files = scandir($migrationPath);
            foreach ($files as $file) {
                if (str_ends_with($file, '.php')) {
                    $migrationFiles[] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
        }

        // Get already run migrations
        $runMigrations = [];
        try {
            $results = DB::table('migrations')->pluck('migration')->toArray();
            $runMigrations = $results;
        } catch (\Exception $e) {
            // Migrations table might not exist
        }

        // Return only pending migrations
        return array_diff($migrationFiles, $runMigrations);
    }

    private function shouldMarkAsRun($migration, $existingTables)
    {
        // Table creation migrations
        if (str_contains($migration, 'create_') && str_contains($migration, '_table')) {
            $tableName = $this->extractTableName($migration, 'create_');
            return in_array($tableName, $existingTables);
        }

        // Table modification migrations for existing tables
        if (str_contains($migration, 'add_') || str_contains($migration, 'modify_')) {
            $tableName = $this->extractTableNameFromModification($migration);
            if ($tableName && in_array($tableName, $existingTables)) {
                // Check if column already exists
                return $this->columnAlreadyExists($migration, $tableName);
            }
        }

        return false;
    }

    private function extractTableName($migration, $prefix)
    {
        $pattern = "/{$prefix}(.+?)_table/";
        if (preg_match($pattern, $migration, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractTableNameFromModification($migration)
    {
        // Extract table name from patterns like "add_column_to_table_name_table"
        if (preg_match('/(?:add_|modify_).+_to_(.+)_table/', $migration, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function columnAlreadyExists($migration, $tableName)
    {
        try {
            // For role_id to users table
            if (str_contains($migration, 'add_role_id_to_users')) {
                return Schema::hasColumn('users', 'role_id');
            }
            
            // For customer_id to orders table
            if (str_contains($migration, 'add_customer_id_to_orders')) {
                return Schema::hasColumn('orders', 'customer_id');
            }

            // For profile_photo to users table
            if (str_contains($migration, 'add_profile_photo_to_users')) {
                return Schema::hasColumn('users', 'profile_photo');
            }

            // Generic column detection
            if (preg_match('/add_(.+?)_to_' . $tableName . '_table/', $migration, $matches)) {
                $columnName = $matches[1];
                return Schema::hasColumn($tableName, $columnName);
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function markMigrationsAsRun($migrations)
    {
        $batch = $this->getNextBatchNumber();
        
        foreach ($migrations as $migration) {
            try {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch
                ]);
                $this->line("   ✅ Marked: {$migration}");
            } catch (\Exception $e) {
                $this->error("   ❌ Failed to mark: {$migration} - " . $e->getMessage());
            }
        }
    }

    private function getNextBatchNumber()
    {
        try {
            $lastBatch = DB::table('migrations')->max('batch');
            return $lastBatch ? $lastBatch + 1 : 1;
        } catch (\Exception $e) {
            return 1;
        }
    }
}
