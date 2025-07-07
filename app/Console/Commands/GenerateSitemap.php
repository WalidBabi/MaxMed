<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate comprehensive sitemap for SEO (daily scheduled version)';

    public function handle()
    {
        $this->info('🚀 Generating daily sitemap update...');
        
        // Run the ultimate sitemap command with default options
        $this->call('sitemap:ultimate', [
            '--images' => true
        ]);
        
        $this->info('✅ Daily sitemap generation completed!');
        $this->line('📍 Main sitemap: https://maxmedme.com/sitemap.xml');
        
        return 0;
    }
} 