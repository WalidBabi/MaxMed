<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\News;
use App\Models\Category;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate comprehensive SEO-optimized sitemaps';

    public function handle()
    {
        $baseUrl = 'https://maxmedme.com';
        
        $this->info('Generating SEO-optimized sitemaps...');

        // Generate separate sitemaps for different content types
        $this->generateMainSitemap($baseUrl);
        $this->generateProductSitemap($baseUrl);
        $this->generateCategorySitemap($baseUrl);
        $this->generateNewsSitemap($baseUrl);

        // Create sitemap index
        $this->generateSitemapIndex($baseUrl);

        $this->info('✅ Comprehensive sitemap suite generated successfully!');
        $this->info('📄 Generated files:');
        $this->info('   - sitemap.xml (index)');
        $this->info('   - sitemap-main.xml');
        $this->info('   - sitemap-products.xml');
        $this->info('   - sitemap-categories.xml');
        $this->info('   - sitemap-news.xml');
    }

    private function generateMainSitemap($baseUrl)
    {
        $sitemap = Sitemap::create();

        // Add static pages with optimized priorities
        $sitemap->add(Url::create($baseUrl)
            ->setPriority(1.0)
            ->setChangeFrequency('weekly'));

        $sitemap->add(Url::create($baseUrl . '/about')
            ->setPriority(0.8)
            ->setChangeFrequency('monthly'));

        $sitemap->add(Url::create($baseUrl . '/contact')
            ->setPriority(0.9)
            ->setChangeFrequency('monthly'));

        $sitemap->add(Url::create($baseUrl . '/products')
            ->setPriority(0.9)
            ->setChangeFrequency('daily'));

        $sitemap->add(Url::create($baseUrl . '/partners')
            ->setPriority(0.7)
            ->setChangeFrequency('monthly'));

        $sitemap->add(Url::create($baseUrl . '/news')
            ->setPriority(0.8)
            ->setChangeFrequency('daily'));

        // Add industry pages
        $sitemap->add(Url::create($baseUrl . '/industries')
            ->setPriority(0.8)
            ->setChangeFrequency('monthly'));

        // Add quotation form pages (accessible without auth)
        $sitemap->add(Url::create($baseUrl . '/quotation/form')
            ->setPriority(0.7)
            ->setChangeFrequency('monthly'));

        $sitemap->writeToFile(public_path('sitemap-main.xml'));
        $this->info('✓ Main sitemap generated');
    }

    private function generateProductSitemap($baseUrl)
    {
        $sitemap = Sitemap::create();
        $productCount = 0;

        // Add products with better SEO optimization
        Product::chunk(100, function ($products) use ($sitemap, $baseUrl, &$productCount) {
            foreach ($products as $product) {
                $priority = 0.8;
                
                // Boost priority for newer products
                if ($product->created_at && $product->created_at->diffInDays() < 30) {
                    $priority = 0.9;
                }

                // Boost priority for products with images
                if ($product->image_url) {
                    $priority = min(1.0, $priority + 0.1);
                }

                $sitemap->add(Url::create($baseUrl . "/product/{$product->id}")
                    ->setPriority($priority)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($product->updated_at ?? $product->created_at));
                
                $productCount++;
            }
        });

        $sitemap->writeToFile(public_path('sitemap-products.xml'));
        $this->info("✓ Products sitemap generated ({$productCount} products)");
    }

    private function generateCategorySitemap($baseUrl)
    {
        $sitemap = Sitemap::create();
        $categoryCount = 0;

        // Add categories - only include valid categories with content
        Category::whereHas('products')
            ->orWhereHas('subcategories')
            ->chunk(50, function ($categories) use ($sitemap, $baseUrl, &$categoryCount) {
                foreach ($categories as $category) {
                    $productCount = $category->products()->count();
                    $subcategoryCount = $category->subcategories()->count();
                    
                    // Calculate priority based on content
                    $priority = 0.7;
                    if ($productCount > 10) $priority = 0.8;
                    if ($productCount > 20) $priority = 0.9;

                    $sitemap->add(Url::create($baseUrl . "/categories/{$category->id}")
                        ->setPriority($priority)
                        ->setChangeFrequency($productCount > 0 ? 'weekly' : 'monthly')
                        ->setLastModificationDate($category->updated_at));
                    
                    $categoryCount++;

                    // Add subcategories with content
                    foreach ($category->subcategories()->whereHas('products')->get() as $subcategory) {
                        $subProductCount = $subcategory->products()->count();
                        $subPriority = max(0.6, min(0.8, 0.6 + ($subProductCount * 0.01)));

                        $sitemap->add(Url::create($baseUrl . "/categories/{$category->id}/{$subcategory->id}")
                            ->setPriority($subPriority)
                            ->setChangeFrequency('weekly')
                            ->setLastModificationDate($subcategory->updated_at));
                        
                        $categoryCount++;

                        // Add sub-subcategories with products
                        foreach ($subcategory->subcategories()->whereHas('products')->get() as $subsubcategory) {
                            $sitemap->add(Url::create($baseUrl . "/categories/{$category->id}/{$subcategory->id}/{$subsubcategory->id}")
                                ->setPriority(0.6)
                                ->setChangeFrequency('weekly')
                                ->setLastModificationDate($subsubcategory->updated_at));
                            
                            $categoryCount++;
                        }
                    }
                }
            });

        $sitemap->writeToFile(public_path('sitemap-categories.xml'));
        $this->info("✓ Categories sitemap generated ({$categoryCount} categories)");
    }

    private function generateNewsSitemap($baseUrl)
    {
        $sitemap = Sitemap::create();
        $newsCount = 0;

        // Add news articles
        News::chunk(50, function ($newsItems) use ($sitemap, $baseUrl, &$newsCount) {
            foreach ($newsItems as $news) {
                $priority = 0.7;
                
                // Boost priority for recent news
                if ($news->created_at && $news->created_at->diffInDays() < 7) {
                    $priority = 0.9;
                } elseif ($news->created_at && $news->created_at->diffInDays() < 30) {
                    $priority = 0.8;
                }

                $sitemap->add(Url::create($baseUrl . "/news/{$news->id}")
                    ->setPriority($priority)
                    ->setChangeFrequency('monthly')
                    ->setLastModificationDate($news->updated_at));
                
                $newsCount++;
            }
        });

        $sitemap->writeToFile(public_path('sitemap-news.xml'));
        $this->info("✓ News sitemap generated ({$newsCount} articles)");
    }

    private function generateSitemapIndex($baseUrl)
    {
        $sitemapIndex = Sitemap::create();

        // Add all sitemaps to index with proper priorities
        $sitemapIndex->add(Url::create($baseUrl . '/sitemap-main.xml')
            ->setPriority(1.0)
            ->setLastModificationDate(now()));

        $sitemapIndex->add(Url::create($baseUrl . '/sitemap-products.xml')
            ->setPriority(0.9)
            ->setLastModificationDate(now()));

        $sitemapIndex->add(Url::create($baseUrl . '/sitemap-categories.xml')
            ->setPriority(0.8)
            ->setLastModificationDate(now()));

        $sitemapIndex->add(Url::create($baseUrl . '/sitemap-news.xml')
            ->setPriority(0.7)
            ->setLastModificationDate(now()));

        $sitemapIndex->writeToFile(public_path('sitemap.xml'));
        $this->info('✓ Sitemap index generated');
    }
} 