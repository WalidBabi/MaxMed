<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

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
        
        return view('products.index', compact('products'));
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

    // You can add more methods like create, store, edit, update, destroy as needed
} 