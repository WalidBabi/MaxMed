<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PopulateProductSkus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:populate-skus {--force : Force update existing SKUs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate SKUs for existing products that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting SKU population for products...');

        // Get products without SKUs (or all if force flag is used)
        $query = Product::query();
        
        if (!$this->option('force')) {
            $query->whereNull('sku');
        }
        
        $products = $query->get();
        
        if ($products->isEmpty()) {
            $this->info('No products need SKU generation.');
            return 0;
        }

        $this->info("Found {$products->count()} products that need SKUs.");
        
        // Group products by brand prefix to assign sequential numbers
        $productsByPrefix = [];
        foreach ($products as $product) {
            $prefix = $this->getSkuPrefix($product);
            if (!isset($productsByPrefix[$prefix])) {
                $productsByPrefix[$prefix] = [];
            }
            $productsByPrefix[$prefix][] = $product;
        }

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $updated = 0;
        $skipped = 0;

        // Process each brand group separately
        foreach ($productsByPrefix as $prefix => $brandProducts) {
            $counter = 1;
            
            foreach ($brandProducts as $product) {
                try {
                    // Generate SKU with sequential counter for this brand
                    $sku = $prefix . str_pad($counter, 4, '0', STR_PAD_LEFT);
                    
                    // Check if SKU already exists (in case of duplicates)
                    $existingProduct = Product::where('sku', $sku)->where('id', '!=', $product->id)->first();
                    
                    if ($existingProduct) {
                        // If SKU exists, add a suffix and increment counter
                        $sku = $prefix . str_pad($counter, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(uniqid(), -2));
                    }
                    
                    $product->update(['sku' => $sku]);
                    $updated++;
                    $counter++;
                    
                } catch (\Exception $e) {
                    $this->error("Failed to update product {$product->id}: " . $e->getMessage());
                    $skipped++;
                }
                
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        
        $this->info("SKU population completed!");
        $this->info("Updated: {$updated} products");
        
        if ($skipped > 0) {
            $this->warn("Skipped: {$skipped} products due to errors");
        }

        return 0;
    }

    /**
     * Get SKU prefix based on product brand
     */
    private function getSkuPrefix($product)
    {
        if (!$product->brand) {
            return 'MM-'; // Default for products without brand
        }

        $brandName = strtolower($product->brand->name);
        
        if (str_contains($brandName, 'maxtest')) {
            return 'MT-';
        } elseif (str_contains($brandName, 'maxware')) {
            return 'MW-';
        } else {
            return 'MM-'; // Default for other brands
        }
    }
}
