<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class GenerateRssFeed extends Command
{
    protected $signature = 'rss:generate {--type=all : Type of RSS feed (news, products, all)}';
    protected $description = 'Generate RSS feeds for news and products';

    public function handle()
    {
        $type = $this->option('type');
        
        $this->info('🔄 Generating RSS feeds for MaxMed UAE...');

        switch ($type) {
            case 'news':
                $this->generateNewsFeed();
                break;
            case 'products':
                $this->generateProductsFeed();
                break;
            case 'all':
            default:
                $this->generateNewsFeed();
                $this->generateProductsFeed();
                $this->generateMainFeed();
                break;
        }

        $this->info('✅ RSS feeds generated successfully!');
        return 0;
    }

    private function generateNewsFeed()
    {
        $this->info('📰 Generating news RSS feed...');
        
        $news = News::where('published', true)
                   ->orderBy('created_at', 'desc')
                   ->limit(20)
                   ->get();

        $rss = $this->createRssHeader('MaxMed UAE - Latest News', 
                                     'Stay updated with latest news from MaxMed UAE',
                                     route('news.index'));

        foreach ($news as $article) {
            $rss .= $this->createRssItem(
                $article->title,
                route('news.show', $article),
                strip_tags($article->content),
                $article->created_at,
                'MaxMed UAE',
                $article->id . '@maxmedme.com'
            );
        }

        $rss .= $this->createRssFooter();
        
        File::put(public_path('rss/news.xml'), $rss);
        $this->info('✓ News RSS feed created: /rss/news.xml');
    }

    private function generateProductsFeed()
    {
        $this->info('🔬 Generating products RSS feed...');
        
        $products = Product::with(['category', 'brand'])
                          ->orderBy('created_at', 'desc')
                          ->limit(50)
                          ->get();

        $rss = $this->createRssHeader('MaxMed UAE - Latest Products', 
                                     'Discover latest laboratory equipment and medical supplies from MaxMed UAE',
                                     route('products.index'));

        foreach ($products as $product) {
            $description = strip_tags($product->description) ?: 'Professional laboratory equipment from MaxMed UAE';
            $category = $product->category ? $product->category->name : 'Laboratory Equipment';
            
            $rss .= $this->createRssItem(
                $product->name . ' - ' . $category,
                route('product.show', $product),
                $description . ' | Category: ' . $category . ' | Contact MaxMed UAE for pricing.',
                $product->created_at,
                'MaxMed UAE',
                $product->id . '@maxmedme.com',
                $category,
                $product->image_url
            );
        }

        $rss .= $this->createRssFooter();
        
        File::put(public_path('rss/products.xml'), $rss);
        $this->info('✓ Products RSS feed created: /rss/products.xml');
    }

    private function generateMainFeed()
    {
        $this->info('🏠 Generating main RSS feed...');
        
        // Combine recent news and products
        $news = News::where('published', true)
                   ->orderBy('created_at', 'desc')
                   ->limit(10)
                   ->get();
                   
        $products = Product::with(['category'])
                          ->orderBy('created_at', 'desc')
                          ->limit(15)
                          ->get();

        $rss = $this->createRssHeader('MaxMed UAE - Latest Updates', 
                                     'Latest news and products from MaxMed UAE - Leading laboratory equipment supplier in Dubai',
                                     url('/'));

        // Add news items
        foreach ($news as $article) {
            $rss .= $this->createRssItem(
                '[News] ' . $article->title,
                route('news.show', $article),
                strip_tags($article->content),
                $article->created_at,
                'MaxMed UAE',
                'news-' . $article->id . '@maxmedme.com',
                'News'
            );
        }

        // Add product items
        foreach ($products as $product) {
            $description = strip_tags($product->description) ?: 'Professional laboratory equipment from MaxMed UAE';
            $category = $product->category ? $product->category->name : 'Laboratory Equipment';
            
            $rss .= $this->createRssItem(
                '[Product] ' . $product->name,
                route('product.show', $product),
                $description,
                $product->created_at,
                'MaxMed UAE',
                'product-' . $product->id . '@maxmedme.com',
                $category,
                $product->image_url
            );
        }

        $rss .= $this->createRssFooter();
        
        File::put(public_path('rss/feed.xml'), $rss);
        $this->info('✓ Main RSS feed created: /rss/feed.xml');
    }

    private function createRssHeader($title, $description, $link)
    {
        $buildDate = Carbon::now()->toRssString();
        
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
               '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n" .
               '<channel>' . "\n" .
               '<title><![CDATA[' . $title . ']]></title>' . "\n" .
               '<link>' . $link . '</link>' . "\n" .
               '<description><![CDATA[' . $description . ']]></description>' . "\n" .
               '<language>en-us</language>' . "\n" .
               '<pubDate>' . $buildDate . '</pubDate>' . "\n" .
               '<lastBuildDate>' . $buildDate . '</lastBuildDate>' . "\n" .
               '<managingEditor>sales@maxmedme.com (MaxMed UAE)</managingEditor>' . "\n" .
               '<webMaster>sales@maxmedme.com (MaxMed UAE)</webMaster>' . "\n" .
               '<generator>MaxMed UAE RSS Generator</generator>' . "\n" .
               '<image>' . "\n" .
               '<title>MaxMed UAE</title>' . "\n" .
               '<url>' . asset('Images/logo.png') . '</url>' . "\n" .
               '<link>' . url('/') . '</link>' . "\n" .
               '<description>MaxMed UAE - Laboratory Equipment Supplier</description>' . "\n" .
               '</image>' . "\n" .
               '<atom:link href="' . $link . '" rel="self" type="application/rss+xml" />' . "\n";
    }

    private function createRssItem($title, $link, $description, $pubDate, $author, $guid, $category = null, $image = null)
    {
        $item = '<item>' . "\n" .
                '<title><![CDATA[' . $title . ']]></title>' . "\n" .
                '<link>' . $link . '</link>' . "\n" .
                '<description><![CDATA[' . $description . ']]></description>' . "\n" .
                '<pubDate>' . $pubDate->toRssString() . '</pubDate>' . "\n" .
                '<author>sales@maxmedme.com (' . $author . ')</author>' . "\n" .
                '<guid isPermaLink="false">' . $guid . '</guid>' . "\n";

        if ($category) {
            $item .= '<category><![CDATA[' . $category . ']]></category>' . "\n";
        }

        if ($image) {
            $item .= '<enclosure url="' . $image . '" type="image/jpeg" />' . "\n";
        }

        $item .= '</item>' . "\n";

        return $item;
    }

    private function createRssFooter()
    {
        return '</channel>' . "\n" . '</rss>';
    }
} 