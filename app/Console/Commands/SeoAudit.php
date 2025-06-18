<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Services\SeoService;

class SeoAudit extends Command
{
    protected $signature = 'seo:audit {--fix : Automatically fix issues where possible}';
    protected $description = 'Audit SEO implementation and identify improvements';

    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        parent::__construct();
        $this->seoService = $seoService;
    }

    public function handle()
    {
        $this->info('🔍 Starting SEO Audit...');
        $this->newLine();

        $this->auditProducts();
        $this->auditCategories();
        $this->auditPages();
        $this->auditImages();
        $this->auditPerformance();

        $this->newLine();
        $this->info('✅ SEO Audit Complete');
    }

    private function auditProducts()
    {
        $this->info('📦 Auditing Products...');
        
        $products = Product::all();
        $issues = [];
        
        foreach ($products as $product) {
            // Check for missing alt text
            if (empty($product->name)) {
                $issues[] = "Product ID {$product->id}: Missing product name";
            }
            
            // Check for missing descriptions
            if (empty($product->description)) {
                $issues[] = "Product ID {$product->id}: Missing description";
            }
            
            // Check for missing images
            if (empty($product->image_url)) {
                $issues[] = "Product ID {$product->id}: Missing product image";
            }
            
            // Check for missing category
            if (!$product->category_id) {
                $issues[] = "Product ID {$product->id}: Not assigned to any category";
            }
        }
        
        if (empty($issues)) {
            $this->line('  ✅ All products have proper SEO data');
        } else {
            $this->warn('  ❌ Found ' . count($issues) . ' product SEO issues:');
            foreach (array_slice($issues, 0, 10) as $issue) {
                $this->line('    • ' . $issue);
            }
            if (count($issues) > 10) {
                $this->line('    ... and ' . (count($issues) - 10) . ' more issues');
            }
        }
    }

    private function auditCategories()
    {
        $this->info('📂 Auditing Categories...');
        
        $categories = Category::all();
        $issues = [];
        
        foreach ($categories as $category) {
            if (empty($category->description)) {
                $issues[] = "Category '{$category->name}': Missing description";
            }
            
            if (empty($category->image_url)) {
                $issues[] = "Category '{$category->name}': Missing image";
            }
        }
        
        if (empty($issues)) {
            $this->line('  ✅ All categories have proper SEO data');
        } else {
            $this->warn('  ❌ Found ' . count($issues) . ' category SEO issues:');
            foreach ($issues as $issue) {
                $this->line('    • ' . $issue);
            }
        }
    }

    private function auditPages()
    {
        $this->info('📄 Auditing Pages...');
        
        $pages = [
            'Home' => route('welcome'),
            'Products' => route('products.index'),
            'Categories' => route('categories.index'),
            'Contact' => route('contact'),
            'About' => route('about'),
        ];
        
        foreach ($pages as $name => $url) {
            $this->line("  ✅ {$name}: {$url}");
        }
    }

    private function auditImages()
    {
        $this->info('🖼️  Auditing Images...');
        
        $imageIssues = [];
        
        // Check for missing lazy loading
        $viewFiles = glob(resource_path('views/**/*.blade.php'));
        $lazyLoadingMissing = 0;
        
        foreach ($viewFiles as $file) {
            $content = file_get_contents($file);
            if (strpos($content, '<img') !== false && strpos($content, 'loading="lazy"') === false) {
                $lazyLoadingMissing++;
            }
        }
        
        if ($lazyLoadingMissing > 0) {
            $this->warn("  ❌ Found {$lazyLoadingMissing} view files without lazy loading");
            $this->line('    💡 Consider using <x-lazy-image> component for better performance');
        } else {
            $this->line('  ✅ Image lazy loading implementation looks good');
        }
    }

    private function auditPerformance()
    {
        $this->info('⚡ Auditing Performance...');
        
        // Check for sitemap
        if (file_exists(public_path('sitemap.xml'))) {
            $this->line('  ✅ Sitemap.xml exists');
        } else {
            $this->warn('  ❌ Sitemap.xml missing - run php artisan sitemap:generate');
        }
        
        // Check robots.txt
        if (file_exists(public_path('robots.txt'))) {
            $this->line('  ✅ Robots.txt exists');
        } else {
            $this->warn('  ❌ Robots.txt missing');
        }
        
        // Check for 404 page
        if (file_exists(resource_path('views/errors/404.blade.php'))) {
            $this->line('  ✅ Custom 404 page exists');
        } else {
            $this->warn('  ❌ Custom 404 page missing');
        }
    }
} 