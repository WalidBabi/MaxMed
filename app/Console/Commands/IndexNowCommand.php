<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IndexNowService;

class IndexNowCommand extends Command
{
    protected $signature = 'indexnow:submit 
                            {--url= : Submit a specific URL}
                            {--all : Submit all sitemap URLs}
                            {--products : Submit new product URLs}
                            {--categories : Submit new category URLs}
                            {--news : Submit new news URLs}
                            {--validate : Validate IndexNow setup}
                            {--config : Show IndexNow configuration}';
    
    protected $description = 'Submit URLs to IndexNow for automatic search engine indexing';

    private $indexNowService;

    public function __construct(IndexNowService $indexNowService)
    {
        parent::__construct();
        $this->indexNowService = $indexNowService;
    }

    public function handle()
    {
        $this->info('🚀 IndexNow URL Submission for MaxMed UAE');
        $this->newLine();

        if ($this->option('validate')) {
            $this->validateSetup();
            return 0;
        }

        if ($this->option('config')) {
            $this->showConfig();
            return 0;
        }

        if ($url = $this->option('url')) {
            $this->submitSingleUrl($url);
            return 0;
        }

        if ($this->option('all')) {
            $this->submitAllUrls();
            return 0;
        }

        if ($this->option('products')) {
            $this->submitNewProducts();
            return 0;
        }

        if ($this->option('categories')) {
            $this->submitNewCategories();
            return 0;
        }

        if ($this->option('news')) {
            $this->submitNewNews();
            return 0;
        }

        // Default: submit all URLs
        $this->submitAllUrls();
        return 0;
    }

    private function validateSetup()
    {
        $this->info('🔍 Validating IndexNow setup...');
        
        $results = $this->indexNowService->validateSetup();
        
        $this->table(
            ['Check', 'Status', 'Details'],
            [
                [
                    'Key File Accessible',
                    $results['key_file_accessible'] ? '✅ PASS' : '❌ FAIL',
                    $results['key_file_accessible'] ? 'Key file is accessible' : ($results['key_file_error'] ?? 'Unknown error')
                ],
                [
                    'Key File Content',
                    $results['key_file_content'] ? '✅ PASS' : '❌ FAIL',
                    $results['key_file_content'] ? 'Key content matches' : 'Key content does not match'
                ],
                [
                    'API Connection',
                    $results['api_connection'] ? '✅ PASS' : '❌ FAIL',
                    $results['api_connection'] ? 'API connection successful' : ($results['api_error'] ?? 'Unknown error')
                ],
                [
                    'API Status',
                    $results['api_status'] ?? 'N/A',
                    $results['api_status'] === 200 ? 'OK' : 'Error'
                ]
            ]
        );

        if ($results['key_file_accessible'] && $results['key_file_content'] && $results['api_connection']) {
            $this->info('✅ IndexNow setup is valid and ready to use!');
        } else {
            $this->error('❌ IndexNow setup has issues. Please check the configuration.');
        }
    }

    private function showConfig()
    {
        $this->info('⚙️ IndexNow Configuration');
        
        $config = $this->indexNowService->getConfig();
        
        $this->table(
            ['Setting', 'Value'],
            [
                ['API Key', $config['api_key']],
                ['Key Location', $config['key_location']],
                ['Host', $config['host']],
                ['IndexNow URL', $config['indexnow_url']]
            ]
        );
    }

    private function submitSingleUrl(string $url)
    {
        $this->info("📤 Submitting single URL: {$url}");
        
        $success = $this->indexNowService->submitUrl($url);
        
        if ($success) {
            $this->info('✅ URL submitted successfully!');
        } else {
            $this->error('❌ URL submission failed. Check logs for details.');
        }
    }

    private function submitAllUrls()
    {
        $this->info('📤 Submitting all sitemap URLs to IndexNow...');
        
        $results = $this->indexNowService->submitSitemapUrls();
        
        $this->displayResults($results, 'All URLs');
    }

    private function submitNewProducts()
    {
        $this->info('📤 Submitting new product URLs to IndexNow...');
        
        $results = $this->indexNowService->submitNewProducts();
        
        $this->displayResults($results, 'New Products');
    }

    private function submitNewCategories()
    {
        $this->info('📤 Submitting new category URLs to IndexNow...');
        
        $results = $this->indexNowService->submitNewCategories();
        
        $this->displayResults($results, 'New Categories');
    }

    private function submitNewNews()
    {
        $this->info('📤 Submitting new news URLs to IndexNow...');
        
        $results = $this->indexNowService->submitNewNews();
        
        $this->displayResults($results, 'New News');
    }

    private function displayResults(array $results, string $type)
    {
        $this->newLine();
        $this->info("📊 {$type} Submission Results:");
        
        $totalUrls = 0;
        $successfulRequests = 0;
        
        foreach ($results as $index => $result) {
            $totalUrls += $result['urls_count'];
            if ($result['success']) {
                $successfulRequests++;
            }
            
            $status = $result['success'] ? '✅' : '❌';
            $this->line("   {$status} Batch " . ($index + 1) . ": {$result['urls_count']} URLs (Status: {$result['status']})");
        }
        
        $this->newLine();
        $this->info("📈 Summary:");
        $this->line("   • Total URLs: {$totalUrls}");
        $this->line("   • Successful requests: {$successfulRequests}/" . count($results));
        $this->line("   • Success rate: " . round(($successfulRequests / count($results)) * 100, 1) . "%");
        
        if ($successfulRequests === count($results)) {
            $this->info('✅ All submissions successful!');
        } else {
            $this->warn('⚠️ Some submissions failed. Check logs for details.');
        }
    }
} 