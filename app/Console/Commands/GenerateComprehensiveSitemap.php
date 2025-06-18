<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class GenerateComprehensiveSitemap extends Command
{
    protected $signature = 'sitemap:comprehensive 
                            {--validate : Validate sitemap structure}
                            {--submit : Submit to search engines}';
    protected $description = 'Generate comprehensive sitemap with all URLs, priorities, and metadata';

    public function handle()
    {
        $this->info('🗺️  Generating comprehensive sitemap for MaxMed UAE...');

        // Generate all sitemaps
        $this->generateMainSitemap();
        $this->generateProductsSitemap();
        $this->generateCategoriesSitemap();
        $this->generateNewsSitemap();
        $this->generateImageSitemap();
        $this->generateSitemapIndex();

        if ($this->option('validate')) {
            $this->validateSitemaps();
        }

        $this->info('✅ Comprehensive sitemap generation completed!');
        $this->displayStats();

        if ($this->option('submit')) {
            $this->submitToSearchEngines();
        }
    }

    private function generateMainSitemap()
    {
        $this->info('📄 Generating main pages sitemap...');
        
        $urls = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/products', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/categories', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/news', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => '/partners', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/industries', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/quotation', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        $sitemap = $this->createSitemapHeader();
        
        foreach ($urls as $url) {
            $fullUrl = url($url['url']);
            $lastmod = Carbon::now()->toAtomString();
            
            $sitemap .= "    <url>\n";
            $sitemap .= "        <loc>{$fullUrl}</loc>\n";
            $sitemap .= "        <lastmod>{$lastmod}</lastmod>\n";
            $sitemap .= "        <changefreq>{$url['changefreq']}</changefreq>\n";
            $sitemap .= "        <priority>{$url['priority']}</priority>\n";
            $sitemap .= "    </url>\n";
        }
        
        $sitemap .= "</urlset>\n";
        
        File::put(public_path('sitemap-main.xml'), $sitemap);
    }

    private function generateProductsSitemap()
    {
        $this->info('🔬 Generating products sitemap...');
        
        $products = Product::with(['category', 'brand'])
                          ->orderBy('updated_at', 'desc')
                          ->get();

        $sitemap = $this->createSitemapHeader();
        
        foreach ($products as $product) {
            $url = route('product.show', $product->slug);
            $lastmod = $product->updated_at->toAtomString();
            
            $sitemap .= "    <url>\n";
            $sitemap .= "        <loc>{$url}</loc>\n";
            $sitemap .= "        <lastmod>{$lastmod}</lastmod>\n";
            $sitemap .= "        <changefreq>weekly</changefreq>\n";
            $sitemap .= "        <priority>0.8</priority>\n";
            
            // Add image information if available
            if ($product->image) {
                $imageUrl = asset('Images/' . $product->image);
                $sitemap .= "        <image:image>\n";
                $sitemap .= "            <image:loc>{$imageUrl}</image:loc>\n";
                $sitemap .= "            <image:title>" . htmlspecialchars($product->name) . "</image:title>\n";
                if ($product->description) {
                    $sitemap .= "            <image:caption>" . htmlspecialchars(strip_tags($product->description)) . "</image:caption>\n";
                }
                $sitemap .= "        </image:image>\n";
            }
            
            $sitemap .= "    </url>\n";
        }
        
        $sitemap .= "</urlset>\n";
        
        File::put(public_path('sitemap-products.xml'), $sitemap);
    }

    private function generateCategoriesSitemap()
    {
        $this->info('📂 Generating categories sitemap...');
        
        $categories = Category::orderBy('updated_at', 'desc')->get();

        $sitemap = $this->createSitemapHeader();
        
        foreach ($categories as $category) {
            $url = route('categories.show', $category->slug);
            $lastmod = $category->updated_at->toAtomString();
            
            $sitemap .= "    <url>\n";
            $sitemap .= "        <loc>{$url}</loc>\n";
            $sitemap .= "        <lastmod>{$lastmod}</lastmod>\n";
            $sitemap .= "        <changefreq>weekly</changefreq>\n";
            $sitemap .= "        <priority>0.7</priority>\n";
            
            // Add category image if available
            if ($category->image) {
                $imageUrl = asset('Images/' . $category->image);
                $sitemap .= "        <image:image>\n";
                $sitemap .= "            <image:loc>{$imageUrl}</image:loc>\n";
                $sitemap .= "            <image:title>" . htmlspecialchars($category->name) . "</image:title>\n";
                if ($category->description) {
                    $sitemap .= "            <image:caption>" . htmlspecialchars(strip_tags($category->description)) . "</image:caption>\n";
                }
                $sitemap .= "        </image:image>\n";
            }
            
            $sitemap .= "    </url>\n";
        }
        
        $sitemap .= "</urlset>\n";
        
        File::put(public_path('sitemap-categories.xml'), $sitemap);
    }

    private function generateNewsSitemap()
    {
        $this->info('📰 Generating news sitemap...');
        
        $news = News::orderBy('created_at', 'desc')->get();

        $sitemap = $this->createSitemapHeader();
        
        foreach ($news as $article) {
            $url = route('news.show', $article->slug);
            $lastmod = $article->updated_at->toAtomString();
            
            $sitemap .= "    <url>\n";
            $sitemap .= "        <loc>{$url}</loc>\n";
            $sitemap .= "        <lastmod>{$lastmod}</lastmod>\n";
            $sitemap .= "        <changefreq>monthly</changefreq>\n";
            $sitemap .= "        <priority>0.6</priority>\n";
            $sitemap .= "    </url>\n";
        }
        
        $sitemap .= "</urlset>\n";
        
        File::put(public_path('sitemap-news.xml'), $sitemap);
    }

    private function generateImageSitemap()
    {
        $this->info('🖼️  Generating image sitemap...');
        
        $sitemap = $this->createSitemapHeader();
        
        // Add main site images
        $mainImages = [
            ['url' => '/', 'image' => 'Images/about.png', 'title' => 'MaxMed UAE - Laboratory Equipment Supplier'],
            ['url' => '/about', 'image' => 'Images/bacteria.jpg', 'title' => 'About MaxMed UAE'],
        ];
        
        foreach ($mainImages as $item) {
            $pageUrl = url($item['url']);
            $imageUrl = asset($item['image']);
            
            $sitemap .= "    <url>\n";
            $sitemap .= "        <loc>{$pageUrl}</loc>\n";
            $sitemap .= "        <image:image>\n";
            $sitemap .= "            <image:loc>{$imageUrl}</image:loc>\n";
            $sitemap .= "            <image:title>" . htmlspecialchars($item['title']) . "</image:title>\n";
            $sitemap .= "        </image:image>\n";
            $sitemap .= "    </url>\n";
        }
        
        $sitemap .= "</urlset>\n";
        
        File::put(public_path('sitemap-images.xml'), $sitemap);
    }

    private function generateSitemapIndex()
    {
        $this->info('📋 Generating sitemap index...');
        
        $sitemaps = [
            'sitemap-main.xml',
            'sitemap-products.xml', 
            'sitemap-categories.xml',
            'sitemap-news.xml',
            'sitemap-images.xml'
        ];
        
        $index = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $index .= "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        
        foreach ($sitemaps as $sitemap) {
            $url = url($sitemap);
            $lastmod = Carbon::now()->toAtomString();
            
            $index .= "    <sitemap>\n";
            $index .= "        <loc>{$url}</loc>\n";
            $index .= "        <lastmod>{$lastmod}</lastmod>\n";
            $index .= "    </sitemap>\n";
        }
        
        $index .= "</sitemapindex>\n";
        
        File::put(public_path('sitemap.xml'), $index);
    }

    private function createSitemapHeader()
    {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
               "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" " .
               "xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\" " .
               "xmlns:news=\"http://www.google.com/schemas/sitemap-news/0.9\">\n";
    }

    private function validateSitemaps()
    {
        $this->info('🔍 Validating sitemap structure...');
        
        $sitemaps = ['sitemap.xml', 'sitemap-main.xml', 'sitemap-products.xml', 
                    'sitemap-categories.xml', 'sitemap-news.xml', 'sitemap-images.xml'];
        
        foreach ($sitemaps as $sitemap) {
            $path = public_path($sitemap);
            if (File::exists($path)) {
                $content = File::get($path);
                if (strpos($content, '<?xml') === 0) {
                    $this->line("✅ {$sitemap} - Valid XML structure");
                } else {
                    $this->error("❌ {$sitemap} - Invalid XML structure");
                }
            } else {
                $this->error("❌ {$sitemap} - File not found");
            }
        }
    }

    private function displayStats()
    {
        $stats = [
            'Main Pages' => $this->countUrlsInSitemap('sitemap-main.xml'),
            'Products' => $this->countUrlsInSitemap('sitemap-products.xml'),
            'Categories' => $this->countUrlsInSitemap('sitemap-categories.xml'),
            'News' => $this->countUrlsInSitemap('sitemap-news.xml'),
            'Images' => $this->countUrlsInSitemap('sitemap-images.xml'),
        ];
        
        $total = array_sum($stats);
        
        $this->info("\n📊 Sitemap Statistics:");
        foreach ($stats as $type => $count) {
            $this->line("   {$type}: {$count} URLs");
        }
        $this->info("   Total URLs: {$total}");
        
        $this->info("\n🔗 Submit this URL to Google Search Console:");
        $this->line("   https://maxmedme.com/sitemap.xml");
    }

    private function countUrlsInSitemap($filename)
    {
        $path = public_path($filename);
        if (!File::exists($path)) return 0;
        
        $content = File::get($path);
        return substr_count($content, '<url>');
    }

    private function submitToSearchEngines()
    {
        $this->info('🚀 Submitting sitemap to search engines...');
        
        $sitemapUrl = urlencode(url('sitemap.xml'));
        
        // Google
        $googleUrl = "http://www.google.com/ping?sitemap={$sitemapUrl}";
        $this->line("Google: {$googleUrl}");
        
        // Bing
        $bingUrl = "http://www.bing.com/ping?sitemap={$sitemapUrl}";
        $this->line("Bing: {$bingUrl}");
        
        $this->info("📝 Manual submission still recommended via:");
        $this->line("   - Google Search Console");
        $this->line("   - Bing Webmaster Tools");
    }
} 