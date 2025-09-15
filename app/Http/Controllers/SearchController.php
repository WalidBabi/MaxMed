<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // Return all products if no query is provided
        if (!$query) {
            return redirect()->route('products.index');
        }

        // Cache category lookup
        $category = \App\Services\PerformanceCacheService::cacheQuery(
            "category_search_" . md5(strtolower(trim($query))),
            function() use ($query) {
                return DB::table('categories')->whereRaw('LOWER(name) = ?', [strtolower(trim($query))])->first();
            },
            1800 // 30 minutes
        );

        if ($category) {
            return redirect()->route('categories.show', $category->slug);
        }

        try {
            // Clean the query - remove brand names and extra formatting
            $cleanQuery = $this->cleanSearchQuery($query);
            
            // Normalize search terms
            $searchTerm = '%' . strtolower(trim($cleanQuery)) . '%';
            $exactTerm = strtolower(trim($cleanQuery));
            $tokens = $this->tokenizeSearchQuery($exactTerm);
            $allTokensLike = implode('%', $tokens);
            $allTokensSearch = '%' . $allTokensLike . '%';
            
            // Normalize for aggressive full phrase match (remove punctuation, lower case)
            $normalizedQuery = preg_replace('/[^a-z0-9]+/', '', strtolower($cleanQuery));
            
            // For multi-token AND matching
            $andTokenConditions = [];
            $andTokenBindings = [];
            foreach ($tokens as $token) {
                $andTokenConditions[] = "LOWER(products.name) LIKE ?";
                $andTokenBindings[] = '%' . $token . '%';
            }
            $andTokenSql = count($andTokenConditions) ? (implode(' AND ', $andTokenConditions)) : '1=0';
            
            // Prepare bindings for CASE statement
            $caseBindings = [
                $normalizedQuery, // Aggressive normalized full phrase match
                $exactTerm,           // Exact name
                $searchTerm,         // Name LIKE %full phrase%
                $exactTerm . '%',    // Name LIKE 'full phrase%'
                $allTokensSearch,    // Name LIKE all tokens as phrase
            ];
            $caseBindings = array_merge($caseBindings, $andTokenBindings); // All tokens AND
            $caseBindings[] = $searchTerm; // Desc LIKE full phrase
            $caseBindings[] = $searchTerm; // Name LIKE any token
            $caseBindings[] = $searchTerm; // Desc LIKE any token
            $caseSql = '(
                CASE 
                    WHEN REGEXP_REPLACE(LOWER(products.name), "[^a-z0-9]+", "") = ? THEN 2000
                    WHEN LOWER(products.name) = ? THEN 1000
                    WHEN LOWER(products.name) LIKE ? THEN 900
                    WHEN LOWER(products.name) LIKE ? THEN 800
                    WHEN LOWER(products.name) LIKE ? THEN 700
                    WHEN ' . $andTokenSql . ' THEN 600
                    WHEN LOWER(products.description) LIKE ? THEN 400
                    WHEN LOWER(products.name) LIKE ? THEN 200
                    WHEN LOWER(products.description) LIKE ? THEN 100
                    ELSE 10
                END
            ) as relevance_score';
            
            $productsQuery = DB::table('products')
                ->selectRaw('products.*, ' . $caseSql, $caseBindings)
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->where(function($query) use ($searchTerm, $exactTerm, $cleanQuery, $tokens, $normalizedQuery) {
                    // Aggressive normalized full phrase match
                    $query->whereRaw('REPLACE(LOWER(products.name), " ", "") = ?', [$normalizedQuery]);
                    
                    // Full phrase match
                    $query->orWhere('products.name', 'like', $searchTerm)
                        ->orWhere('products.description', 'like', $searchTerm)
                        ->orWhere('products.name', 'like', $exactTerm . '%');
                    
                    // All tokens AND match
                    if (count($tokens) > 1) {
                        $query->orWhere(function($q) use ($tokens) {
                            foreach ($tokens as $token) {
                                $q->where('products.name', 'like', '%' . $token . '%');
                            }
                        });
                    }
                    
                    // Any token match
                    foreach ($tokens as $token) {
                        $query->orWhere('products.name', 'like', '%' . $token . '%')
                              ->orWhere('products.description', 'like', '%' . $token . '%');
                    }
                    
                    // Brand and category matching (optional, lower priority)
                    $query->orWhere('brands.name', 'like', $searchTerm)
                          ->orWhere('categories.name', 'like', $searchTerm);
                })
                ->orderBy('relevance_score', 'desc')
                ->orderBy('products.name', 'asc');
            
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

                // PHP-side normalization for top match
                $normalizedQuery = preg_replace('/[^a-z0-9]+/', '', strtolower($cleanQuery));
                $productsWithScores = $productModels->map(function ($product) use ($normalizedQuery, $paginatedResults) {
                    $normalizedProductName = preg_replace('/[^a-z0-9]+/', '', strtolower($product->name));
                    $php_relevance_score = ($normalizedProductName === $normalizedQuery) ? 2000 : 0;
                    // Find the original SQL relevance_score from the paginated results
                    $sqlScore = 0;
                    foreach ($paginatedResults->items() as $item) {
                        if ($item->id == $product->id && isset($item->relevance_score)) {
                            $sqlScore = $item->relevance_score;
                            break;
                        }
                    }
                    $product->php_relevance_score = $php_relevance_score;
                    $product->sql_relevance_score = $sqlScore;
                    return $product;
                });
                // Resort by php_relevance_score desc, then sql_relevance_score desc, then name asc
                $sortedProducts = $productsWithScores->sort(function($a, $b) {
                    if ($b->php_relevance_score !== $a->php_relevance_score) {
                        return $b->php_relevance_score <=> $a->php_relevance_score;
                    }
                    if ($b->sql_relevance_score !== $a->sql_relevance_score) {
                        return $b->sql_relevance_score <=> $a->sql_relevance_score;
                    }
                    return strcmp($a->name, $b->name);
                })->values();

                // Create a new custom pagination instance
                $products = new \Illuminate\Pagination\LengthAwarePaginator(
                    $sortedProducts,
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
     * Clean search query by removing brand names and extra formatting
     */
    private function cleanSearchQuery($query)
    {
        // Remove brand names (format: "Product Name - Brand Name")
        $query = preg_replace('/\s*-\s*[^-]+$/', '', $query);
        
        // Remove common brand indicators
        $query = preg_replace('/\s*\([^)]*\)/', '', $query);
        $query = preg_replace('/\s*\[[^\]]*\]/', '', $query);
        
        // Remove trademark symbols and other special characters
        $query = preg_replace('/[®™©]/', '', $query);
        
        // Remove common words that don't help with search
        $commonWords = ['test', 'rapid', 'kit', 'cassette', 'strip', 'device'];
        $query = preg_replace('/\b(' . implode('|', $commonWords) . ')\b/i', '', $query);
        
        // Clean up extra whitespace
        $query = preg_replace('/\s+/', ' ', $query);
        
        return trim($query);
    }

    /**
     * Tokenize search query for better multi-word matching
     */
    private function tokenizeSearchQuery($query)
    {
        // Split by common delimiters
        $tokens = preg_split('/[\s\-_.,()]+/', $query);
        
        // Filter out common stop words and short tokens
        $stopWords = ['the', 'and', 'or', 'for', 'with', 'in', 'on', 'at', 'to', 'of', 'a', 'an'];
        $tokens = array_filter($tokens, function($token) use ($stopWords) {
            return strlen($token) >= 2 && !in_array(strtolower($token), $stopWords);
        });
        
        return array_unique($tokens);
    }

    /**
     * Generate fuzzy search terms for typo tolerance
     */
    private function generateFuzzyTerms($term)
    {
        $fuzzyTerms = [];
        
        // Common medical/laboratory term variations
        $variations = [
            'test' => ['testing', 'tests'],
            'rapid' => ['quick', 'fast'],
            'kit' => ['kits', 'package'],
            'blood' => ['haemoglobin', 'hemoglobin'],
            'urine' => ['urinalysis', 'urinary'],
            'stool' => ['fecal', 'feces'],
            'pregnancy' => ['hcg', 'human chorionic gonadotropin'],
            'covid' => ['coronavirus', 'sars-cov-2'],
            'hiv' => ['human immunodeficiency virus'],
            'hepatitis' => ['hep'],
            'syphilis' => ['treponema'],
            'malaria' => ['plasmodium'],
            'dengue' => ['denv'],
            'chlamydia' => ['chlamydia trachomatis'],
            'gonorrhea' => ['neisseria gonorrhoeae'],
        ];
        
        foreach ($variations as $key => $synonyms) {
            if (stripos($term, $key) !== false) {
                $fuzzyTerms = array_merge($fuzzyTerms, $synonyms);
            }
        }
        
        return array_unique($fuzzyTerms);
    }

    /**
     * Get medical/laboratory synonyms for semantic search
     */
    private function getMedicalSynonyms($term)
    {
        $synonyms = [];
        
        // Medical term synonyms
        $medicalSynonyms = [
            'blood' => ['serum', 'plasma', 'whole blood'],
            'urine' => ['urinalysis', 'urinary'],
            'stool' => ['fecal', 'feces', 'bowel'],
            'pregnancy' => ['hcg', 'human chorionic gonadotropin', 'gestation'],
            'covid' => ['coronavirus', 'sars-cov-2', 'covid-19'],
            'hiv' => ['human immunodeficiency virus', 'aids'],
            'hepatitis' => ['hep', 'liver disease'],
            'syphilis' => ['treponema', 'venereal disease'],
            'malaria' => ['plasmodium', 'tropical disease'],
            'dengue' => ['denv', 'breakbone fever'],
            'chlamydia' => ['chlamydia trachomatis', 'std'],
            'gonorrhea' => ['neisseria gonorrhoeae', 'std'],
            'test' => ['assay', 'examination', 'analysis'],
            'rapid' => ['quick', 'fast', 'instant'],
            'kit' => ['package', 'set', 'collection'],
            'cassette' => ['cartridge', 'device'],
            'strip' => ['test strip', 'dipstick'],
            'device' => ['instrument', 'equipment', 'apparatus'],
        ];
        
        foreach ($medicalSynonyms as $key => $synonymList) {
            if (stripos($term, $key) !== false) {
                $synonyms = array_merge($synonyms, $synonymList);
            }
        }
        
        return array_unique($synonyms);
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query || strlen($query) < 2) {
            return response()->json(['completions' => []]);
        }
        
        try {
            // Normalize search terms
            $searchTerm = '%' . strtolower(trim($query)) . '%';
            $exactTerm = strtolower(trim($query));
            
            // Log the autocomplete query
            Log::info("Autocomplete request: " . $query);
            
            // Enhanced product suggestions with medical synonyms
            $productSuggestions = DB::table('products')
                ->select([
                    'products.name',
                    'brands.name as brand_name',
                    'products.image_url'
                ])
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->where(function($q) use ($searchTerm, $exactTerm) {
                    // Exact name match
                    $q->where(DB::raw('LOWER(products.name)'), '=', $exactTerm);
                    // Starts with search term
                    $q->orWhere(DB::raw('LOWER(products.name)'), 'like', $exactTerm . '%');
                    // Contains search term
                    $q->orWhere(DB::raw('LOWER(products.name)'), 'like', '%' . $exactTerm . '%');
                    
                    // Enhanced token-based matching
                    $tokens = $this->tokenizeSearchQuery($exactTerm);
                    foreach ($tokens as $token) {
                        if (strlen($token) >= 2) {
                            $q->orWhere(DB::raw('LOWER(products.name)'), 'like', '%' . $token . '%');
                        }
                    }
                    
                    // Medical synonyms for better suggestions
                    $semanticTerms = $this->getMedicalSynonyms($exactTerm);
                    foreach ($semanticTerms as $semanticTerm) {
                        $q->orWhere(DB::raw('LOWER(products.name)'), 'like', '%' . $semanticTerm . '%');
                    }
                })
                ->orderByRaw('
                    CASE 
                        WHEN LOWER(products.name) = ? THEN 1
                        WHEN LOWER(products.name) LIKE ? THEN 2
                        WHEN LOWER(products.name) LIKE ? THEN 3
                        ELSE 4
                    END
                ', [$exactTerm, $exactTerm . '%', '%' . $exactTerm . '%'])
                ->orderBy('products.name', 'asc')
                ->distinct()
                ->limit(8)
                ->get();
                
            // Enhanced category suggestions
            $categorySuggestions = DB::table('categories')
                ->select('name', 'slug')
                ->where(function($q) use ($searchTerm, $exactTerm) {
                    $q->where(DB::raw('LOWER(name)'), 'like', $searchTerm);
                    
                    // Include medical synonyms for categories
                    $semanticTerms = $this->getMedicalSynonyms($exactTerm);
                    foreach ($semanticTerms as $semanticTerm) {
                        $q->orWhere(DB::raw('LOWER(name)'), 'like', '%' . $semanticTerm . '%');
                    }
                })
                ->orderByRaw('
                    CASE 
                        WHEN LOWER(name) = ? THEN 1
                        WHEN LOWER(name) LIKE ? THEN 2
                        ELSE 3
                    END
                ', [$exactTerm, $exactTerm . '%'])
                ->orderBy('name', 'asc')
                ->distinct()
                ->limit(3)
                ->get();
                
            // Enhanced brand suggestions
            $brandSuggestions = DB::table('brands')
                ->select('name')
                ->where(function($q) use ($searchTerm, $exactTerm) {
                    $q->where(DB::raw('LOWER(name)'), 'like', $searchTerm);
                    
                    // Include fuzzy matching for brand names
                    $fuzzyTerms = $this->generateFuzzyTerms($exactTerm);
                    foreach ($fuzzyTerms as $fuzzyTerm) {
                        $q->orWhere(DB::raw('LOWER(name)'), 'like', '%' . $fuzzyTerm . '%');
                    }
                })
                ->orderByRaw('
                    CASE 
                        WHEN LOWER(name) = ? THEN 1 
                        WHEN LOWER(name) LIKE ? THEN 2
                        ELSE 3
                    END
                ', [$exactTerm, $exactTerm . '%'])
                ->orderBy('name', 'asc')
                ->distinct()
                ->limit(2)
                ->get();
                
            // Format product suggestions with brand info and image
            $formattedProductSuggestions = $productSuggestions->map(function($product) {
                $text = $product->name;
                if ($product->brand_name) {
                    $text .= ' - ' . $product->brand_name;
                }
                return [
                    'text' => $text,
                    'image_url' => $product->image_url,
                    'type' => 'product',
                ];
            })->toArray();
            
            // Format category and brand suggestions as text only
            $formattedCategorySuggestions = $categorySuggestions->map(function($cat) {
                return [
                    'text' => $cat->name,
                    'slug' => $cat->slug,
                    'image_url' => null,
                    'type' => 'category',
                ];
            })->toArray();
            $formattedBrandSuggestions = $brandSuggestions->map(function($brand) {
                return [
                    'text' => $brand->name,
                    'image_url' => null,
                    'type' => 'brand',
                ];
            })->toArray();
            
            // Merge all suggestions
            $allSuggestions = array_merge(
                $formattedProductSuggestions,
                $formattedCategorySuggestions,
                $formattedBrandSuggestions
            );
            
            // Remove duplicates and limit to 10 suggestions
            $uniqueSuggestions = [];
            foreach ($allSuggestions as $suggestion) {
                if (!isset($uniqueSuggestions[$suggestion['text']])) {
                    $uniqueSuggestions[$suggestion['text']] = $suggestion;
                }
            }
            $finalSuggestions = array_slice(array_values($uniqueSuggestions), 0, 10);
            
            // Log the number of suggestions
            Log::info("Returning " . count($finalSuggestions) . " search suggestions");
            
            return response()->json([
                'completions' => $finalSuggestions
            ]);
            
        } catch (\Exception $e) {
            Log::error("Search suggestions error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['completions' => []]);
        }
    }
} 