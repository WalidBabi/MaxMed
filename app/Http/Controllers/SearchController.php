<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // Return all products if no query is provided
        if (!$query) {
            return redirect()->route('products.index');
        }

        // Search for products that match the query in name or description
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhereHas('category', function ($categoryQuery) use ($query) {
                $categoryQuery->where('name', 'LIKE', "%{$query}%");
            })
            ->paginate(12);

        return view('search.results', compact('products', 'query'));
    }
} 