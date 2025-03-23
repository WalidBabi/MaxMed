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
        
        // Handle sorting
        if ($sortOption = $request->input('sort')) {
            switch ($sortOption) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                // Add more sort options if needed
            }
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
        // Pass the single product to the view
        return view('products.show', compact('product'));
    }
    
    public function checkAvailability(Product $product, int $quantity): JsonResponse
    {
        // dd($product, $quantity);
        return response()->json([
            'available' => $product->inventory->quantity >= $quantity
        ]);
    }

    public function showProducts()
    {
        // Retrieve all products or apply any specific logic
        $products = Product::all(); // or use a query to filter/sort products

        // Pass the products to the view
        return view('products.show', compact('products'));
    }

    // You can add more methods like create, store, edit, update, destroy as needed
} 