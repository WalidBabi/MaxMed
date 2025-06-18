<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class DiscoverAllUrls extends Command
{
    protected $signature = 'seo:discover-urls 
                            {--export=console : Export format (console, json, csv, txt)}
                            {--include-dynamic : Include dynamic URLs with parameters}
                            {--include-auth : Include authenticated URLs}';
    protected $description = 'Discover all URLs on the MaxMed website for comprehensive sitemap generation';

    private $baseUrl = 'https://maxmedme.com';
    private $allUrls = [];

    public function handle()
    {
        $this->info('🔍 Discovering all URLs on MaxMed UAE website...');

        // Discover different types of URLs
        $this->discoverStaticRoutes();
        $this->discoverProductUrls();
        $this->discoverCategoryUrls();
        $this->discoverNewsUrls();
        $this->discoverQuotationUrls();
        $this->discoverSearchUrls();
        $this->discoverRssUrls();
        $this->discoverAssetUrls();
        
        if ($this->option('include-auth')) {
            $this->discoverAuthenticatedUrls();
        }

        if ($this->option('include-dynamic')) {
            $this->discoverDynamicUrls();
        }

        $this->displayResults();
        $this->exportResults();

        return 0;
    }

    private function discoverStaticRoutes()
    {
        $this->info('📄 Discovering static routes...');

        $staticUrls = [
            // Main pages
            ['url' => '/', 'type' => 'homepage', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/about', 'type' => 'static', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'type' => 'static', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/privacy-policy', 'type' => 'static', 'priority' => '0.5', 'changefreq' => 'yearly'],
            
            // Product & Category pages
            ['url' => '/products', 'type' => 'listing', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/categories', 'type' => 'listing', 'priority' => '0.9', 'changefreq' => 'weekly'],
            
            // Industry & Partners
            ['url' => '/industries', 'type' => 'static', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/partners', 'type' => 'static', 'priority' => '0.6', 'changefreq' => 'monthly'],
            
            // News & Content
            ['url' => '/news', 'type' => 'listing', 'priority' => '0.7', 'changefreq' => 'daily'],
            
            // Functional pages
            ['url' => '/cart', 'type' => 'functional', 'priority' => '0.6', 'changefreq' => 'never'],
            ['url' => '/search', 'type' => 'functional', 'priority' => '0.7', 'changefreq' => 'never'],
        ];

        foreach ($staticUrls as $url) {
            $this->addUrl($url);
        }

        $this->line("   Found " . count($staticUrls) . " static URLs");
    }

    private function discoverProductUrls()
    {
        $this->info('🔬 Discovering product URLs...');

        $products = Product::select('id', 'slug', 'name', 'updated_at')->get();
        
        foreach ($products as $product) {
            $this->addUrl([
                'url' => '/products/' . $product->slug,
                'type' => 'product',
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => $product->updated_at,
                'title' => $product->name,
                'id' => $product->id
            ]);

            // Add quotation URLs for each product
            $this->addUrl([
                'url' => '/quotation/' . $product->slug,
                'type' => 'quotation',
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'title' => 'Quote for ' . $product->name,
                'id' => $product->id
            ]);

            $this->addUrl([
                'url' => '/quotation/' . $product->slug . '/form',
                'type' => 'quotation-form',
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'title' => 'Quote Form for ' . $product->name,
                'id' => $product->id
            ]);
        }

        $this->line("   Found " . ($products->count() * 3) . " product-related URLs");
    }

    private function discoverCategoryUrls()
    {
        $this->info('📂 Discovering category URLs...');

        $categories = Category::with(['subcategories.subcategories.subcategories'])
                             ->select('id', 'slug', 'name', 'updated_at', 'parent_id')
                             ->get();

        $categoryCount = 0;

        foreach ($categories as $category) {
            // Main category
            $this->addUrl([
                'url' => '/categories/' . $category->slug,
                'type' => 'category',
                'priority' => '0.7',
                'changefreq' => 'weekly',
                'lastmod' => $category->updated_at,
                'title' => $category->name,
                'id' => $category->id
            ]);
            $categoryCount++;

            // Subcategories (level 2)
            if ($category->subcategories) {
                foreach ($category->subcategories as $subcategory) {
                    $this->addUrl([
                        'url' => '/categories/' . $category->slug . '/' . $subcategory->slug,
                        'type' => 'subcategory',
                        'priority' => '0.6',
                        'changefreq' => 'weekly',
                        'lastmod' => $subcategory->updated_at,
                        'title' => $subcategory->name,
                        'id' => $subcategory->id
                    ]);
                    $categoryCount++;

                    // Sub-subcategories (level 3)
                    if ($subcategory->subcategories) {
                        foreach ($subcategory->subcategories as $subsubcategory) {
                            $this->addUrl([
                                'url' => '/categories/' . $category->slug . '/' . $subcategory->slug . '/' . $subsubcategory->slug,
                                'type' => 'subsubcategory',
                                'priority' => '0.5',
                                'changefreq' => 'weekly',
                                'lastmod' => $subsubcategory->updated_at,
                                'title' => $subsubcategory->name,
                                'id' => $subsubcategory->id
                            ]);
                            $categoryCount++;

                            // Sub-sub-subcategories (level 4)
                            if ($subsubcategory->subcategories) {
                                foreach ($subsubcategory->subcategories as $subsubsubcategory) {
                                    $this->addUrl([
                                        'url' => '/categories/' . $category->slug . '/' . $subcategory->slug . '/' . $subsubcategory->slug . '/' . $subsubsubcategory->slug,
                                        'type' => 'subsubsubcategory',
                                        'priority' => '0.4',
                                        'changefreq' => 'weekly',
                                        'lastmod' => $subsubsubcategory->updated_at,
                                        'title' => $subsubsubcategory->name,
                                        'id' => $subsubsubcategory->id
                                    ]);
                                    $categoryCount++;
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->line("   Found {$categoryCount} category URLs");
    }

    private function discoverNewsUrls()
    {
        $this->info('📰 Discovering news URLs...');

        $news = News::select('id', 'title', 'updated_at')->get();
        
        foreach ($news as $article) {
            $this->addUrl([
                'url' => '/news/' . $article->id,
                'type' => 'news',
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'lastmod' => $article->updated_at,
                'title' => $article->title
            ]);
        }

        $this->line("   Found " . $news->count() . " news URLs");
    }

    private function discoverQuotationUrls()
    {
        $this->info('💬 Discovering quotation URLs...');

        $quotationUrls = [
            ['url' => '/quotation', 'type' => 'quotation-main', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        foreach ($quotationUrls as $url) {
            $this->addUrl($url);
        }

        $this->line("   Found " . count($quotationUrls) . " quotation URLs");
    }

    private function discoverSearchUrls()
    {
        $this->info('🔍 Discovering search URLs...');

        // Common search terms for laboratory equipment
        $searchTerms = [
            'microscope', 'centrifuge', 'spectrophotometer', 'autoclave', 
            'pipette', 'balance', 'incubator', 'ph-meter', 'analytical-chemistry',
            'laboratory-consumables', 'medical-equipment', 'research-equipment'
        ];

        foreach ($searchTerms as $term) {
            $this->addUrl([
                'url' => '/search?q=' . $term,
                'type' => 'search',
                'priority' => '0.5',
                'changefreq' => 'weekly',
                'title' => 'Search results for ' . $term
            ]);
        }

        $this->line("   Found " . count($searchTerms) . " search URLs");
    }

    private function discoverRssUrls()
    {
        $this->info('📡 Discovering RSS URLs...');

        $rssUrls = [
            ['url' => '/rss/feed.xml', 'type' => 'rss', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['url' => '/rss/products.xml', 'type' => 'rss', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['url' => '/rss/news.xml', 'type' => 'rss', 'priority' => '0.6', 'changefreq' => 'daily'],
        ];

        foreach ($rssUrls as $url) {
            $this->addUrl($url);
        }

        $this->line("   Found " . count($rssUrls) . " RSS URLs");
    }

    private function discoverAssetUrls()
    {
        $this->info('🖼️ Discovering important asset URLs...');

        $assetUrls = [
            ['url' => '/sitemap.xml', 'type' => 'sitemap', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/robots.txt', 'type' => 'robots', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/site.webmanifest', 'type' => 'manifest', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        foreach ($assetUrls as $url) {
            $this->addUrl($url);
        }

        $this->line("   Found " . count($assetUrls) . " asset URLs");
    }

    private function discoverAuthenticatedUrls()
    {
        $this->info('🔐 Discovering authenticated URLs...');

        $authUrls = [
            ['url' => '/login', 'type' => 'auth', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => '/register', 'type' => 'auth', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => '/dashboard', 'type' => 'auth', 'priority' => '0.3', 'changefreq' => 'never'],
            ['url' => '/profile', 'type' => 'auth', 'priority' => '0.3', 'changefreq' => 'never'],
        ];

        foreach ($authUrls as $url) {
            $this->addUrl($url);
        }

        $this->line("   Found " . count($authUrls) . " authenticated URLs");
    }

    private function discoverDynamicUrls()
    {
        $this->info('⚡ Discovering dynamic URLs...');

        // Pagination URLs for products
        for ($page = 2; $page <= 10; $page++) {
            $this->addUrl([
                'url' => '/products?page=' . $page,
                'type' => 'pagination',
                'priority' => '0.6',
                'changefreq' => 'weekly',
                'title' => 'Products Page ' . $page
            ]);
        }

        // Filter URLs
        $filters = ['price-low-high', 'price-high-low', 'newest', 'popular'];
        foreach ($filters as $filter) {
            $this->addUrl([
                'url' => '/products?sort=' . $filter,
                'type' => 'filter',
                'priority' => '0.5',
                'changefreq' => 'weekly',
                'title' => 'Products sorted by ' . $filter
            ]);
        }

        $this->line("   Found " . (10 + count($filters)) . " dynamic URLs");
    }

    private function addUrl($urlData)
    {
        $urlData['full_url'] = $this->baseUrl . $urlData['url'];
        $this->allUrls[] = $urlData;
    }

    private function displayResults()
    {
        $totalUrls = count($this->allUrls);
        $typeStats = [];

        foreach ($this->allUrls as $url) {
            $type = $url['type'];
            $typeStats[$type] = isset($typeStats[$type]) ? $typeStats[$type] + 1 : 1;
        }

        $this->info("\n📊 URL Discovery Results:");
        $this->info("Total URLs discovered: {$totalUrls}");
        $this->line("\n📋 Breakdown by type:");

        foreach ($typeStats as $type => $count) {
            $this->line("   {$type}: {$count} URLs");
        }

        // Show high-priority URLs
        $highPriorityUrls = array_filter($this->allUrls, function($url) {
            return floatval($url['priority'] ?? 0) >= 0.8;
        });

        $this->info("\n🔥 High Priority URLs (" . count($highPriorityUrls) . "):");
        foreach (array_slice($highPriorityUrls, 0, 10) as $url) {
            $this->line("   {$url['full_url']} (Priority: {$url['priority']})");
        }
    }

    private function exportResults()
    {
        $format = $this->option('export');
        
        switch ($format) {
            case 'json':
                $this->exportJson();
                break;
            case 'csv':
                $this->exportCsv();
                break;
            case 'txt':
                $this->exportTxt();
                break;
            default:
                // Console output already shown
                break;
        }
    }

    private function exportJson()
    {
        $filename = 'storage/seo-audit/all-urls-' . date('Y-m-d-H-i-s') . '.json';
        File::put($filename, json_encode($this->allUrls, JSON_PRETTY_PRINT));
        $this->info("📄 URLs exported to: {$filename}");
    }

    private function exportCsv()
    {
        $filename = 'storage/seo-audit/all-urls-' . date('Y-m-d-H-i-s') . '.csv';
        $csv = "URL,Full URL,Type,Priority,Change Frequency,Title\n";
        
        foreach ($this->allUrls as $url) {
            $csv .= sprintf('"%s","%s","%s","%s","%s","%s"' . "\n",
                $url['url'],
                $url['full_url'],
                $url['type'],
                $url['priority'] ?? '',
                $url['changefreq'] ?? '',
                $url['title'] ?? ''
            );
        }
        
        File::put($filename, $csv);
        $this->info("📊 URLs exported to: {$filename}");
    }

    private function exportTxt()
    {
        $filename = 'storage/seo-audit/all-urls-' . date('Y-m-d-H-i-s') . '.txt';
        $txt = "MaxMed UAE - All Website URLs\n";
        $txt .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $txt .= "Total URLs: " . count($this->allUrls) . "\n\n";
        
        foreach ($this->allUrls as $url) {
            $txt .= $url['full_url'] . "\n";
        }
        
        File::put($filename, $txt);
        $this->info("📝 URLs exported to: {$filename}");
    }
} 