<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use App\Services\SeoService;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Check if URL is www version and redirect to non-www
        if (strpos($request->getHost(), 'www.') === 0) {
            return redirect()->to('https://maxmedme.com' . $request->getRequestUri(), 301);
        }
        
        $query = Product::with(['category', 'inventory']); // eager load category and inventory
        
        // Filter by search query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Get categories for sidebar and filtering
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
        
        // Filter by category
        if ($categoryName = $request->input('category')) {
            // Normalize category name by removing special characters for comparison
            $normalizedCategoryName = preg_replace('/[^\w\s]/', '', $categoryName);
            
            $query->whereHas('category', function ($q) use ($normalizedCategoryName, $categoryName) {
                $q->where('name', 'like', "%{$normalizedCategoryName}%")
                  ->orWhere('name', 'like', "%{$categoryName}%");
            });
            
            // Try to find a matching category for better structured URLs
            $matchingCategory = Category::where('name', 'like', "%{$normalizedCategoryName}%")
                ->orWhere('name', 'like', "%{$categoryName}%")
                ->first();
                
            // Don't redirect from /products?category=X URLs to allow these pages to be indexed
            // This helps with SEO for category-specific product listings
        }
        
        // Filter for industry-specific subcategories (used in "Explore Solutions" buttons)
        if ($industryFor = $request->input('for')) {
            $industryMapping = [
                // Healthcare & Medical Facilities subcategories
                'clinics' => ['Point-of-care diagnostics', 'Exam room equipment', 'Medical supplies'],
                'hospitals' => ['Surgical instruments', 'Hospital equipment', 'Patient monitoring', 'Infection control'],
                'veterinary' => ['Veterinary equipment', 'Animal care', 'Veterinary diagnostics'],
                'medical-labs' => ['Laboratory equipment', 'Clinical analyzers', 'Lab consumables'],
                
                // Scientific & Research Institutions subcategories
                'research-labs' => ['Research equipment', 'Analytical instruments', 'Laboratory supplies'],
                'academia' => ['Educational lab equipment', 'Teaching supplies', 'Research instruments'],
                'biotech-pharma' => ['Biotech equipment', 'Pharmaceutical testing', 'Cell culture'],
                'forensic' => ['Forensic equipment', 'Evidence collection', 'Analysis tools'],
                
                // Specialized Testing & Diagnostics subcategories
                'environment' => ['Environmental testing', 'Air quality', 'Water analysis', 'Soil testing'],
                'food' => ['Food testing equipment', 'Safety analysis', 'Quality control'],
                'material' => ['Material testing', 'Strength analysis', 'Composition testing'],
                'cosmetic' => ['Cosmetic testing', 'Dermatology equipment', 'Safety analysis'],
                
                // Government & Regulatory Bodies subcategories
                'public-health' => ['Public health monitoring', 'Disease surveillance', 'Community screening'],
                'military-defense' => ['Military medical equipment', 'Field diagnostics', 'Research tools'],
                'regulatory' => ['Quality control systems', 'Compliance tools', 'Inspection equipment'],
                
                // Emerging & AI-driven Healthcare subcategories
                'telemedicine' => ['Telehealth systems', 'Remote monitoring', 'Digital diagnostics'],
                'ai-medical' => ['AI development tools', 'Medical imaging analysis', 'Healthcare data systems']
            ];
            
            // If the requested industry subcategory exists in our mapping
            if (isset($industryMapping[$industryFor])) {
                $relatedCategories = $industryMapping[$industryFor];
                
                // Filter products by these related categories using 'whereHas' relationship
                $query->where(function($q) use ($relatedCategories) {
                    foreach($relatedCategories as $category) {
                        $q->orWhereHas('category', function($subQ) use ($category) {
                            $subQ->where('name', 'like', "%{$category}%");
                        });
                    }
                });
                
                // Set a view variable to display the industry subcategory name
                $industryCategory = str_replace('-', ' ', ucwords($industryFor, '-'));
            }
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
        
        // Use industry-specific view if requested
        if (isset($industryCategory) && $request->input('for')) {
            return view('products.industry', compact('products', 'categories', 'industryCategory', 'industryFor'));
        }
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Product $product)
    {
        // Check if URL is www version and redirect to non-www
        if (strpos($request->getHost(), 'www.') === 0) {
            return redirect()->to('https://maxmedme.com' . $request->getRequestUri(), 301);
        }

        // Load necessary relationships
        $product->load(['category', 'brand', 'specifications' => function($query) {
            $query->orderBy('category', 'asc')->orderBy('sort_order', 'asc');
        }]);

        // Group specifications by category
        $specsByCategory = $product->specifications->groupBy('category');

        // Generate SEO data
        $seoData = [
            'title' => "{$product->name} | MaxMed UAE",
            'meta_description' => "Buy {$product->name} from MaxMed UAE. " . Str::limit(strip_tags($product->description), 150),
            'meta_keywords' => implode(', ', [
                $product->name,
                $product->category ? $product->category->name : '',
                $product->brand ? $product->brand->name : '',
                'laboratory equipment',
                'medical supplies',
                'UAE',
                'Dubai'
            ]),
            'og_title' => $product->name,
            'og_description' => Str::limit(strip_tags($product->description), 200),
            'og_image' => $product->image_url ?? asset('Images/banner2.jpeg')
        ];

        return view('products.show', compact('product', 'specsByCategory', 'seoData'));
    }
    
    public function checkAvailability(Request $request, Product $product, int $quantity): JsonResponse
    {
        // Check if URL is www version and redirect to non-www for API calls
        if (strpos($request->getHost(), 'www.') === 0) {
            return response()->json([
                'redirect' => 'https://maxmedme.com/check-availability/' . $product->id . '/' . $quantity
            ], 301);
        }
        
        return response()->json([
            'available' => $product->inventory->quantity >= $quantity
        ]);
    }

    public function showProducts(Request $request)
    {
        // Check if URL is www version and redirect to non-www
        if (strpos($request->getHost(), 'www.') === 0) {
            return redirect()->to('https://maxmedme.com/showproducts', 301);
        }
        
        // Retrieve all products or apply any specific logic
        $products = Product::all(); // or use a query to filter/sort products

        // Pass the products to the view
        return view('products.show', compact('products'));
    }

    // You can add more methods like create, store, edit, update, destroy as needed
} 