<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixSearchConsoleUrls extends Command
{
    protected $signature = 'seo:fix-search-console-urls';
    protected $description = 'Fix all pending URLs in Google Search Console by creating proper redirects and sitemaps';

    private $baseUrl = 'https://maxmedme.com';
    private $problematicUrls = [];
    private $fixedUrls = [];

    public function handle()
    {
        $this->info('🔧 Fixing Google Search Console Pending URLs...');
        $this->info('=' . str_repeat('=', 50));
        
        // Load the problematic URLs from CSV if available
        $this->loadProblematicUrls();
        
        // Fix product URLs
        $this->fixProductUrls();
        
        // Fix category URLs
        $this->fixCategoryUrls();
        
        // Fix quotation URLs
        $this->fixQuotationUrls();
        
        // Generate clean sitemap
        $this->generateCleanSitemap();
        
        // Update .htaccess with redirects
        $this->updateHtaccess();
        
        // Display results
        $this->displayResults();
        
        return 0;
    }

    private function loadProblematicUrls()
    {
        $csvPath = base_path('Table.csv');
        if (File::exists($csvPath)) {
            $this->info('📂 Loading problematic URLs from CSV...');
            $csv = array_map('str_getcsv', file($csvPath));
            array_shift($csv); // Remove header
            
            foreach ($csv as $row) {
                if (isset($row[0]) && $row[0]) {
                    $url = parse_url($row[0], PHP_URL_PATH);
                    $this->problematicUrls[] = $url;
                }
            }
            
            $this->line('   Found ' . count($this->problematicUrls) . ' problematic URLs');
        }
    }

    private function fixProductUrls()
    {
        $this->info('🔬 Fixing product URLs...');
        
        $productIds = [];
        foreach ($this->problematicUrls as $url) {
            if (preg_match('/\/product\/(\d+)/', $url, $matches)) {
                $productIds[] = (int)$matches[1];
            }
        }
        
        $uniqueProductIds = array_unique($productIds);
        $this->line('   Found ' . count($uniqueProductIds) . ' product IDs to fix');
        
        foreach ($uniqueProductIds as $productId) {
            $product = Product::find($productId);
            if ($product) {
                if (!$product->slug) {
                    $product->slug = $this->generateSlug($product->name);
                    $product->save();
                }
                
                $oldUrl = "/product/{$productId}";
                $newUrl = "/products/{$product->slug}";
                $this->fixedUrls[] = [
                    'old' => $this->baseUrl . $oldUrl,
                    'new' => $this->baseUrl . $newUrl,
                    'type' => 'product'
                ];
            } else {
                // Product doesn't exist, redirect to products page
                $this->fixedUrls[] = [
                    'old' => $this->baseUrl . "/product/{$productId}",
                    'new' => $this->baseUrl . '/products',
                    'type' => 'product_404'
                ];
            }
        }
    }

    private function fixCategoryUrls()
    {
        $this->info('📂 Fixing category URLs...');
        
        $categoryIds = [];
        foreach ($this->problematicUrls as $url) {
            if (preg_match('/\/categories\/(\d+)/', $url, $matches)) {
                $categoryIds[] = (int)$matches[1];
            }
        }
        
        $uniqueCategoryIds = array_unique($categoryIds);
        $this->line('   Found ' . count($uniqueCategoryIds) . ' category IDs to fix');
        
        foreach ($uniqueCategoryIds as $categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                if (!$category->slug) {
                    $category->slug = $this->generateSlug($category->name);
                    $category->save();
                }
                
                $oldUrl = "/categories/{$categoryId}";
                $newUrl = "/categories/{$category->slug}";
                $this->fixedUrls[] = [
                    'old' => $this->baseUrl . $oldUrl,
                    'new' => $this->baseUrl . $newUrl,
                    'type' => 'category'
                ];
            } else {
                // Category doesn't exist, redirect to categories page
                $this->fixedUrls[] = [
                    'old' => $this->baseUrl . "/categories/{$categoryId}",
                    'new' => $this->baseUrl . '/categories',
                    'type' => 'category_404'
                ];
            }
        }
    }

    private function fixQuotationUrls()
    {
        $this->info('💬 Fixing quotation URLs...');
        
        $quotationIds = [];
        foreach ($this->problematicUrls as $url) {
            if (preg_match('/\/quotation\/(\d+)/', $url, $matches)) {
                $quotationIds[] = (int)$matches[1];
            }
        }
        
        $uniqueQuotationIds = array_unique($quotationIds);
        $this->line('   Found ' . count($uniqueQuotationIds) . ' quotation IDs to fix');
        
        foreach ($uniqueQuotationIds as $quotationId) {
            $product = Product::find($quotationId);
            if ($product && $product->slug) {
                $oldUrl = "/quotation/{$quotationId}";
                $newUrl = "/quotation/{$product->slug}";
                $this->fixedUrls[] = [
                    'old' => $this->baseUrl . $oldUrl,
                    'new' => $this->baseUrl . $newUrl,
                    'type' => 'quotation'
                ];
            } else {
                // Redirect to main quotation form
                $this->fixedUrls[] = [
                    'old' => $this->baseUrl . "/quotation/{$quotationId}",
                    'new' => $this->baseUrl . '/quotation/form',
                    'type' => 'quotation_404'
                ];
            }
        }
    }

    private function generateCleanSitemap()
    {
        $this->info('🗺️ Generating clean sitemap...');
        
        // Generate main sitemap
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Add main pages
        $mainPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/products', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/categories', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/news', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['url' => '/quotation/form', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];
        
        foreach ($mainPages as $page) {
            $xml .= '    <url>' . "\n";
            $xml .= '        <loc>' . $this->baseUrl . $page['url'] . '</loc>' . "\n";
            $xml .= '        <lastmod>' . Carbon::now()->toAtomString() . '</lastmod>' . "\n";
            $xml .= '        <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
            $xml .= '        <priority>' . $page['priority'] . '</priority>' . "\n";
            $xml .= '    </url>' . "\n";
        }
        
        // Add products with slugs only
        Product::whereNotNull('slug')->chunk(100, function ($products) use (&$xml) {
            foreach ($products as $product) {
                $xml .= '    <url>' . "\n";
                $xml .= '        <loc>' . $this->baseUrl . '/products/' . $product->slug . '</loc>' . "\n";
                $xml .= '        <lastmod>' . $product->updated_at->toAtomString() . '</lastmod>' . "\n";
                $xml .= '        <changefreq>weekly</changefreq>' . "\n";
                $xml .= '        <priority>0.8</priority>' . "\n";
                $xml .= '    </url>' . "\n";
            }
        });
        
        $xml .= '</urlset>' . "\n";
        
        File::put(public_path('sitemap-clean.xml'), $xml);
        $this->line('   Clean sitemap generated: sitemap-clean.xml');
    }

    private function updateHtaccess()
    {
        $this->info('⚙️ Updating .htaccess with redirects...');
        
        $htaccessPath = public_path('.htaccess');
        $content = File::get($htaccessPath);
        
        // Add redirect rules
        $redirectRules = "\n# Search Console URL Fixes\n";
        
        foreach ($this->fixedUrls as $fix) {
            $oldPath = parse_url($fix['old'], PHP_URL_PATH);
            $newPath = parse_url($fix['new'], PHP_URL_PATH);
            
            if ($oldPath !== $newPath) {
                $redirectRules .= "RewriteRule ^" . ltrim($oldPath, '/') . "$ " . $newPath . " [R=301,L]\n";
            }
        }
        
        // Check if these rules already exist
        if (!str_contains($content, '# Search Console URL Fixes')) {
            $content .= $redirectRules;
            File::put($htaccessPath, $content);
            $this->line('   .htaccess updated with ' . count($this->fixedUrls) . ' redirects');
        } else {
            $this->line('   .htaccess already contains redirect rules');
        }
    }

    private function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    private function displayResults()
    {
        $this->info("\n" . '📊 SEARCH CONSOLE URL FIX RESULTS');
        $this->info('=' . str_repeat('=', 50));
        
        $this->info("\n🔗 Fixed URLs by Type:");
        $types = ['product', 'category', 'quotation', 'product_404', 'category_404', 'quotation_404'];
        
        foreach ($types as $type) {
            $count = count(array_filter($this->fixedUrls, fn($fix) => $fix['type'] === $type));
            if ($count > 0) {
                $this->line("   {$type}: {$count} URLs");
            }
        }
        
        $this->info("\n📋 Next Steps:");
        $this->line("1. Submit the clean sitemap to Google Search Console");
        $this->line("2. Use 'Request Indexing' for critical pages");
        $this->line("3. Monitor crawl stats for improvements");
        $this->line("4. Check Coverage report for 'Pending' status changes");
        
        $this->info("\n✅ All " . count($this->fixedUrls) . " problematic URLs have been fixed!");
    }
} 