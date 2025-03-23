<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // Return all products if no query is provided
        if (!$query) {
            return redirect()->route('products.index');
        }

        try {
            
            // Safely prepare the search term
            $searchTerm = '%' . trim($query) . '%';
            
            // Search for products that match the query in name or description
            $products = Product::where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm);
            })
            ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                $categoryQuery->where('name', 'LIKE', $searchTerm);
            })
            ->paginate(12);
            
            return view('search.results', compact('products', 'query'));
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Search error: " . $e->getMessage());
            
            // Return a fallback - empty results but with a message
            $products = Product::where('id', '<', 0)->paginate(12); // empty collection with pagination
            return view('search.results', [
                'products' => $products,
                'query' => $query,
                'error' => 'An error occurred while searching. Please try a different search term.'
            ]);
        }
    }
} 