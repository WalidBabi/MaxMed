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
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        $sitemap = Sitemap::create();
        $baseUrl = 'https://maxmedme.com';

        // Add static pages
        $sitemap->add(Url::create($baseUrl)
            ->setPriority(1.0)
            ->setChangeFrequency('weekly'));

        $sitemap->add(Url::create($baseUrl . '/about')
            ->setPriority(0.8)
            ->setChangeFrequency('monthly'));

        $sitemap->add(Url::create($baseUrl . '/contact')
            ->setPriority(0.8)
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

        // Add categories - only include valid categories with content
        Category::all()->each(function (Category $category) use ($sitemap, $baseUrl) {
            // Only add categories that have products or subcategories
            if ($category->products->count() > 0 || $category->subcategories->count() > 0) {
                $sitemap->add(Url::create($baseUrl . "/categories/{$category->id}")
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($category->updated_at));
                
                // Add subcategories that have content
                foreach ($category->subcategories as $subcategory) {
                    if ($subcategory->products->count() > 0 || $subcategory->subcategories->count() > 0) {
                        $sitemap->add(Url::create($baseUrl . "/categories/{$category->id}/{$subcategory->id}")
                            ->setPriority(0.7)
                            ->setChangeFrequency('weekly')
                            ->setLastModificationDate($subcategory->updated_at));
                        
                        // Add subsubcategories that have products
                        foreach ($subcategory->subcategories as $subsubcategory) {
                            if ($subsubcategory->products->count() > 0) {
                                $sitemap->add(Url::create($baseUrl . "/categories/{$category->id}/{$subcategory->id}/{$subsubcategory->id}")
                                    ->setPriority(0.6)
                                    ->setChangeFrequency('weekly')
                                    ->setLastModificationDate($subsubcategory->updated_at));
                            }
                        }
                    }
                }
            }
        });

        // Add products
        Product::all()->each(function (Product $product) use ($sitemap, $baseUrl) {
            $sitemap->add(Url::create($baseUrl . "/product/{$product->id}")
                ->setPriority(0.8)
                ->setChangeFrequency('weekly')
                ->setLastModificationDate($product->updated_at));
        });

        // Add news articles
        News::all()->each(function (News $news) use ($sitemap, $baseUrl) {
            $sitemap->add(Url::create($baseUrl . "/news/{$news->id}")
                ->setPriority(0.7)
                ->setChangeFrequency('weekly')
                ->setLastModificationDate($news->updated_at));
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
} 