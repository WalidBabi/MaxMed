<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\News;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Add static pages
        $sitemap->add(Url::create('/')
            ->setPriority(1.0)
            ->setChangeFrequency('weekly'));

        $sitemap->add(Url::create('/medical-laboratory-equipment')
            ->setPriority(0.9)
            ->setChangeFrequency('daily'));

        $sitemap->add(Url::create('/about-maxmed-uae')
            ->setPriority(0.8)
            ->setChangeFrequency('monthly'));

        $sitemap->add(Url::create('/contact-laboratory-equipment-supplier')
            ->setPriority(0.8)
            ->setChangeFrequency('monthly'));

        // Add products
        Product::all()->each(function (Product $product) use ($sitemap) {
            $sitemap->add(Url::create("/product/{$product->id}")
                ->setPriority(0.8)
                ->setChangeFrequency('weekly')
                ->setLastModificationDate($product->updated_at));
        });

        // Add news articles
        News::all()->each(function (News $news) use ($sitemap) {
            $sitemap->add(Url::create("/news/{$news->id}")
                ->setPriority(0.7)
                ->setChangeFrequency('monthly')
                ->setLastModificationDate($news->updated_at));
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
} 