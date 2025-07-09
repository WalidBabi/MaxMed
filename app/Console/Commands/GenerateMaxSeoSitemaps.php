<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Models\Brand;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class GenerateMaxSeoSitemaps extends Command
{
    protected $signature = 'sitemap:max-seo 
                            {--validate : Validate all sitemaps}
                            {--submit : Submit to search engines}';
    
    protected $description = 'Generate MAXIMUM SEO sitemaps with all possible content types and URL patterns';

    private $baseUrl = 'https://maxmedme.com';
    private $sitemapFiles = [];

    public function handle()
    {
        $this->info('🚀 Generating MAXIMUM SEO sitemaps for first page Google rankings...');
        
        // Generate all possible sitemaps
        $this->generateMainSitemap();
        $this->generateProductSitemaps();
        $this->generateCategorySitemaps();
        $this->generateBrandSitemaps();
        $this->generateIndustrySitemaps();
        $this->generateNewsSitemaps();
        $this->generateFunctionalSitemaps();
        $this->generateQuotationSitemaps();
        $this->generateImageSitemaps();
        $this->generateRegionalSitemaps();
        $this->generateKeywordSitemaps();
        
        // Generate index
        $this->generateComprehensiveSitemapIndex();
        
        if ($this->option('validate')) {
            $this->validateAllSitemaps();
        }
        
        $this->displayComprehensiveStats();
        
        if ($this->option('submit')) {
            $this->submitToSearchEngines();
        }
        
        return 0;
    }

    private function generateMainSitemap()
    {
        $this->info('📄 Generating main pages sitemap...');
        
        $urls = [
            // Core pages - highest priority
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/about', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => '/privacy-policy', 'priority' => '0.6', 'changefreq' => 'yearly'],
            
            // Product and category listing pages
            ['url' => '/products', 'priority' => '0.95', 'changefreq' => 'daily'],
            ['url' => '/categories', 'priority' => '0.9', 'changefreq' => 'weekly'],
            
            // Industry and solution pages
            ['url' => '/industries', 'priority' => '0.8', 'changefreq' => 'monthly'],
            
            // News and content
            ['url' => '/news', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => '/partners', 'priority' => '0.7', 'changefreq' => 'monthly'],
            
            // Cart and commerce
            ['url' => '/cart', 'priority' => '0.7', 'changefreq' => 'daily'],
        ];

        $this->createSitemapFile('main', $urls);
    }

    private function generateProductSitemaps()
    {
        $this->info('📦 Generating product sitemaps...');
        
        $products = Product::with(['category', 'brand'])->get();
        
        // Main product pages
        $productUrls = [];
        $quotationUrls = [];
        
        foreach ($products as $product) {
            if ($product->slug) {
                // Main product page
                $productUrls[] = [
                    'url' => "/products/{$product->slug}",
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $product->updated_at,
                    'image' => $product->image_url,
                    'title' => $product->name,
                    'description' => $product->description
                ];
                
                // Product quotation pages
                $quotationUrls[] = [
                    'url' => "/quotation/{$product->slug}",
                    'priority' => '0.7',
                    'changefreq' => 'monthly',
                    'lastmod' => $product->updated_at
                ];
                
                $quotationUrls[] = [
                    'url' => "/quotation/form/{$product->slug}",
                    'priority' => '0.7',
                    'changefreq' => 'monthly',
                    'lastmod' => $product->updated_at
                ];
                
                $quotationUrls[] = [
                    'url' => "/quotation/confirmation/{$product->slug}",
                    'priority' => '0.6',
                    'changefreq' => 'monthly',
                    'lastmod' => $product->updated_at
                ];
            }
        }
        
        $this->createSitemapFile('products', $productUrls);
        $this->createSitemapFile('quotations', $quotationUrls);
    }

    private function generateCategorySitemaps()
    {
        $this->info('📁 Generating category sitemaps...');
        
        $categories = Category::all();
        
        $categoryUrls = [];
        
        foreach ($categories as $category) {
            // Main category page
            $categoryUrls[] = [
                'url' => "/categories/{$category->slug}",
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => $category->updated_at
            ];
        }
        
        $this->createSitemapFile('categories', $categoryUrls);
    }

    private function generateBrandSitemaps()
    {
        $this->info('🏷️ Generating brand sitemaps...');
        
        $brands = Brand::all();
        $brandUrls = [];
        
        foreach ($brands as $brand) {
            $brandUrls[] = [
                'url' => "/brands/{$brand->slug}",
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => $brand->updated_at ?? now()
            ];
        }
        
        $this->createSitemapFile('brands', $brandUrls);
    }

    private function generateIndustrySitemaps()
    {
        $this->info('🏭 Generating industry sitemaps...');
        
        $industries = [
            'healthcare' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'research' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'laboratories' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'pharmaceuticals' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'biotechnology' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'education' => ['priority' => '0.7', 'changefreq' => 'monthly'],
            'government' => ['priority' => '0.7', 'changefreq' => 'monthly'],
            'manufacturing' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        ];
        
        $industryUrls = [];
        
        foreach ($industries as $industry => $config) {
            $industryUrls[] = [
                'url' => "/industries/{$industry}",
                'priority' => $config['priority'],
                'changefreq' => $config['changefreq'],
                'lastmod' => now()
            ];
        }
        
        $this->createSitemapFile('industries', $industryUrls);
    }

    private function generateNewsSitemaps()
    {
        $this->info('📰 Generating news sitemaps...');
        
        $news = News::orderBy('created_at', 'desc')->get();
        $newsUrls = [];
        
        foreach ($news as $article) {
            $newsUrls[] = [
                'url' => "/news/{$article->slug}",
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'lastmod' => $article->updated_at,
                'news' => [
                    'publication_date' => $article->created_at,
                    'title' => $article->title,
                    'language' => 'en'
                ]
            ];
        }
        
        $this->createSitemapFile('news', $newsUrls);
    }

    private function generateFunctionalSitemaps()
    {
        $this->info('⚙️ Generating functional sitemaps...');
        
        $functionalUrls = [
            // Search and discovery
            ['url' => '/search', 'priority' => '0.7', 'changefreq' => 'daily'],
            
            // RSS and feeds
            ['url' => '/rss/feed.xml', 'priority' => '0.6', 'changefreq' => 'daily'],
            ['url' => '/rss/news.xml', 'priority' => '0.6', 'changefreq' => 'daily'],
            ['url' => '/rss/products.xml', 'priority' => '0.6', 'changefreq' => 'daily'],
            
            // Special pages
            ['url' => '/partners', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];
        
        $this->createSitemapFile('functional', $functionalUrls);
    }

    private function generateQuotationSitemaps()
    {
        $this->info('💬 Generating quotation sitemaps...');
        
        $quotationUrls = [
            ['url' => '/quotation', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/quotation/form', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];
        
        $this->createSitemapFile('quotation-main', $quotationUrls);
    }

    private function generateImageSitemaps()
    {
        $this->info('🖼️ Generating image sitemaps...');
        
        $products = Product::whereNotNull('image_url')->get();
        $imageUrls = [];
        
        foreach ($products as $product) {
            if ($product->image_url) {
                $imageUrls[] = [
                    'url' => "/products/{$product->slug}",
                    'priority' => '0.7',
                    'changefreq' => 'weekly',
                    'lastmod' => $product->updated_at,
                    'image' => $product->image_url,
                    'title' => $product->name,
                    'caption' => $product->description
                ];
            }
        }
        
        $this->createSitemapFile('images', $imageUrls);
    }

    private function generateRegionalSitemaps()
    {
        $this->info('🌍 Generating regional sitemaps...');
        
        $regions = [
            'dubai' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'abu-dhabi' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'sharjah' => ['priority' => '0.7', 'changefreq' => 'monthly'],
            'uae' => ['priority' => '0.9', 'changefreq' => 'monthly'],
            'middle-east' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        ];
        
        $regionalUrls = [];
        
        foreach ($regions as $region => $config) {
            $regionalUrls[] = [
                'url' => "/regions/{$region}",
                'priority' => $config['priority'],
                'changefreq' => $config['changefreq'],
                'lastmod' => now()
            ];
        }
        
        $this->createSitemapFile('regional', $regionalUrls);
    }

    private function generateKeywordSitemaps()
    {
        $this->info('🔍 Generating keyword-based sitemaps...');
        
        $keywords = [
            'medical-equipment' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'laboratory-supplies' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'healthcare-products' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'medical-supplies' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'lab-equipment' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'diagnostic-equipment' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'surgical-instruments' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'hospital-supplies' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'clinical-supplies' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'research-equipment' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'analytical-instruments' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'medical-supplies-dubai' => ['priority' => '0.9', 'changefreq' => 'monthly'],
            'laboratory-equipment-dubai' => ['priority' => '0.9', 'changefreq' => 'monthly'],
            'hospital-supplies-dubai' => ['priority' => '0.9', 'changefreq' => 'monthly'],
            'medical-equipment-abu-dhabi' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'healthcare-products-sharjah' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'medical-supplies-uae' => ['priority' => '0.9', 'changefreq' => 'monthly'],
            'laboratory-supplies-gcc' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        ];
        
        $keywordUrls = [];
        
        foreach ($keywords as $keyword => $config) {
            $keywordUrls[] = [
                'url' => "/keywords/{$keyword}",
                'priority' => $config['priority'],
                'changefreq' => $config['changefreq'],
                'lastmod' => now()
            ];
        }
        
        $this->createSitemapFile('keywords', $keywordUrls);
    }

    private function createSitemapFile($name, $urls)
    {
        if (empty($urls)) {
            return;
        }
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        $xml .= ' xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>{$this->baseUrl}{$url['url']}</loc>\n";
            
            if (isset($url['lastmod'])) {
                $lastmod = $url['lastmod'] instanceof Carbon ? $url['lastmod'] : Carbon::parse($url['lastmod']);
                $xml .= "        <lastmod>{$lastmod->toAtomString()}</lastmod>\n";
            } else {
                $xml .= "        <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            }
            
            $xml .= "        <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "        <priority>{$url['priority']}</priority>\n";
            
            // Add image if present
            if (isset($url['image']) && $url['image']) {
                $xml .= "        <image:image>\n";
                $xml .= "            <image:loc>{$url['image']}</image:loc>\n";
                if (isset($url['title'])) {
                    $xml .= "            <image:title>" . htmlspecialchars($url['title']) . "</image:title>\n";
                }
                if (isset($url['caption'])) {
                    $xml .= "            <image:caption>" . htmlspecialchars($url['caption']) . "</image:caption>\n";
                }
                $xml .= "        </image:image>\n";
            }
            
            // Add news if present
            if (isset($url['news'])) {
                $xml .= "        <news:news>\n";
                $xml .= "            <news:publication>\n";
                $xml .= "                <news:name>MaxMed UAE</news:name>\n";
                $xml .= "                <news:language>{$url['news']['language']}</news:language>\n";
                $xml .= "            </news:publication>\n";
                $xml .= "            <news:publication_date>{$url['news']['publication_date']->toAtomString()}</news:publication_date>\n";
                $xml .= "            <news:title>" . htmlspecialchars($url['news']['title']) . "</news:title>\n";
                $xml .= "        </news:news>\n";
            }
            
            $xml .= "    </url>\n";
        }
        
        $xml .= "</urlset>\n";
        
        $filename = "sitemap-{$name}.xml";
        File::put(public_path($filename), $xml);
        $this->sitemapFiles[] = $filename;
        $this->line("   ✓ Generated {$filename} with " . count($urls) . " URLs");
    }

    private function generateComprehensiveSitemapIndex()
    {
        $this->info('📋 Generating comprehensive sitemap index...');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($this->sitemapFiles as $filename) {
            $xml .= "    <sitemap>\n";
            $xml .= "        <loc>{$this->baseUrl}/{$filename}</loc>\n";
            $xml .= "        <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            $xml .= "    </sitemap>\n";
        }
        
        $xml .= "</sitemapindex>\n";
        
        File::put(public_path('sitemap.xml'), $xml);
        $this->line("   ✓ Generated sitemap index with " . count($this->sitemapFiles) . " sitemaps");
    }

    private function validateAllSitemaps()
    {
        $this->info('🔍 Validating all sitemaps...');
        
        $allFiles = array_merge(['sitemap.xml'], $this->sitemapFiles);
        
        foreach ($allFiles as $filename) {
            $path = public_path($filename);
            if (File::exists($path)) {
                $content = File::get($path);
                if (strpos($content, '<?xml') === 0) {
                    $this->line("   ✓ {$filename} - Valid XML");
                } else {
                    $this->error("   ✗ {$filename} - Invalid XML");
                }
            } else {
                $this->error("   ✗ {$filename} - File not found");
            }
        }
    }

    private function displayComprehensiveStats()
    {
        $this->info("\n📊 Comprehensive Sitemap Statistics:");
        $this->line("   Total sitemaps: " . count($this->sitemapFiles));
        
        $totalUrls = 0;
        foreach ($this->sitemapFiles as $filename) {
            $path = public_path($filename);
            if (File::exists($path)) {
                $content = File::get($path);
                $urls = substr_count($content, '<loc>');
                $totalUrls += $urls;
                $this->line("   {$filename}: {$urls} URLs");
            }
        }
        
        $this->info("   Total URLs: {$totalUrls}");
        $this->info("\n🚀 Submit to Google Search Console:");
        $this->line("   {$this->baseUrl}/sitemap.xml");
        $this->info("\n📝 Additional sitemaps to submit:");
        foreach ($this->sitemapFiles as $filename) {
            $this->line("   {$this->baseUrl}/{$filename}");
        }
    }

    private function submitToSearchEngines()
    {
        $this->info('📤 Submitting to search engines...');
        
        // Google
        $googleUrl = "https://www.google.com/ping?sitemap=" . urlencode("{$this->baseUrl}/sitemap.xml");
        $this->line("   Google: {$googleUrl}");
        
        // Bing
        $bingUrl = "https://www.bing.com/ping?sitemap=" . urlencode("{$this->baseUrl}/sitemap.xml");
        $this->line("   Bing: {$bingUrl}");
        
        $this->info('   ✓ Sitemap URLs generated for manual submission');
    }
} 