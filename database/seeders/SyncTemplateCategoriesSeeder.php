<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class SyncTemplateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templateNames = [
            'Lab Equipment',
            'Reagents',
            'Consumables',
            'Analytical Chemistry',
        ];

        foreach ($templateNames as $name) {
            $category = Category::where('name', $name)->first();
            if (!$category) {
                Category::create(['name' => $name]);
                $this->command->info("Created category: $name");
            } else {
                $this->command->info("Category already exists: $name");
            }
        }
    }
} 