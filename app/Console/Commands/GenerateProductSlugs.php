<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class GenerateProductSlugs extends Command
{
    protected $signature = 'products:generate-slugs';
    protected $description = 'Generate slugs for products that don\'t have them';

    public function handle()
    {
        $this->info('Generating slugs for products...');
        
        $products = Product::whereNull('slug')->orWhere('slug', '')->get();
        
        $this->info("Found {$products->count()} products without slugs.");
        
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();
        
        foreach ($products as $product) {
            $product->slug = $product->generateSlug();
            $product->save();
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Slugs generated successfully!');
        
        return 0;
    }
} 