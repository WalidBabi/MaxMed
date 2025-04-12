<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        
        // Get featured products based on multiple criteria
        $featuredProducts = Product::where(function($query) {
            // Products with discount > 15%
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        })
        ->inRandomOrder()
        ->take(4)
        ->get();
        
        // Get featured brands
        $featuredBrands = Brand::where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('welcome', compact('categories', 'featuredProducts', 'featuredBrands'));
    }
}