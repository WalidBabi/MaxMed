<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateDubaiTimezone extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dubai:update-dates {--dry-run : Run without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Update blade templates to use Dubai timezone formatting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🕒 Updating blade templates for Dubai timezone...');
        
        if ($isDryRun) {
            $this->warn('📋 DRY RUN MODE - No files will be modified');
        }
        
        $patterns = [
            // Basic created_at format patterns
            '/\{\{\s*\$([a-zA-Z_]+)->created_at->format\([\'"]([^\'"]+)[\'"]\)\s*\}\}/' => '{{ formatDubaiDate($\\1->created_at, \'\\2\') }}',
            
            // Updated_at format patterns
            '/\{\{\s*\$([a-zA-Z_]+)->updated_at->format\([\'"]([^\'"]+)[\'"]\)\s*\}\}/' => '{{ formatDubaiDate($\\1->updated_at, \'\\2\') }}',
            
            // diffForHumans patterns
            '/\{\{\s*\$([a-zA-Z_]+)->created_at->diffForHumans\(\)\s*\}\}/' => '{{ formatDubaiDateForHumans($\\1->created_at) }}',
            '/\{\{\s*\$([a-zA-Z_]+)->updated_at->diffForHumans\(\)\s*\}\}/' => '{{ formatDubaiDateForHumans($\\1->updated_at) }}',
            
            // Format with chaining
            '/\{\{\s*\$([a-zA-Z_]+)->([a-zA-Z_]+)->format\([\'"]([^\'"]+)[\'"]\)\s*\}\}/' => '{{ formatDubaiDate($\\1->\\2, \'\\3\') }}',
            
            // now() format patterns
            '/\{\{\s*now\(\)->format\([\'"]([^\'"]+)[\'"]\)\s*\}\}/' => '{{ nowDubai(\'\\1\') }}',
        ];
        
        $viewPath = resource_path('views');
        $files = $this->getBladeFiles($viewPath);
        
        $updatedFiles = 0;
        $totalChanges = 0;
        
        foreach ($files as $file) {
            $content = File::get($file);
            $originalContent = $content;
            $fileChanges = 0;
            
            foreach ($patterns as $pattern => $replacement) {
                $newContent = preg_replace($pattern, $replacement, $content);
                if ($newContent !== $content) {
                    $fileChanges += preg_match_all($pattern, $content);
                    $content = $newContent;
                }
            }
            
            if ($content !== $originalContent) {
                $relativePath = str_replace(resource_path('views/'), '', $file);
                
                if (!$isDryRun) {
                    File::put($file, $content);
                    $this->line("✅ Updated: {$relativePath} ({$fileChanges} changes)");
                } else {
                    $this->line("📝 Would update: {$relativePath} ({$fileChanges} changes)");
                }
                
                $updatedFiles++;
                $totalChanges += $fileChanges;
            }
        }
        
        if ($updatedFiles > 0) {
            $this->info("\n🎉 Summary:");
            $this->info("   Files processed: " . count($files));
            $this->info("   Files updated: {$updatedFiles}");
            $this->info("   Total changes: {$totalChanges}");
            
            if ($isDryRun) {
                $this->warn("\n⚠️  This was a dry run. Re-run without --dry-run to apply changes.");
            } else {
                $this->info("\n✨ All blade templates have been updated for Dubai timezone!");
                $this->info("💡 Remember to test your application and clear any caches.");
            }
        } else {
            $this->info("✨ No files needed updating. All templates are already using Dubai timezone formatting!");
        }
        
        // Show manual update suggestions
        $this->showManualUpdateSuggestions();
    }
    
    /**
     * Get all blade files recursively
     */
    private function getBladeFiles($directory)
    {
        $files = [];
        $items = File::allFiles($directory);
        
        foreach ($items as $item) {
            if ($item->getExtension() === 'php' && str_ends_with($item->getFilename(), '.blade.php')) {
                $files[] = $item->getPathname();
            }
        }
        
        return $files;
    }
    
    /**
     * Show suggestions for manual updates
     */
    private function showManualUpdateSuggestions()
    {
        $this->info("\n📋 Manual updates you may need to consider:");
        $this->line("   1. Check JavaScript files for date formatting");
        $this->line("   2. Update email templates if they contain dates");
        $this->line("   3. Review API responses for consistent timezone handling");
        $this->line("   4. Test notification timestamps");
        $this->line("   5. Verify PDF generation with correct dates");
        
        $this->info("\n💡 Helpful commands:");
        $this->line("   php artisan config:clear");
        $this->line("   php artisan view:clear");
        $this->line("   php artisan cache:clear");
        
        $this->info("\n📖 See DUBAI_TIMEZONE_IMPLEMENTATION.md for complete documentation");
    }
} 