<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class MigrateSupplierBrands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplier:migrate-brands {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate supplier products to use their company names as brands instead of hardcoded Yooning brand';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        // Get the Yooning brand
        $yooningBrand = Brand::where('name', 'Yooning')->first();
        
        if (!$yooningBrand) {
            $this->error('Yooning brand not found. No migration needed.');
            return 1;
        }

        // Get all supplier products that use Yooning brand
        $supplierProducts = Product::where('brand_id', $yooningBrand->id)
            ->whereNotNull('supplier_id')
            ->with(['supplier.supplierInformation'])
            ->get();

        if ($supplierProducts->isEmpty()) {
            $this->info('No supplier products found with Yooning brand. No migration needed.');
            return 0;
        }

        $this->info("Found {$supplierProducts->count()} supplier products with Yooning brand");

        $migratedCount = 0;
        $skippedCount = 0;

        foreach ($supplierProducts as $product) {
            $supplier = $product->supplier;
            
            if (!$supplier || !$supplier->supplierInformation) {
                $this->warn("Product {$product->id} ({$product->name}) - Supplier or supplier information not found, skipping");
                $skippedCount++;
                continue;
            }

            $companyName = $supplier->supplierInformation->company_name;
            
            if (empty($companyName)) {
                $this->warn("Product {$product->id} ({$product->name}) - Supplier company name is empty, skipping");
                $skippedCount++;
                continue;
            }

            // Get or create brand based on company name
            $companyBrand = Brand::firstOrCreate(['name' => $companyName]);

            if ($isDryRun) {
                $this->line("Would migrate: Product {$product->id} ({$product->name}) from 'Yooning' to '{$companyBrand->name}'");
            } else {
                try {
                    DB::transaction(function () use ($product, $companyBrand) {
                        $product->update(['brand_id' => $companyBrand->id]);
                    });
                    
                    $this->line("Migrated: Product {$product->id} ({$product->name}) from 'Yooning' to '{$companyBrand->name}'");
                    $migratedCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to migrate product {$product->id}: " . $e->getMessage());
                    $skippedCount++;
                }
            }
        }

        if ($isDryRun) {
            $this->info("\nDRY RUN SUMMARY:");
            $this->info("- Would migrate: {$migratedCount} products");
            $this->info("- Would skip: {$skippedCount} products");
        } else {
            $this->info("\nMIGRATION SUMMARY:");
            $this->info("- Successfully migrated: {$migratedCount} products");
            $this->info("- Skipped: {$skippedCount} products");
        }

        return 0;
    }
}
