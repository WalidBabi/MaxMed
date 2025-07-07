<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\Category;
use App\Models\Brand;

class TestProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test category if it doesn't exist
        $category = Category::firstOrCreate(
            ['name' => 'Test Category'],
            ['description' => 'Test category for specifications testing']
        );

        // Create a test brand if it doesn't exist
        $brand = Brand::firstOrCreate(
            ['name' => 'Test Brand'],
            ['description' => 'Test brand for specifications testing']
        );

        // Create a test product with specifications and size options
        $product = Product::firstOrCreate(
            ['name' => 'Test Product with Specs'],
            [
                'sku' => 'TEST-SPECS-001',
                'description' => 'A test product with specifications and size options',
                'price_aed' => 100.00,
                'procurement_price_usd' => 25.00,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'has_size_options' => true,
                'size_options' => json_encode(['Small', 'Medium', 'Large', 'XL'])
            ]
        );

        // Create specifications for the test product
        $specifications = [
            ['specification_key' => 'Material', 'specification_value' => 'Stainless Steel', 'display_name' => 'Material: Stainless Steel'],
            ['specification_key' => 'Color', 'specification_value' => 'Silver', 'display_name' => 'Color: Silver'],
            ['specification_key' => 'Weight', 'specification_value' => '500g', 'display_name' => 'Weight: 500g'],
            ['specification_key' => 'Dimensions', 'specification_value' => '10x5x2 cm', 'display_name' => 'Dimensions: 10x5x2 cm'],
            ['specification_key' => 'Certification', 'specification_value' => 'ISO 9001', 'display_name' => 'Certification: ISO 9001']
        ];

        foreach ($specifications as $spec) {
            ProductSpecification::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'specification_key' => $spec['specification_key']
                ],
                [
                    'specification_value' => $spec['specification_value'],
                    'display_name' => $spec['display_name']
                ]
            );
        }

        // Create another test product without size options
        $product2 = Product::firstOrCreate(
            ['name' => 'Test Product No Size'],
            [
                'sku' => 'TEST-NOSIZE-001',
                'description' => 'A test product with specifications but no size options',
                'price_aed' => 75.00,
                'procurement_price_usd' => 20.00,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'has_size_options' => false,
                'size_options' => null
            ]
        );

        // Create specifications for the second test product
        $specifications2 = [
            ['specification_key' => 'Type', 'specification_value' => 'Digital', 'display_name' => 'Type: Digital'],
            ['specification_key' => 'Battery', 'specification_value' => 'Rechargeable', 'display_name' => 'Battery: Rechargeable'],
            ['specification_key' => 'Warranty', 'specification_value' => '2 Years', 'display_name' => 'Warranty: 2 Years']
        ];

        foreach ($specifications2 as $spec) {
            ProductSpecification::firstOrCreate(
                [
                    'product_id' => $product2->id,
                    'specification_key' => $spec['specification_key']
                ],
                [
                    'specification_value' => $spec['specification_value'],
                    'display_name' => $spec['display_name']
                ]
            );
        }

        $this->command->info('Test products with specifications and size options created successfully!');
    }
}
