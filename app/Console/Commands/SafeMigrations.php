<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SafeMigrations extends Command
{
    protected $signature = 'make:migrations-safe';
    protected $description = 'Convert all migrations to be production-safe by adding table existence checks';

    public function handle()
    {
        $path = database_path('migrations');
        $files = File::glob($path . '/*.php');
        $modified = 0;

        foreach ($files as $file) {
            $content = File::get($file);
            
            // Skip if already modified
            if (strpos($content, 'Schema::hasTable') !== false) {
                $this->info("Already safe: " . basename($file));
                continue;
            }

            // Look for Schema::create pattern
            if (preg_match("/Schema::create\('([^']+)'/", $content, $matches)) {
                $tableName = $matches[1];
                
                // Extract the create table schema
                if (preg_match('/Schema::create\([^{]+{([^}]+)}\);/s', $content, $schemaMatches)) {
                    $schemaContent = trim($schemaMatches[1]);
                    
                    // Create the new migration content with table existence check
                    $newContent = preg_replace(
                        '/public function up\(\)[^{]*{([^}]+)}/s',
                        'public function up(): void
    {
        if (!Schema::hasTable(\'' . $tableName . '\')) {
            Schema::create(\'' . $tableName . '\', function (Blueprint $table) {
                ' . $schemaContent . '
            });
        }
    }',
                        $content
                    );
                    
                    // Update the down method to be production safe
                    $newContent = preg_replace(
                        '/public function down\(\)[^{]*{[^}]+}/s',
                        'public function down(): void
    {
        // Don\'t drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }',
                        $newContent
                    );
                    
                    File::put($file, $newContent);
                    $this->info("Updated migration: " . basename($file));
                    $modified++;
                }
            } else if (preg_match("/Schema::table\('([^']+)'/", $content, $matches)) {
                // This is an alter table migration, already safer by nature
                $this->info("Alter table migration (already safer): " . basename($file));
            } else {
                $this->warn("Skipped (no table creation/modification found): " . basename($file));
            }
        }
        
        $this->info("Completed! Modified $modified migration files to be production-safe.");
        $this->info("You can now run 'php artisan migrate' safely in production.");
    }
} 