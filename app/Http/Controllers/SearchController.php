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
            // Safely prepare the search term - convert to lowercase
            $searchTerm = '%' . strtolower(trim($query)) . '%';
            $exactQuery = strtolower(trim($query));
            
            // Exact match will have the highest priority - case insensitive
            $exactMatchQuery = Product::whereRaw('LOWER(name) LIKE ?', [$exactQuery])
                ->orWhere(function($q) use ($exactQuery) {
                    // Match full product code - case insensitive
                    $q->whereRaw('LOWER(sku) = ?', [$exactQuery]);
                });
                
            // If exact matches exist, use them; otherwise perform a broader search
            $exactMatches = $exactMatchQuery->get();
            
            if ($exactMatches->count() > 0) {
                $products = $exactMatchQuery->paginate(12);
            } else {
                // Use semantic search with weighted relevance scoring - case insensitive
                $products = Product::selectRaw('
                    products.*, 
                    CASE 
                        WHEN LOWER(name) LIKE ? THEN 10
                        WHEN LOWER(name) LIKE ? THEN 8 
                        WHEN LOWER(description) LIKE ? THEN 5
                        WHEN EXISTS (SELECT 1 FROM categories WHERE categories.id = products.category_id AND LOWER(categories.name) LIKE ?) THEN 3
                        ELSE 1
                    END as relevance_score', 
                    [
                        $exactQuery,   // Exact match on name
                        $searchTerm,   // Partial match on name
                        $searchTerm,   // Match on description
                        $searchTerm    // Match on category name
                    ]
                )
                ->where(function($q) use ($searchTerm, $query) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(sku) LIKE ?', [$searchTerm])
                      ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                          $categoryQuery->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                      });
                      
                    // Split the query into words for more intelligent matching
                    $words = explode(' ', trim($query));
                    if (count($words) > 1) {
                        foreach ($words as $word) {
                            if (strlen($word) > 3) { // Only consider words longer than 3 characters
                                $wordTerm = '%' . strtolower($word) . '%';
                                $q->orWhereRaw('LOWER(name) LIKE ?', [$wordTerm])
                                  ->orWhereRaw('LOWER(description) LIKE ?', [$wordTerm]);
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
            // Convert to lowercase for case-insensitive search
            $searchTerm = '%' . strtolower(trim($query)) . '%';
            $exactQuery = strtolower(trim($query));
            
            // Get product name suggestions with relevance scoring - case insensitive
            $productSuggestions = Product::selectRaw('
                id, 
                name, 
                image_url,
                CASE 
                    WHEN LOWER(name) LIKE ? THEN 3
                    WHEN LOWER(name) LIKE ? THEN 2
                    ELSE 1
                END as relevance', 
                [
                    $exactQuery,  // Exact match
                    $searchTerm   // Partial match
                ]
            )
            ->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
            ->orderByDesc('relevance')
            ->take(5)
            ->get();
                
            // Get category suggestions - case insensitive
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