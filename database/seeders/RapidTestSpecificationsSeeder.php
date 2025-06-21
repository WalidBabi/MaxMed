<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\Category;

class RapidTestSpecificationsSeeder extends Seeder
{
    public function run(): void
    {
        // Find rapid test category (adjust based on your category structure)
        $rapidTestCategory = Category::where('name', 'LIKE', '%rapid%')
            ->orWhere('name', 'LIKE', '%test%')
            ->first();

        if (!$rapidTestCategory) {
            $this->command->info('No rapid test category found. Please create products first.');
            return;
        }

        // Get some sample products from rapid test category
        $rapidTestProducts = Product::where('category_id', $rapidTestCategory->id)
            ->limit(5)
            ->get();

        if ($rapidTestProducts->isEmpty()) {
            $this->command->info('No rapid test products found. Please create products first.');
            return;
        }

        foreach ($rapidTestProducts as $product) {
            $this->createRapidTestSpecifications($product);
        }

        $this->command->info('Rapid test specifications seeded successfully!');
    }

    private function createRapidTestSpecifications(Product $product)
    {
        $specifications = [
            // Performance Specifications
            [
                'specification_key' => 'test_count',
                'specification_value' => rand(10, 100),
                'unit' => 'tests',
                'category' => 'Performance',
                'display_name' => 'Tests per Kit',
                'description' => 'Number of individual tests included in the kit',
                'sort_order' => 1,
                'is_filterable' => true,
                'show_on_listing' => true,
                'show_on_detail' => true,
            ],
            [
                'specification_key' => 'detection_time',
                'specification_value' => rand(5, 30),
                'unit' => 'minutes',
                'category' => 'Performance',
                'display_name' => 'Detection Time',
                'description' => 'Time required to get test results',
                'sort_order' => 2,
                'is_filterable' => true,
                'show_on_listing' => true,
                'show_on_detail' => true,
            ],
            [
                'specification_key' => 'sensitivity',
                'specification_value' => rand(95, 99) . '.' . rand(0, 9),
                'unit' => '%',
                'category' => 'Performance',
                'display_name' => 'Sensitivity',
                'description' => 'Ability to correctly identify positive cases',
                'sort_order' => 3,
                'is_filterable' => false,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'specification_key' => 'specificity',
                'specification_value' => rand(95, 99) . '.' . rand(0, 9),
                'unit' => '%',
                'category' => 'Performance',
                'display_name' => 'Specificity',
                'description' => 'Ability to correctly identify negative cases',
                'sort_order' => 4,
                'is_filterable' => false,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],

            // Physical Specifications
            [
                'specification_key' => 'sample_type',
                'specification_value' => collect(['Nasal Swab', 'Throat Swab', 'Saliva', 'Blood', 'Urine'])->random(),
                'unit' => null,
                'category' => 'Physical',
                'display_name' => 'Sample Type',
                'description' => 'Type of biological sample required for testing',
                'sort_order' => 1,
                'is_filterable' => true,
                'show_on_listing' => true,
                'show_on_detail' => true,
            ],
            [
                'specification_key' => 'storage_temperature',
                'specification_value' => rand(2, 8) . '-' . rand(25, 30),
                'unit' => '°C',
                'category' => 'Physical',
                'display_name' => 'Storage Temperature',
                'description' => 'Required temperature range for product storage',
                'sort_order' => 2,
                'is_filterable' => false,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'specification_key' => 'shelf_life',
                'specification_value' => rand(18, 36),
                'unit' => 'months',
                'category' => 'Physical',
                'display_name' => 'Shelf Life',
                'description' => 'Product expiration period from manufacturing date',
                'sort_order' => 3,
                'is_filterable' => true,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],

            // Regulatory Specifications
            [
                'specification_key' => 'ce_marking',
                'specification_value' => 'Yes',
                'unit' => null,
                'category' => 'Regulatory',
                'display_name' => 'CE Marking',
                'description' => 'European Conformity certification status',
                'sort_order' => 1,
                'is_filterable' => true,
                'show_on_listing' => true,
                'show_on_detail' => true,
            ],
            [
                'specification_key' => 'fda_approval',
                'specification_value' => collect(['EUL', 'Approved', 'Pending'])->random(),
                'unit' => null,
                'category' => 'Regulatory',
                'display_name' => 'FDA Status',
                'description' => 'US Food and Drug Administration approval status',
                'sort_order' => 2,
                'is_filterable' => true,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
            [
                'specification_key' => 'iso_certification',
                'specification_value' => 'ISO 13485:2016',
                'unit' => null,
                'category' => 'Regulatory',
                'display_name' => 'ISO Certification',
                'description' => 'International Organization for Standardization compliance',
                'sort_order' => 3,
                'is_filterable' => false,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],

            // Technical Specifications
            [
                'specification_key' => 'test_method',
                'specification_value' => collect(['Lateral Flow', 'ELISA', 'RT-PCR', 'Antigen Detection'])->random(),
                'unit' => null,
                'category' => 'Technical',
                'display_name' => 'Test Method',
                'description' => 'Scientific methodology used for detection',
                'sort_order' => 1,
                'is_filterable' => true,
                'show_on_listing' => true,
                'show_on_detail' => true,
            ],
            [
                'specification_key' => 'reading_method',
                'specification_value' => collect(['Visual', 'Digital Reader', 'Mobile App'])->random(),
                'unit' => null,
                'category' => 'Technical',
                'display_name' => 'Reading Method',
                'description' => 'How test results are interpreted',
                'sort_order' => 2,
                'is_filterable' => true,
                'show_on_listing' => false,
                'show_on_detail' => true,
            ],
        ];

        foreach ($specifications as $spec) {
            ProductSpecification::create(array_merge($spec, [
                'product_id' => $product->id,
                'is_searchable' => true,
            ]));
        }
    }
} 