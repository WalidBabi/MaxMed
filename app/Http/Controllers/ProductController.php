<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::with('category'); // eager load category
        
        if ($categoryName = $request->input('category')) {
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }
        
        // Instead of getting all products, we paginate or limit the results
        $products = $query->paginate(16);
        
        // Fetch only top-level categories (categories without a parent)
        $categories = Category::whereNull('parent_id')->get();
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
    
    public function checkAvailability(Product $product, int $quantity): JsonResponse
    {
        // dd($product, $quantity);
        return response()->json([
            'available' => $product->inventory->quantity >= $quantity
        ]);
    }

    // You can add more methods like create, store, edit, update, destroy as needed
} 