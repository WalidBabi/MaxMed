<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\Storage;

class GenerateSitemaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate comprehensive sitemaps for better SEO';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemaps...');
        
        $this->generateMainSitemap();
        $this->generateProductSitemap();
        $this->generateCategorySitemap();
        $this->generateNewsSitemap();
        
        $this->info('All sitemaps generated successfully!');
        return 0;
    }
    
    private function generateMainSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        $sitemaps = [
            'sitemap-product.xml',
            'sitemap-categories.xml',
            'sitemap-news.xml'
        ];
        
        foreach ($sitemaps as $sitemap) {
            $xml .= '  <sitemap>' . PHP_EOL;
            $xml .= '    <loc>https://maxmedme.com/' . $sitemap . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . now()->toISOString() . '</lastmod>' . PHP_EOL;
            $xml .= '  </sitemap>' . PHP_EOL;
        }
        
        // Add main pages
        $mainPages = [
            ['url' => '', 'priority' => '1.0'],
            ['url' => 'about', 'priority' => '0.8'],
            ['url' => 'contact', 'priority' => '0.8'],
            ['url' => 'products', 'priority' => '0.9'],
            ['url' => 'news', 'priority' => '0.7'],
        ];
        
        $xml .= '</sitemapindex>';
        
        file_put_contents(public_path('sitemap.xml'), $xml);
        $this->info('Main sitemap generated');
    }
    
    private function generateProductSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        Product::chunk(100, function ($products) use (&$xml) {
            foreach ($products as $product) {
                if ($product->slug) {
                    $xml .= '  <url>' . PHP_EOL;
                    $xml .= '    <loc>https://maxmedme.com/products/' . $product->slug . '</loc>' . PHP_EOL;
                    $xml .= '    <lastmod>' . $product->updated_at->toISOString() . '</lastmod>' . PHP_EOL;
                    $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
                    $xml .= '    <priority>0.8</priority>' . PHP_EOL;
                    $xml .= '  </url>' . PHP_EOL;
                }
            }
        });
        
        $xml .= '</urlset>';
        
        file_put_contents(public_path('sitemap-product.xml'), $xml);
        $this->info('Product sitemap generated');
    }
    
    private function generateCategorySitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        Category::chunk(50, function ($categories) use (&$xml) {
            foreach ($categories as $category) {
                $xml .= '  <url>' . PHP_EOL;
                $xml .= '    <loc>https://maxmedme.com/categories/' . $category->id . '</loc>' . PHP_EOL;
                $xml .= '    <lastmod>' . $category->updated_at->toISOString() . '</lastmod>' . PHP_EOL;
                $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
                $xml .= '    <priority>0.7</priority>' . PHP_EOL;
                $xml .= '  </url>' . PHP_EOL;
            }
        });
        
        $xml .= '</urlset>';
        
        file_put_contents(public_path('sitemap-categories.xml'), $xml);
        $this->info('Category sitemap generated');
    }
    
    private function generateNewsSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        if (class_exists(News::class)) {
            News::chunk(50, function ($newsItems) use (&$xml) {
                foreach ($newsItems as $news) {
                    $xml .= '  <url>' . PHP_EOL;
                    $xml .= '    <loc>https://maxmedme.com/news/' . $news->slug . '</loc>' . PHP_EOL;
                    $xml .= '    <lastmod>' . $news->updated_at->toISOString() . '</lastmod>' . PHP_EOL;
                    $xml .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
                    $xml .= '    <priority>0.6</priority>' . PHP_EOL;
                    $xml .= '  </url>' . PHP_EOL;
                }
            });
        }
        
        $xml .= '</urlset>';
        
        file_put_contents(public_path('sitemap-news.xml'), $xml);
        $this->info('News sitemap generated');
    }
}
