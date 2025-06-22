<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSpecification;

class TestProductSpecificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Product::where('slug', 'test-mt-0124-maxtest-dubai-uae')->first();
        
        if (!$product) {
            $this->command->error('Test product not found!');
            return;
        }

        // Clear existing specifications for this product
        ProductSpecification::where('product_id', $product->id)->delete();

        // Add sample specifications
        $specifications = [
            [
                'product_id' => $product->id,
                'specification_key' => 'accuracy',
                'specification_value' => '99.5',
                'unit' => '%',
                'category' => 'Performance',
                'display_name' => 'Accuracy',
                'description' => 'Test accuracy rate for reliable results',
                'sort_order' => 1,
                'is_filterable' => true,
                'is_searchable' => true,
                'show_on_listing' => true,
                'show_on_detail' => true,
            ],
            [
                'product_id' => $product->id,
                'specification_key' => 'sensitivity',
                'specification_value' => '98.2',
                'unit' => '%',
                'category' => 'Performance',
                'display_name' => 'Sensitivity',
                'description' => 'Test sensitivity rate for detection',
                'sort_order' => 2,
                'is_filterable' => true,
                'is_searchable' => true,
                'show_on_listing' => true,
                'show_on_detail' => true,
            ],
            [
                'product_id' => $product->id,
                'specification_key' => 'specificity',
                'specification_value' => '99.8',
                'unit' => '%',
                'category' => 'Performance',
                'display_name' => 'Specificity',
                'description' => 'Test specificity rate for precision',
                'sort_order' => 3,
                'is_filterable' => true,
                'is_searchable' => true,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'product_id' => $product->id,
                'specification_key' => 'test_time',
                'specification_value' => '15',
                'unit' => 'minutes',
                'category' => 'Technical',
                'display_name' => 'Test Time',
                'description' => 'Time required to complete the test',
                'sort_order' => 1,
                'is_filterable' => false,
                'is_searchable' => true,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'product_id' => $product->id,
                'specification_key' => 'sample_volume',
                'specification_value' => '50',
                'unit' => 'μL',
                'category' => 'Technical',
                'display_name' => 'Sample Volume',
                'description' => 'Required sample volume for testing',
                'sort_order' => 2,
                'is_filterable' => false,
                'is_searchable' => false,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'product_id' => $product->id,
                'specification_key' => 'storage_temp',
                'specification_value' => '2-8',
                'unit' => '°C',
                'category' => 'Storage',
                'display_name' => 'Storage Temperature',
                'description' => 'Recommended storage temperature range',
                'sort_order' => 1,
                'is_filterable' => false,
                'is_searchable' => false,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'product_id' => $product->id,
                'specification_key' => 'shelf_life',
                'specification_value' => '12',
                'unit' => 'months',
                'category' => 'Storage',
                'display_name' => 'Shelf Life',
                'description' => 'Product shelf life from manufacturing date',
                'sort_order' => 2,
                'is_filterable' => false,
                'is_searchable' => false,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'product_id' => $product->id,
                'specification_key' => 'ce_marking',
                'specification_value' => 'Yes',
                'category' => 'Regulatory',
                'display_name' => 'CE Marking',
                'description' => 'CE marking for European market compliance',
                'sort_order' => 1,
                'is_filterable' => true,
                'is_searchable' => true,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'product_id' => $product->id,
                'specification_key' => 'fda_approved',
                'specification_value' => 'Yes',
                'category' => 'Regulatory',
                'display_name' => 'FDA Approved',
                'description' => 'FDA approval for US market',
                'sort_order' => 2,
                'is_filterable' => true,
                'is_searchable' => true,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
        ];

        foreach ($specifications as $spec) {
            ProductSpecification::create($spec);
        }

        $this->command->info('Test product specifications added successfully!');
        $this->command->info('Added ' . count($specifications) . ' specifications to product: ' . $product->name);
    }
} 