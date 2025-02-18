<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Product::create([
            'name' => 'Product 1',
            'price' => 29.99,
            'image_url' => 'images/product1.jpg',
        ]);

        Product::create([
            'name' => 'Product 2',
            'price' => 49.99,
            'image_url' => 'images/product2.jpg',
        ]);

        // Add more products as needed
    }
} 