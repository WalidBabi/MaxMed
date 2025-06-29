<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MonitorSearchConsoleStatus extends Command
{
    protected $signature = 'seo:monitor-search-console 
                            {--check-urls : Check if URLs are accessible}
                            {--export-report : Export status report}';
    
    protected $description = 'Monitor the status of URLs that were pending in Google Search Console';

    private $baseUrl = 'https://maxmedme.com';
    private $csvPath;
    private $results = [];

    public function handle()
    {
        $this->info('📊 Monitoring Google Search Console URL Status...');
        $this->info('=' . str_repeat('=', 60));
        
        $this->csvPath = base_path('Table.csv');
        
        if (!File::exists($this->csvPath)) {
            $this->error('❌ Table.csv not found. Please place the CSV file in the project root.');
            return 1;
        }
        
        $this->loadUrlsFromCsv();
        
        if ($this->option('check-urls')) {
            $this->checkUrlAccessibility();
        }
        
        $this->analyzeUrlPatterns();
        $this->generateRecommendations();
        
        if ($this->option('export-report')) {
            $this->exportReport();
        }
        
        return 0;
    }

    private function loadUrlsFromCsv()
    {
        $this->info('📂 Loading URLs from CSV...');
        
        $csv = array_map('str_getcsv', file($this->csvPath));
        $header = array_shift($csv); // Remove header
        
        foreach ($csv as $row) {
            if (isset($row[0]) && $row[0]) {
                $url = $row[0];
                $crawlDate = $row[1] ?? null;
                $status = $row[2] ?? 'Pending';
                
                $this->results[] = [
                    'url' => $url,
                    'crawl_date' => $crawlDate,
                    'status' => $status,
                    'path' => parse_url($url, PHP_URL_PATH),
                    'type' => $this->categorizeUrl($url),
                    'accessible' => null,
                    'redirect_target' => null,
                    'fix_applied' => $this->isFixApplied($url)
                ];
            }
        }
        
        $this->line('   Loaded ' . count($this->results) . ' URLs');
    }

    private function categorizeUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        
        if (preg_match('/\/product\/\d+/', $path)) {
            return 'product_id';
        } elseif (preg_match('/\/products\//', $path)) {
            return 'product_slug';
        } elseif (preg_match('/\/quotation\/.*dubai-uae/', $path)) {
            return 'quotation_long';
        } elseif (preg_match('/\/quotation\/\d+/', $path)) {
            return 'quotation_id';
        } elseif (preg_match('/\/categories\/\d+/', $path)) {
            return 'category_id';
        } elseif (preg_match('/\/categories\/.*/', $path)) {
            return 'category_slug';
        } else {
            return 'other';
        }
    }

    private function isFixApplied($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        
        // Check if it's an old product URL that should be redirected
        if (preg_match('/\/product\/(\d+)/', $path, $matches)) {
            return 'redirect_added';
        }
        
        // Check if it's an old category URL
        if (preg_match('/\/categories\/(\d+)/', $path, $matches)) {
            return 'redirect_added';
        }
        
        // Check if it's an old quotation URL
        if (preg_match('/\/quotation\/(\d+)/', $path, $matches)) {
            return 'redirect_added';
        }
        
        return 'no_fix_needed';
    }

    private function checkUrlAccessibility()
    {
        $this->info('🔍 Checking URL accessibility...');
        $bar = $this->output->createProgressBar(count($this->results));
        
        foreach ($this->results as &$result) {
            try {
                $response = Http::timeout(10)->get($result['url']);
                $result['accessible'] = $response->successful();
                $result['status_code'] = $response->status();
                
                // Check for redirects
                if ($response->status() >= 300 && $response->status() < 400) {
                    $result['redirect_target'] = $response->header('Location');
                }
                
            } catch (\Exception $e) {
                $result['accessible'] = false;
                $result['status_code'] = 'error';
                $result['error'] = $e->getMessage();
            }
            
            $bar->advance();
            usleep(250000); // 250ms delay to be respectful
        }
        
        $bar->finish();
        $this->newLine();
    }

    private function analyzeUrlPatterns()
    {
        $this->info('📈 Analyzing URL patterns...');
        
        $typeStats = [];
        $fixStats = [];
        
        foreach ($this->results as $result) {
            // Count by type
            $type = $result['type'];
            if (!isset($typeStats[$type])) {
                $typeStats[$type] = 0;
            }
            $typeStats[$type]++;
            
            // Count by fix status
            $fix = $result['fix_applied'];
            if (!isset($fixStats[$fix])) {
                $fixStats[$fix] = 0;
            }
            $fixStats[$fix]++;
        }
        
        $this->table(
            ['URL Type', 'Count', 'Percentage'],
            collect($typeStats)->map(function ($count, $type) {
                $percentage = round(($count / count($this->results)) * 100, 1);
                return [$type, $count, $percentage . '%'];
            })->toArray()
        );
        
        $this->info("\n🛠️ Fix Status:");
        $this->table(
            ['Fix Status', 'Count', 'Percentage'],
            collect($fixStats)->map(function ($count, $status) {
                $percentage = round(($count / count($this->results)) * 100, 1);
                return [$status, $count, $percentage . '%'];
            })->toArray()
        );
    }

    private function generateRecommendations()
    {
        $this->info("\n💡 RECOMMENDATIONS FOR GOOGLE SEARCH CONSOLE:");
        $this->info('=' . str_repeat('=', 60));
        
        $recommendations = [
            "1. Submit Updated Sitemaps:",
            "   - Main sitemap: {$this->baseUrl}/sitemap.xml",
            "   - Clean sitemap: {$this->baseUrl}/sitemap-clean.xml",
            "   - Products: {$this->baseUrl}/sitemap-products.xml",
            "",
            "2. Use 'Request Indexing' for Priority Pages:",
            "   - Homepage: {$this->baseUrl}/",
            "   - Products page: {$this->baseUrl}/products",
            "   - Categories page: {$this->baseUrl}/categories",
            "",
            "3. Monitor These Metrics in GSC:",
            "   - Coverage > Pending (should decrease)",
            "   - Performance > Impressions (should stabilize)",
            "   - Sitemaps > Submitted vs Indexed ratio",
            "",
            "4. Expected Timeline:",
            "   - Redirects: 1-3 days for recognition",
            "   - Sitemap processing: 3-7 days",
            "   - Full indexing: 1-4 weeks",
            "",
            "5. Weekly Actions:",
            "   - Check 'Pending' count in Coverage report",
            "   - Review any new 'Error' or 'Valid with warnings'",
            "   - Request indexing for critical new pages"
        ];
        
        foreach ($recommendations as $rec) {
            $this->line($rec);
        }
    }

    private function exportReport()
    {
        $this->info("\n📄 Exporting detailed report...");
        
        $reportData = [
            'generated_at' => Carbon::now()->toDateTimeString(),
            'total_urls' => count($this->results),
            'summary' => $this->getSummaryStats(),
            'urls' => $this->results
        ];
        
        $reportPath = storage_path('seo-reports/search-console-monitor-' . Carbon::now()->format('Y-m-d-H-i-s') . '.json');
        
        // Ensure directory exists
        if (!File::exists(dirname($reportPath))) {
            File::makeDirectory(dirname($reportPath), 0755, true);
        }
        
        File::put($reportPath, json_encode($reportData, JSON_PRETTY_PRINT));
        
        $this->line("   Report exported to: {$reportPath}");
    }

    private function getSummaryStats()
    {
        $stats = [
            'by_type' => [],
            'by_fix_status' => [],
            'by_accessibility' => []
        ];
        
        foreach ($this->results as $result) {
            // By type
            $type = $result['type'];
            if (!isset($stats['by_type'][$type])) {
                $stats['by_type'][$type] = 0;
            }
            $stats['by_type'][$type]++;
            
            // By fix status
            $fix = $result['fix_applied'];
            if (!isset($stats['by_fix_status'][$fix])) {
                $stats['by_fix_status'][$fix] = 0;
            }
            $stats['by_fix_status'][$fix]++;
            
            // By accessibility (if checked)
            if ($result['accessible'] !== null) {
                $accessible = $result['accessible'] ? 'accessible' : 'not_accessible';
                if (!isset($stats['by_accessibility'][$accessible])) {
                    $stats['by_accessibility'][$accessible] = 0;
                }
                $stats['by_accessibility'][$accessible]++;
            }
        }
        
        return $stats;
    }
} 