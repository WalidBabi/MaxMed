<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PopulateSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:slugs {--products} {--categories} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate slug columns for products and categories with SEO-friendly URLs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting slug population for SEO-friendly URLs...');

        if ($this->option('products') || $this->option('all')) {
            $this->populateProductSlugs();
        }

        if ($this->option('categories') || $this->option('all')) {
            $this->populateCategorySlugs();
        }

        if (!$this->option('products') && !$this->option('categories') && !$this->option('all')) {
            $this->error('❌ Please specify --products, --categories, or --all');
            return 1;
        }

        $this->info('');
        $this->info('🎉 All slugs populated successfully!');
        $this->info('Your site now has SEO-friendly URLs ready for implementation.');

        return 0;
    }

    private function populateProductSlugs()
    {
        $this->info('');
        $this->info('📦 Populating Product Slugs...');

        // Get all products without slugs or with empty slugs
        $products = DB::select("
            SELECT id, name, sku, brand_id 
            FROM products 
            WHERE slug IS NULL OR slug = ''
        ");

        if (empty($products)) {
            $this->info('   ✅ All products already have slugs');
            return;
        }

        $this->info("   Found " . count($products) . " products to process...");

        $updated = 0;
        $progressBar = $this->output->createProgressBar(count($products));
        $progressBar->start();

        foreach ($products as $product) {
            // Create SEO-friendly slug from title and model
            $baseSlug = $this->createProductSlug($product);
            $uniqueSlug = $this->ensureUniqueSlug($baseSlug, 'products', $product->id);

            // Update the product with the slug
            DB::update("UPDATE products SET slug = ? WHERE id = ?", [$uniqueSlug, $product->id]);

            $updated++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info('');
        $this->info("   ✅ Updated {$updated} product slugs");

        // Show some examples
        $this->showProductExamples();
    }

    private function populateCategorySlugs()
    {
        $this->info('');
        $this->info('📂 Populating Category Slugs...');

        // Get all categories without slugs or with empty slugs
        $categories = DB::select("
            SELECT id, name, parent_id 
            FROM categories 
            WHERE slug IS NULL OR slug = ''
        ");

        if (empty($categories)) {
            $this->info('   ✅ All categories already have slugs');
            return;
        }

        $this->info("   Found " . count($categories) . " categories to process...");

        $updated = 0;
        $progressBar = $this->output->createProgressBar(count($categories));
        $progressBar->start();

        foreach ($categories as $category) {
            // Create SEO-friendly slug from name
            $baseSlug = $this->createCategorySlug($category);
            $uniqueSlug = $this->ensureUniqueSlug($baseSlug, 'categories', $category->id);

            // Update the category with the slug
            DB::update("UPDATE categories SET slug = ? WHERE id = ?", [$uniqueSlug, $category->id]);

            $updated++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info('');
        $this->info("   ✅ Updated {$updated} category slugs");

        // Show some examples
        $this->showCategoryExamples();
    }

    private function createProductSlug($product)
    {
        // Combine name and sku for more descriptive slugs
        $text = trim($product->name . ' ' . ($product->sku ?? ''));
        
        // Remove brand name if it's already in the name to avoid duplication
        if ($product->brand_id) {
            $brand = DB::select("SELECT name FROM brands WHERE id = ?", [$product->brand_id])[0] ?? null;
            if ($brand && stripos($text, $brand->name) !== false) {
                // Brand name already in name, keep as is
            } else {
                // Add brand name for better SEO
                $text = trim($text . ' ' . ($brand->name ?? ''));
            }
        }

        // Add location for local SEO
        $text .= ' dubai uae';

        return $this->generateSlug($text);
    }

    private function createCategorySlug($category)
    {
        $text = $category->name;
        
        // For subcategories, include parent for better SEO structure
        if ($category->parent_id) {
            $parent = DB::select("SELECT name FROM categories WHERE id = ?", [$category->parent_id])[0] ?? null;
            if ($parent) {
                $text = $parent->name . ' ' . $text;
            }
        }

        return $this->generateSlug($text);
    }

    private function generateSlug($text)
    {
        // Clean and create SEO-friendly slug
        $slug = Str::lower($text);
        
        // Replace special characters and spaces with hyphens
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Limit length for better URLs
        if (strlen($slug) > 100) {
            $slug = substr($slug, 0, 100);
            $slug = rtrim($slug, '-');
        }

        return $slug;
    }

    private function ensureUniqueSlug($baseSlug, $table, $currentId = null)
    {
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            // Check if slug exists in the table
            $whereClause = $currentId ? "slug = ? AND id != ?" : "slug = ?";
            $params = $currentId ? [$slug, $currentId] : [$slug];
            
            $existing = DB::select("SELECT id FROM {$table} WHERE {$whereClause}", $params);

            if (empty($existing)) {
                return $slug; // Unique slug found
            }

            // Add counter to make it unique
            $slug = $baseSlug . '-' . $counter;
            $counter++;

            // Prevent infinite loops
            if ($counter > 100) {
                $slug = $baseSlug . '-' . time();
                break;
            }
        }

        return $slug;
    }

    private function showProductExamples()
    {
                $examples = DB::select("
            SELECT id, name, sku, slug 
            FROM products 
            WHERE slug IS NOT NULL 
            LIMIT 5
        ");

        if (!empty($examples)) {
            $this->info('   📋 Product URL Examples:');
            foreach ($examples as $example) {
                $oldUrl = "/product/" . $example->id;
                $newUrl = "/products/{$example->slug}";
                $this->line("      {$example->name} ({$example->sku})");
                $this->line("      Old: {$oldUrl}");
                $this->line("      New: {$newUrl}");
                $this->line('');
            }
        }
    }

    private function showCategoryExamples()
    {
        $examples = DB::select("
            SELECT name, slug 
            FROM categories 
            WHERE slug IS NOT NULL 
            LIMIT 5
        ");

        if (!empty($examples)) {
            $this->info('   📋 Category URL Examples:');
            foreach ($examples as $example) {
                $newUrl = "/categories/{$example->slug}";
                $this->line("      {$example->name}");
                $this->line("      New: {$newUrl}");
                $this->line('');
            }
        }
    }
}
