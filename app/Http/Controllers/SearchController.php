<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            // Safely prepare the search term and make it case insensitive
            $searchTerm = '%' . trim(strtolower($query)) . '%';
            
            // Debug the search
            Log::info("Searching for: " . $query . " (Term: " . $searchTerm . ")");
            
            // Perform a more flexible search to match suggestions behavior
            $products = Product::select('products.*')
                ->where(function($q) use ($searchTerm, $query) {
                    // Search in product fields
                    $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(sku) LIKE ?', [$searchTerm]);
                    
                    // Search in categories
                    $q->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                        $categoryQuery->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                    });
                    
                    // Handle individual words for multi-word queries
                    $words = explode(' ', trim($query));
                    if (count($words) > 1) {
                        foreach ($words as $word) {
                            if (strlen($word) > 2) {
                                $wordTerm = '%' . strtolower($word) . '%';
                                $q->orWhereRaw('LOWER(name) LIKE ?', [$wordTerm])
                                  ->orWhereRaw('LOWER(description) LIKE ?', [$wordTerm]);
                            }
                        }
                    }
                })
                ->orderBy('name')
                ->paginate(12);
            
            // Debug results count
            Log::info("Found " . $products->total() . " products for: " . $query);
            
            return view('search.results', compact('products', 'query'));
            
        } catch (\Exception $e) {
            // Log the error
            Log::error("Search error: " . $e->getMessage());
            
            // Return a fallback - empty results but with a message
            $products = Product::where('id', '<', 0)->paginate(12); // empty collection with pagination
            return view('search.results', [
                'products' => $products,
                'query' => $query,
                'error' => 'An error occurred while searching. Please try a different search term.'
            ]);
        }
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }
        
        try {
            $searchTerm = '%' . trim(strtolower($query)) . '%';
            
            // Get product name suggestions with relevance scoring - improved case insensitivity
            $productSuggestions = Product::selectRaw('
                id, 
                name, 
                image_url,
                CASE 
                    WHEN LOWER(name) LIKE ? THEN 3
                    WHEN LOWER(name) LIKE ? THEN 2
                    WHEN LOWER(sku) LIKE ? THEN 3
                    ELSE 1
                END as relevance', 
                [
                    trim(strtolower($query)),       // Exact match
                    $searchTerm,                     // Partial match
                    '%' . trim(strtolower($query)) . '%'  // SKU match
                ]
            )
            ->where(function($q) use ($searchTerm, $query) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(sku) LIKE ?', ['%' . trim(strtolower($query)) . '%']);
                  
                // Also search individual words for better suggestions
                $words = explode(' ', trim($query));
                if (count($words) > 1) {
                    foreach ($words as $word) {
                        if (strlen($word) > 2) {
                            $wordTerm = '%' . strtolower($word) . '%';
                            $q->orWhereRaw('LOWER(name) LIKE ?', [$wordTerm]);
                        }
                    }
                }
            })
            ->orderByDesc('relevance')
            ->take(8) // Increased from 5 to 8 for more suggestions
            ->get();
                
            // Get category suggestions - improved case insensitivity
            $categorySuggestions = DB::table('categories')
                ->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                ->select('id', 'name')
                ->take(3)
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'type' => 'category'
                    ];
                });
                
            // Combine suggestions
            $suggestions = [
                'products' => $productSuggestions,
                'categories' => $categorySuggestions
            ];
            
            return response()->json($suggestions);
            
        } catch (\Exception $e) {
            Log::error("Search suggestions error: " . $e->getMessage());
            return response()->json([]);
        }
    }
} 