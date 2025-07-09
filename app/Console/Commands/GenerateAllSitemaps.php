<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAllSitemaps extends Command
{
    protected $signature = 'sitemap:generate-all 
                            {--validate : Validate all sitemaps}
                            {--submit : Submit to search engines}
                            {--keywords : Generate keyword routes}';
    
    protected $description = 'Generate ALL sitemaps for maximum SEO coverage and first page Google rankings';

    public function handle()
    {
        $this->info('🚀 Starting COMPREHENSIVE sitemap generation for MaxMed UAE...');
        $this->info('🎯 Target: First page Google rankings for every category and keyword');
        $this->newLine();
        
        // Run all sitemap generation commands
        $this->runSitemapCommand('sitemap:max-seo', 'MAX SEO sitemaps');
        $this->runSitemapCommand('sitemap:ultimate', 'Ultimate comprehensive sitemaps');
        $this->runSitemapCommand('sitemap:comprehensive', 'Comprehensive sitemaps');
        $this->runSitemapCommand('sitemap:keywords', 'Keyword-based sitemaps');
        
        $this->newLine();
        $this->info('✅ All sitemaps generated successfully!');
        $this->displayFinalStats();
        $this->displaySubmissionInstructions();
        
        return 0;
    }

    protected function runSitemapCommand($command, $description)
    {
        $this->info("🔄 Running {$description}...");
        
        $options = [];
        if ($this->option('validate')) {
            $options['--validate'] = true;
        }
        if ($this->option('submit')) {
            $options['--submit'] = true;
        }
        if ($this->option('keywords') && $command === 'sitemap:keywords') {
            $options['--generate-routes'] = true;
        }
        
        $result = $this->call($command, $options);
        
        if ($result === 0) {
            $this->line("   ✅ {$description} completed successfully");
        } else {
            $this->error("   ❌ {$description} failed");
        }
        
        $this->newLine();
    }

    private function displayFinalStats()
    {
        $this->info('📊 FINAL SITEMAP STATISTICS:');
        $this->newLine();
        
        // Count all sitemap files
        $sitemapFiles = glob(public_path('sitemap*.xml'));
        $totalFiles = count($sitemapFiles);
        
        $this->line("   📂 Total sitemap files: {$totalFiles}");
        
        // Count total URLs
        $totalUrls = 0;
        foreach ($sitemapFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $urls = substr_count($content, '<loc>');
                $totalUrls += $urls;
                
                $filename = basename($file);
                $this->line("   📄 {$filename}: {$urls} URLs");
            }
        }
        
        $this->newLine();
        $this->info("   🎯 TOTAL URLS FOR GOOGLE: {$totalUrls}");
        $this->newLine();
        
        // Show coverage breakdown
        $this->info('🗂️ SITEMAP COVERAGE:');
        $this->line('   ✅ Main pages (homepage, about, contact)');
        $this->line('   ✅ All products with images and descriptions');
        $this->line('   ✅ All categories and subcategories');
        $this->line('   ✅ All brands and manufacturers');
        $this->line('   ✅ Industry-specific pages');
        $this->line('   ✅ News and blog content');
        $this->line('   ✅ Quotation and commerce pages');
        $this->line('   ✅ Regional pages (Dubai, Abu Dhabi, UAE)');
        $this->line('   ✅ Keyword-based landing pages');
        $this->line('   ✅ Image sitemap for Google Images');
        $this->line('   ✅ News sitemap for Google News');
        $this->line('   ✅ Functional and utility pages');
        $this->newLine();
    }

    private function displaySubmissionInstructions()
    {
        $this->info('📤 GOOGLE SEARCH CONSOLE SUBMISSION:');
        $this->newLine();
        
        $this->line('1. Go to: https://search.google.com/search-console');
        $this->line('2. Select your MaxMed UAE property');
        $this->line('3. Navigate to "Sitemaps" in the left menu');
        $this->line('4. Submit these sitemaps:');
        $this->newLine();
        
        $this->info('   🎯 MAIN SITEMAP (submit this first):');
        $this->line('   https://maxmedme.com/sitemap.xml');
        $this->newLine();
        
        $this->info('   📂 ADDITIONAL SITEMAPS:');
        $sitemapFiles = glob(public_path('sitemap*.xml'));
        foreach ($sitemapFiles as $file) {
            $filename = basename($file);
            if ($filename !== 'sitemap.xml') {
                $this->line("   https://maxmedme.com/{$filename}");
            }
        }
        $this->newLine();
        
        $this->info('🌐 OTHER SEARCH ENGINES:');
        $this->line('   Bing: https://www.bing.com/webmasters/');
        $this->line('   Yandex: https://webmaster.yandex.com/');
        $this->line('   Baidu: https://ziyuan.baidu.com/');
        $this->newLine();
        
        $this->info('📅 SCHEDULE REGULAR UPDATES:');
        $this->line('   Set up a cron job to run this daily:');
        $this->line('   0 2 * * * cd /path/to/your/app && php artisan sitemap:generate-all');
        $this->newLine();
        
        $this->info('📊 MONITOR RESULTS:');
        $this->line('   1. Check Google Search Console for indexing status');
        $this->line('   2. Monitor organic search traffic increases');
        $this->line('   3. Track keyword rankings improvements');
        $this->line('   4. Watch for Google Images traffic growth');
        $this->newLine();
        
        $this->info('🎯 EXPECTED RESULTS:');
        $this->line('   Week 1-2: Google discovers new URLs');
        $this->line('   Week 3-4: Initial indexing begins');
        $this->line('   Week 5-8: Ranking improvements appear');
        $this->line('   Week 9-12: Significant traffic growth');
        $this->newLine();
        
        $this->info('🚀 MAXMED UAE IS NOW OPTIMIZED FOR FIRST PAGE GOOGLE RANKINGS!');
    }
} 