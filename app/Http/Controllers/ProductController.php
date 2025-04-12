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
        $query = Product::with(['category', 'inventory']); // eager load category and inventory
        
        // Filter by search query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($categoryName = $request->input('category')) {
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }
        
        // Filter by subcategory
        if ($request->filled('subcategory')) {
            $query->where('category_id', $request->input('subcategory'));
        }
        
        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price_aed', '>=', $request->input('price_min'));
        }
        
        if ($request->filled('price_max')) {
            $query->where('price_aed', '<=', $request->input('price_max'));
        }
        
        // Filter by stock status
        if ($request->filled('in_stock')) {
            $inStock = $request->input('in_stock');
            $query->whereHas('inventory', function($q) use ($inStock) {
                if ($inStock == '1') {
                    $q->where('quantity', '>', 0);
                } else {
                    $q->where('quantity', '=', 0);
                }
            });
        }
        
        // Filter by brand
        if ($request->filled('brand')) {
            // Assuming you have a brand field in products or a brand relationship
            $query->where('brand_id', $request->input('brand'));
        }
        
        // Filter by application
        if ($request->filled('application')) {
            // Assuming you have an application field or tag in products
            $query->where('application', $request->input('application'));
        }
        
        // Handle sorting
        if ($sortOption = $request->input('sort')) {
            switch ($sortOption) {
                case 'price_asc':
                    $query->orderBy('price_aed', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price_aed', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // Default sort
            $query->orderBy('created_at', 'desc');
        }
        
        // Paginate products
        $products = $query->paginate(16);
        
        // Preserve query parameters in pagination links
        $products->appends($request->all());
        
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