<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupplierCategoryType;
use Illuminate\Support\Str;

class SupplierCategoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Medical Equipment',
                'description' => 'Suppliers specializing in medical equipment and devices',
                'is_active' => true,
            ],
            [
                'name' => 'Laboratory Supplies',
                'description' => 'Suppliers providing laboratory supplies and consumables',
                'is_active' => true,
            ],
            [
                'name' => 'Pharmaceuticals',
                'description' => 'Pharmaceutical and drug suppliers',
                'is_active' => true,
            ],
            [
                'name' => 'Diagnostic Equipment',
                'description' => 'Suppliers of diagnostic and testing equipment',
                'is_active' => true,
            ],
            [
                'name' => 'Surgical Instruments',
                'description' => 'Suppliers of surgical instruments and tools',
                'is_active' => true,
            ],
            [
                'name' => 'Safety Equipment',
                'description' => 'Suppliers of medical safety and protective equipment',
                'is_active' => true,
            ],
            [
                'name' => 'Research Supplies',
                'description' => 'Suppliers providing research and analytical supplies',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            SupplierCategoryType::updateOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                    'is_active' => $category['is_active'],
                ]
            );
        }

        $this->command->info('SupplierCategoryType seeder completed successfully.');
    }
}
