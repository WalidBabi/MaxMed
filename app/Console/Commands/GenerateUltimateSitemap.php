<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Models\Brand;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class GenerateUltimateSitemap extends Command
{
    protected $signature = 'sitemap:ultimate 
                            {--validate : Validate all sitemaps}
                            {--split : Split into multiple sitemap files}
                            {--images : Include image sitemap}';
    protected $description = 'Generate the ultimate comprehensive sitemap with ALL website URLs for maximum SEO coverage';

    private $baseUrl = 'https://maxmedme.com';
    private $allUrls = [];

    public function handle()
    {
        $this->info('🚀 Generating ULTIMATE comprehensive sitemap for MaxMed UAE...');
        $this->info('📊 This will include ALL discoverable URLs on your website for maximum SEO coverage');

        // Collect all URLs
        $this->collectAllUrls();
        
        // Generate sitemaps
        if ($this->option('split')) {
            $this->generateSplitSitemaps();
        } else {
            $this->generateSingleSitemap();
        }

        $this->generateSitemapIndex();

        if ($this->option('images')) {
            $this->generateImageSitemap();
        }

        if ($this->option('validate')) {
            $this->validateSitemaps();
        }

        $this->displayFinalStats();
        $this->showSubmissionInstructions();

        return 0;
    }

    private function collectAllUrls()
    {
        $this->info('🔍 Collecting ALL website URLs for comprehensive SEO coverage...');

        // Core pages with highest priority
        $this->addStaticUrls();
        
        // Dynamic content - products, categories, news
        $this->addProductUrls();
        $this->addCategoryUrls();
        $this->addNewsUrls();
        $this->addBrandUrls();
        
        // Functional and utility pages
        $this->addFunctionalUrls();
        $this->addIndustryUrls();
        
        // SEO and technical pages
        $this->addSeoUrls();
        
        // Authentication and user-related pages (public parts)
        $this->addAuthUrls();
        
        // API endpoints that are publicly accessible
        $this->addApiUrls();
        
        // Sort by priority (highest first)
        usort($this->allUrls, function($a, $b) {
            return floatval($b['priority'] ?? 0) <=> floatval($a['priority'] ?? 0);
        });
    }

    private function addStaticUrls()
    {
        $this->line('   🏠 Adding static pages...');
        
        $staticUrls = [
            // Core pages - highest priority
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily', 'type' => 'homepage'],
            ['url' => '/about', 'priority' => '0.9', 'changefreq' => 'monthly', 'type' => 'about'],
            ['url' => '/contact', 'priority' => '0.9', 'changefreq' => 'monthly', 'type' => 'contact'],
            ['url' => '/privacy-policy', 'priority' => '0.6', 'changefreq' => 'yearly', 'type' => 'legal'],
            
            // Product and category listing pages - very high priority
            ['url' => '/products', 'priority' => '0.95', 'changefreq' => 'daily', 'type' => 'listing'],
            ['url' => '/categories', 'priority' => '0.9', 'changefreq' => 'weekly', 'type' => 'listing'],
            
            // Industry and solution pages
            ['url' => '/industries', 'priority' => '0.8', 'changefreq' => 'monthly', 'type' => 'industries'],
            
            // Partners and news
            ['url' => '/partners', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'partners'],
            ['url' => '/news', 'priority' => '0.8', 'changefreq' => 'daily', 'type' => 'news'],
        ];

        foreach ($staticUrls as $url) {
            $this->addUrl($url);
        }
        
        $this->line("      Added " . count($staticUrls) . " static pages");
    }

    private function addProductUrls()
    {
        $this->line('   📦 Adding product URLs...');
        
        $products = Product::with(['category', 'brand'])
                          ->select('id', 'slug', 'name', 'updated_at', 'category_id', 'brand_id', 'image_url')
                          ->get();

        $urlCount = 0;
        foreach ($products as $product) {
            // Main product page - high priority
            $this->addUrl([
                'url' => '/products/' . $product->slug,
                'priority' => '0.85',
                'changefreq' => 'weekly',
                'lastmod' => $product->updated_at,
                'type' => 'product',
                'title' => $product->name,
                'image' => $product->image_url
            ]);
            $urlCount++;

            // Product quotation pages - important for lead generation
            $this->addUrl([
                'url' => '/quotation/' . $product->slug,
                'priority' => '0.75',
                'changefreq' => 'monthly',
                'type' => 'quotation',
                'title' => 'Request Quote: ' . $product->name
            ]);
            $urlCount++;

            $this->addUrl([
                'url' => '/quotation/' . $product->slug . '/form',
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'type' => 'quotation-form',
                'title' => 'Quote Form: ' . $product->name
            ]);
            $urlCount++;

            // Product availability check page
            $this->addUrl([
                'url' => '/quotation/confirmation/' . $product->slug,
                'priority' => '0.65',
                'changefreq' => 'monthly',
                'type' => 'quotation-confirmation',
                'title' => 'Quote Confirmation: ' . $product->name
            ]);
            $urlCount++;
        }

        $this->line("      Added {$urlCount} product-related URLs");
    }

    private function addCategoryUrls()
    {
        $this->line('   📁 Adding category URLs...');
        
        $categories = Category::with(['subcategories.subcategories.subcategories'])
                             ->select('id', 'slug', 'name', 'updated_at', 'parent_id')
                             ->get();

        $categoryCount = 0;

        foreach ($categories as $category) {
            if (!$category->parent_id) {
                // Main category - high priority
                $this->addUrl([
                    'url' => '/categories/' . $category->slug,
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $category->updated_at,
                    'type' => 'category',
                    'title' => $category->name . ' - Laboratory Equipment & Supplies in Dubai UAE'
                ]);
                $categoryCount++;

                // Add nested categories
                $categoryCount += $this->addNestedCategories($category, [$category->slug]);
            }
        }

        $this->line("      Added {$categoryCount} category URLs");
    }

    private function addNestedCategories($category, $slugPath, $level = 1)
    {
        $count = 0;
        $priorities = ['0.75', '0.7', '0.65', '0.6'];
        
        if ($category->subcategories && $level < 5) {
            foreach ($category->subcategories as $subcategory) {
                $newPath = array_merge($slugPath, [$subcategory->slug]);
                $url = '/categories/' . implode('/', $newPath);
                
                $this->addUrl([
                    'url' => $url,
                    'priority' => $priorities[$level - 1] ?? '0.5',
                    'changefreq' => 'weekly',
                    'lastmod' => $subcategory->updated_at,
                    'type' => 'subcategory-level-' . $level,
                    'title' => $subcategory->name . ' - Dubai UAE'
                ]);
                $count++;

                // Recursively add deeper levels
                $count += $this->addNestedCategories($subcategory, $newPath, $level + 1);
            }
        }

        return $count;
    }

    private function addNewsUrls()
    {
        $this->line('   📰 Adding news URLs...');
        
        $news = News::where('published', true)
                   ->select('id', 'title', 'updated_at')
                   ->get();
        
        foreach ($news as $article) {
            $this->addUrl([
                'url' => '/news/' . $article->id,
                'priority' => '0.65',
                'changefreq' => 'monthly',
                'lastmod' => $article->updated_at,
                'type' => 'news',
                'title' => $article->title . ' - MaxMed UAE News'
            ]);
        }

        $this->line("      Added " . $news->count() . " news URLs");
    }

    private function addBrandUrls()
    {
        $this->line('   🏷️ Adding brand URLs...');
        
        $brands = Brand::select('id', 'slug', 'name', 'updated_at')
                      ->get();
        
        $brandCount = 0;
        foreach ($brands as $brand) {
            if ($brand->slug) {
                // For now, we'll add potential brand URLs
                // You can implement brand detail pages later
                $this->addUrl([
                    'url' => '/brands/' . $brand->slug,
                    'priority' => '0.6',
                    'changefreq' => 'monthly',
                    'lastmod' => $brand->updated_at,
                    'type' => 'brand',
                    'title' => $brand->name . ' Products - Dubai UAE'
                ]);
                $brandCount++;
            }
        }

        $this->line("      Added {$brandCount} brand URLs");
    }

    private function addFunctionalUrls()
    {
        $this->line('   ⚙️ Adding functional pages...');
        
        $functionalUrls = [
            // Search and discovery
            ['url' => '/search', 'priority' => '0.7', 'changefreq' => 'never', 'type' => 'search'],
            ['url' => '/showproducts', 'priority' => '0.65', 'changefreq' => 'daily', 'type' => 'product-listing'],
            
            // Cart and commerce
            ['url' => '/cart', 'priority' => '0.6', 'changefreq' => 'never', 'type' => 'cart'],
            
            // User-related public pages
            ['url' => '/login', 'priority' => '0.5', 'changefreq' => 'yearly', 'type' => 'auth'],
            ['url' => '/register', 'priority' => '0.5', 'changefreq' => 'yearly', 'type' => 'auth'],
            ['url' => '/forgot-password', 'priority' => '0.4', 'changefreq' => 'yearly', 'type' => 'auth'],
        ];

        foreach ($functionalUrls as $url) {
            $this->addUrl($url);
        }
        
        $this->line("      Added " . count($functionalUrls) . " functional URLs");
    }

    private function addIndustryUrls()
    {
        $this->line('   🏭 Adding industry-specific URLs...');
        
        // Based on the IndustryController methods, add potential industry pages
        $industryUrls = [
            // Healthcare & Medical
            ['url' => '/industries/healthcare/clinics', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'industry'],
            ['url' => '/industries/healthcare/hospitals', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'industry'],
            ['url' => '/industries/healthcare/veterinary', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'industry'],
            ['url' => '/industries/healthcare/medical-laboratories', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'industry'],
            
            // Research & Scientific
            ['url' => '/industries/research/research-labs', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'industry'],
            ['url' => '/industries/research/academia', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'industry'],
            ['url' => '/industries/research/biotech', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'industry'],
            ['url' => '/industries/research/forensic', 'priority' => '0.7', 'changefreq' => 'monthly', 'type' => 'industry'],
            
            // Testing & Diagnostics
            ['url' => '/industries/testing/environment', 'priority' => '0.65', 'changefreq' => 'monthly', 'type' => 'industry'],
            ['url' => '/industries/testing/food', 'priority' => '0.65', 'changefreq' => 'monthly', 'type' => 'industry'],
            ['url' => '/industries/testing/materials', 'priority' => '0.65', 'changefreq' => 'monthly', 'type' => 'industry'],
        ];

        foreach ($industryUrls as $url) {
            $this->addUrl($url);
        }
        
        $this->line("      Added " . count($industryUrls) . " industry URLs");
    }

    private function addSeoUrls()
    {
        $this->line('   🔍 Adding SEO and technical URLs...');
        
        $seoUrls = [
            // Sitemap and robots
            ['url' => '/sitemap.xml', 'priority' => '0.9', 'changefreq' => 'daily', 'type' => 'sitemap'],
            ['url' => '/robots.txt', 'priority' => '0.8', 'changefreq' => 'monthly', 'type' => 'robots'],
            
            // RSS feeds
            ['url' => '/rss/feed.xml', 'priority' => '0.8', 'changefreq' => 'daily', 'type' => 'rss'],
            ['url' => '/rss/products.xml', 'priority' => '0.75', 'changefreq' => 'daily', 'type' => 'rss'],
            ['url' => '/rss/news.xml', 'priority' => '0.7', 'changefreq' => 'daily', 'type' => 'rss'],
            
            // PWA and technical
            ['url' => '/site.webmanifest', 'priority' => '0.6', 'changefreq' => 'monthly', 'type' => 'manifest'],
            ['url' => '/browserconfig.xml', 'priority' => '0.5', 'changefreq' => 'yearly', 'type' => 'config'],
        ];

        foreach ($seoUrls as $url) {
            $this->addUrl($url);
        }
        
        $this->line("      Added " . count($seoUrls) . " SEO URLs");
    }

    private function addAuthUrls()
    {
        $this->line('   🔐 Adding authentication URLs...');
        
        $authUrls = [
            ['url' => '/login/google', 'priority' => '0.4', 'changefreq' => 'yearly', 'type' => 'oauth'],
        ];

        foreach ($authUrls as $url) {
            $this->addUrl($url);
        }
        
        $this->line("      Added " . count($authUrls) . " auth URLs");
    }

    private function addApiUrls()
    {
        $this->line('   🔗 Adding public API endpoints...');
        
        // Only include publicly accessible API endpoints
        $apiUrls = [
            ['url' => '/search/suggestions', 'priority' => '0.3', 'changefreq' => 'never', 'type' => 'api'],
        ];

        foreach ($apiUrls as $url) {
            $this->addUrl($url);
        }
        
        $this->line("      Added " . count($apiUrls) . " API URLs");
    }

    private function addUrl($urlData)
    {
        $urlData['full_url'] = $this->baseUrl . $urlData['url'];
        $urlData['lastmod'] = $urlData['lastmod'] ?? Carbon::now();
        $this->allUrls[] = $urlData;
    }

    private function generateImageSitemap()
    {
        $this->info('🖼️ Generating image sitemap...');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $xml .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $imageCount = 0;
        foreach ($this->allUrls as $url) {
            if (isset($url['image']) && $url['image']) {
                $xml .= "    <url>\n";
                $xml .= "        <loc>{$url['full_url']}</loc>\n";
                $xml .= "        <image:image>\n";
                $xml .= "            <image:loc>{$url['image']}</image:loc>\n";
                if (isset($url['title'])) {
                    $xml .= "            <image:title>" . htmlspecialchars($url['title']) . "</image:title>\n";
                }
                $xml .= "        </image:image>\n";
                $xml .= "    </url>\n";
                $imageCount++;
            }
        }

        $xml .= "</urlset>\n";

        File::put(public_path('sitemap-images.xml'), $xml);
        $this->line("   ✅ Generated image sitemap with {$imageCount} images");
    }

    private function generateSplitSitemaps()
    {
        $this->info('📂 Generating split sitemaps...');
        
        // Group URLs by type
        $urlsByType = [];
        foreach ($this->allUrls as $url) {
            $type = $url['type'];
            if (!isset($urlsByType[$type])) {
                $urlsByType[$type] = [];
            }
            $urlsByType[$type][] = $url;
        }

        // Generate individual sitemaps
        $sitemapFiles = [];
        foreach ($urlsByType as $type => $urls) {
            $filename = "sitemap-{$type}.xml";
            $this->generateSitemapFile($urls, $filename);
            $sitemapFiles[] = $filename;
            $this->line("   ✅ Generated {$filename} with " . count($urls) . " URLs");
        }

        return $sitemapFiles;
    }

    private function generateSingleSitemap()
    {
        $this->info('📄 Generating single comprehensive sitemap...');
        
        // Split into chunks of 50,000 URLs (sitemap limit)
        $chunks = array_chunk($this->allUrls, 50000);
        $sitemapFiles = [];

        foreach ($chunks as $index => $chunk) {
            $filename = $index === 0 ? 'sitemap-all.xml' : "sitemap-all-{$index}.xml";
            $this->generateSitemapFile($chunk, $filename);
            $sitemapFiles[] = $filename;
            $this->line("   ✅ Generated {$filename} with " . count($chunk) . " URLs");
        }

        return $sitemapFiles;
    }

    private function generateSitemapFile($urls, $filename)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $xml .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
        $xml .= 'xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>{$url['full_url']}</loc>\n";
            $xml .= "        <lastmod>{$url['lastmod']->toAtomString()}</lastmod>\n";
            $xml .= "        <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "        <priority>{$url['priority']}</priority>\n";
            $xml .= "    </url>\n";
        }

        $xml .= "</urlset>\n";

        File::put(public_path($filename), $xml);
    }

    private function generateSitemapIndex()
    {
        $this->info('📋 Generating sitemap index...');
        
        // Find all sitemap files
        $sitemapFiles = [];
        $files = File::glob(public_path('sitemap-*.xml'));
        
        foreach ($files as $file) {
            $filename = basename($file);
            if ($filename !== 'sitemap.xml') { // Don't include the index itself
                $sitemapFiles[] = $filename;
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($sitemapFiles as $filename) {
            $xml .= "    <sitemap>\n";
            $xml .= "        <loc>{$this->baseUrl}/{$filename}</loc>\n";
            $xml .= "        <lastmod>" . Carbon::now()->toAtomString() . "</lastmod>\n";
            $xml .= "    </sitemap>\n";
        }

        $xml .= "</sitemapindex>\n";

        File::put(public_path('sitemap.xml'), $xml);
        
        $this->line("   ✅ Generated sitemap index with " . count($sitemapFiles) . " sitemaps");
    }

    private function validateSitemaps()
    {
        $this->info('🔍 Validating sitemaps...');
        
        $files = File::glob(public_path('sitemap*.xml'));
        $validCount = 0;
        
        foreach ($files as $file) {
            $filename = basename($file);
            $content = File::get($file);
            
            if (strpos($content, '<?xml') === 0 && 
                (strpos($content, '<urlset') !== false || strpos($content, '<sitemapindex') !== false)) {
                $this->line("   ✅ {$filename} - Valid");
                $validCount++;
            } else {
                $this->error("   ❌ {$filename} - Invalid XML");
            }
        }
        
        $this->info("Validation complete: {$validCount}/" . count($files) . " files valid");
    }

    private function displayFinalStats()
    {
        $totalUrls = count($this->allUrls);
        $typeStats = [];

        foreach ($this->allUrls as $url) {
            $type = $url['type'];
            $typeStats[$type] = isset($typeStats[$type]) ? $typeStats[$type] + 1 : 1;
        }

        $this->info("\n🎉 ULTIMATE SITEMAP GENERATION COMPLETE!");
        $this->info("📊 Total URLs: {$totalUrls}");
        $this->line("\n📋 URL Breakdown:");

        foreach ($typeStats as $type => $count) {
            $this->line("   {$type}: {$count} URLs");
        }

        // Show top priority URLs
        $topUrls = array_slice($this->allUrls, 0, 10);
        $this->info("\n🔥 Top Priority URLs:");
        foreach ($topUrls as $url) {
            $this->line("   {$url['full_url']} (Priority: {$url['priority']})");
        }
    }

    private function showSubmissionInstructions()
    {
        $this->info("\n📤 SUBMISSION INSTRUCTIONS:");
        $this->info("🔗 Submit this URL to Google Search Console:");
        $this->line("   https://maxmedme.com/sitemap.xml");
        
        $this->info("\n🛠️ Additional SEO Resources:");
        $this->line("   • RSS Feed: https://maxmedme.com/rss/feed.xml");
        $this->line("   • Robots.txt: https://maxmedme.com/robots.txt");
        $this->line("   • Site Manifest: https://maxmedme.com/site.webmanifest");
        
        $this->info("\n✨ Your website now has comprehensive sitemap coverage!");
        $this->line("   Google will discover " . count($this->allUrls) . " pages on your site.");
    }
} 