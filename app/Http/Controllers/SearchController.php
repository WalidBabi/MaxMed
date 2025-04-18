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
            // Normalize search terms
            $searchTerm = '%' . strtolower(trim($query)) . '%';
            $exactTerm = strtolower(trim($query));
            
            // Log the search query for debugging
            Log::info("Search query: " . $query);
            
            // Use DB query builder for more control over the SQL
            $productsQuery = DB::table('products')
                ->select([
                    'products.*',
                    DB::raw('(
                        CASE 
                            WHEN LOWER(products.name) = "' . $exactTerm . '" THEN 100
                            WHEN LOWER(products.name) LIKE "' . $exactTerm . '%" THEN 80
                            WHEN LOWER(products.name) LIKE "%' . $exactTerm . '" THEN 70
                            WHEN LOWER(products.name) LIKE "%' . $exactTerm . '%" THEN 60
                            WHEN LOWER(products.description) LIKE "%' . $exactTerm . '%" THEN 40
                            ELSE 10
                        END) as relevance_score')
                ])
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->where(function($query) use ($searchTerm, $exactTerm) {
                    // Name matching - highest priority
                    $query->where('products.name', 'like', $searchTerm);
                    
                    // Description matching - medium priority
                    $query->orWhere('products.description', 'like', $searchTerm);
                    
                    // Category and brand matching
                    $query->orWhere('categories.name', 'like', $searchTerm);
                    $query->orWhere('brands.name', 'like', $searchTerm);
                    
                    // Token-based matching (for multi-word searches)
                    $tokens = preg_split('/[\s\-_]+/', $exactTerm);
                    if (count($tokens) > 1) {
                        foreach ($tokens as $token) {
                            if (strlen($token) >= 3) { // Only meaningful tokens
                                $tokenTerm = '%' . $token . '%';
                                $query->orWhere('products.name', 'like', $tokenTerm);
                            }
                        }
                    }
                })
                ->orderBy('relevance_score', 'desc') // Sort by relevance score
                ->orderBy('products.name', 'asc'); // Secondary sort by name
            
            // Log the query for debugging
            Log::info("Search SQL: " . $productsQuery->toSql());
            
            // Execute query with pagination
            $paginatedResults = $productsQuery->paginate(12);
            
            // Get the actual Product models for the view
            $productIds = collect($paginatedResults->items())->pluck('id')->toArray();
            
            // If we found any products, get their models
            if (count($productIds) > 0) {
                $productModels = Product::with(['category', 'brand'])
                    ->whereIn('id', $productIds)
                    ->get();
                
                // Create a new custom pagination instance
                $products = new \Illuminate\Pagination\LengthAwarePaginator(
                    $productModels,
                    $paginatedResults->total(),
                    $paginatedResults->perPage(),
                    $paginatedResults->currentPage(),
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                // No results found, return empty collection with pagination
                $products = $paginatedResults;
            }
            
            // Log the result count
            Log::info("Search results count: " . $products->total());
            
            return view('search.results', compact('products', 'query'));
            
        } catch (\Exception $e) {
            // Log the error with details
            Log::error("Search error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            
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
            // Normalize search terms
            $searchTerm = '%' . strtolower(trim($query)) . '%';
            $exactTerm = strtolower(trim($query));
            
            // Log the autocomplete query
            Log::info("Autocomplete request: " . $query);
            
            // Product name suggestions with relevance scoring
            $productSuggestions = DB::table('products')
                ->select('name')
                ->where(function($q) use ($searchTerm, $exactTerm) {
                    // Exact name match
                    $q->where(DB::raw('LOWER(name)'), '=', $exactTerm);
                    // Starts with search term
                    $q->orWhere(DB::raw('LOWER(name)'), 'like', $exactTerm . '%');
                    // Contains search term
                    $q->orWhere(DB::raw('LOWER(name)'), 'like', '%' . $exactTerm . '%');
                    
                    // Process multi-word searches
                    $tokens = preg_split('/[\s\-_]+/', $exactTerm);
                    if (count($tokens) > 1) {
                        foreach ($tokens as $token) {
                            if (strlen($token) >= 3) {
                                $q->orWhere(DB::raw('LOWER(name)'), 'like', '%' . $token . '%');
                            }
                        }
                    }
                })
                ->orderByRaw('
                    CASE 
                        WHEN LOWER(name) = ? THEN 1
                        WHEN LOWER(name) LIKE ? THEN 2
                        WHEN LOWER(name) LIKE ? THEN 3
                        ELSE 4
                    END
                ', [$exactTerm, $exactTerm . '%', '%' . $exactTerm . '%'])
                ->distinct()
                ->limit(6)
                ->pluck('name')
                ->toArray();
                
            // Category suggestions
            $categorySuggestions = DB::table('categories')
                ->select('name')
                ->where(function($q) use ($searchTerm, $exactTerm) {
                    $q->where(DB::raw('LOWER(name)'), 'like', $searchTerm);
                })
                ->orderByRaw('
                    CASE 
                        WHEN LOWER(name) = ? THEN 1
                        WHEN LOWER(name) LIKE ? THEN 2
                        ELSE 3
                    END
                ', [$exactTerm, $exactTerm . '%'])
                ->distinct()
                ->limit(3)
                ->pluck('name')
                ->toArray();
                
            // Brand suggestions
            $brandSuggestions = DB::table('brands')
                ->select('name')
                ->where(DB::raw('LOWER(name)'), 'like', $searchTerm)
                ->orderByRaw('
                    CASE 
                        WHEN LOWER(name) = ? THEN 1 
                        WHEN LOWER(name) LIKE ? THEN 2
                        ELSE 3
                    END
                ', [$exactTerm, $exactTerm . '%'])
                ->distinct()
                ->limit(2)
                ->pluck('name')
                ->toArray();
                
            // Merge all suggestions into one array
            $textCompletions = array_merge($productSuggestions, $categorySuggestions, $brandSuggestions);
            
            // Remove duplicates and limit to 8 suggestions
            $textCompletions = array_unique($textCompletions);
            $textCompletions = array_slice($textCompletions, 0, 8);
            
            // Log the number of suggestions
            Log::info("Returning " . count($textCompletions) . " search suggestions");
            
            return response()->json([
                'completions' => $textCompletions
            ]);
            
        } catch (\Exception $e) {
            Log::error("Search suggestions error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([]);
        }
    }
} 