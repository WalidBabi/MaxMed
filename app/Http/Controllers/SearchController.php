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
            // Safely prepare the search term
            $searchTerm = '%' . trim($query) . '%';
            
            // Exact match will have the highest priority
            $exactMatchQuery = Product::where('name', 'LIKE', trim($query))
                ->orWhere(function($q) use ($query) {
                    // Match full product code
                    $q->where('sku', trim($query));
                });
                
            // If exact matches exist, use them; otherwise perform a broader search
            $exactMatches = $exactMatchQuery->get();
            
            if ($exactMatches->count() > 0) {
                $products = $exactMatchQuery->paginate(12);
            } else {
                // Use semantic search with weighted relevance scoring
                $products = Product::selectRaw('
                    products.*, 
                    CASE 
                        WHEN name LIKE ? THEN 10
                        WHEN name LIKE ? THEN 8 
                        WHEN description LIKE ? THEN 5
                        WHEN EXISTS (SELECT 1 FROM categories WHERE categories.id = products.category_id AND categories.name LIKE ?) THEN 3
                        ELSE 1
                    END as relevance_score', 
                    [
                        trim($query), // Exact match on name
                        $searchTerm,  // Partial match on name
                        $searchTerm,  // Match on description
                        $searchTerm   // Match on category name
                    ]
                )
                ->where(function($q) use ($searchTerm, $query) {
                    $q->where('name', 'LIKE', $searchTerm)
                      ->orWhere('description', 'LIKE', $searchTerm)
                      ->orWhere('sku', 'LIKE', $searchTerm)
                      ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                          $categoryQuery->where('name', 'LIKE', $searchTerm);
                      });
                      
                    // Split the query into words for more intelligent matching
                    $words = explode(' ', trim($query));
                    if (count($words) > 1) {
                        foreach ($words as $word) {
                            if (strlen($word) > 3) { // Only consider words longer than 3 characters
                                $wordTerm = '%' . $word . '%';
                                $q->orWhere('name', 'LIKE', $wordTerm)
                                  ->orWhere('description', 'LIKE', $wordTerm);
                            }
                        }
                    }
                })
                ->orderByDesc('relevance_score')
                ->paginate(12);
            }
            
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
            $searchTerm = '%' . trim($query) . '%';
            
            // Get product name suggestions with relevance scoring
            $productSuggestions = Product::selectRaw('
                id, 
                name, 
                image_url,
                CASE 
                    WHEN name LIKE ? THEN 3
                    WHEN name LIKE ? THEN 2
                    ELSE 1
                END as relevance', 
                [
                    trim($query), // Exact match
                    $searchTerm   // Partial match
                ]
            )
            ->where('name', 'LIKE', $searchTerm)
            ->orderByDesc('relevance')
            ->take(5)
            ->get();
                
            // Get category suggestions
            $categorySuggestions = DB::table('categories')
                ->where('name', 'LIKE', $searchTerm)
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