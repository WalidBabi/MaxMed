<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
                    ->with(['subcategories', 'products'])
                    ->get();
        return view('products.index', compact('categories'));
    }

    public function show(Request $request, Category $category)
    {
        // Check if URL is www version and redirect to non-www
        if (strpos($request->getHost(), 'www.') === 0) {
            return redirect()->to('https://maxmedme.com' . $request->getRequestUri(), 301);
        }
        
        // Check if this category has subcategories or products
        $hasContent = $category->subcategories->isNotEmpty() || 
                     Product::where('category_id', $category->id)->exists();
        
        if (!$hasContent) {
            // Share with view that this page has no content (for noindex)
            View::share('emptyCategory', true);
        }
        
        if ($category->subcategories->isNotEmpty()) {
            // Eager load products for subcategories
            $category->load(['subcategories.products']);
            return view('categories.subcategories', compact('category'));
        }

        // Start building the query
        $query = Product::with(['category', 'inventory'])->where('category_id', $category->id);
        
        // Apply all filters
        $query = $this->applyFilters($request, $query);
        
        // Get paginated results
        $products = $query->paginate(16);
        $products->appends($request->all());
        
        return view('categories.products', compact('category', 'products'));
    }

    public function showSubcategory(Request $request, $category_slug, $subcategory_slug)
    {
        // Check if URL is www version and redirect to non-www
        if (strpos($request->getHost(), 'www.') === 0) {
            return redirect()->to('https://maxmedme.com' . $request->getRequestUri(), 301);
        }
        
        // Find categories by slug
        $category = Category::where('slug', $category_slug)->firstOrFail();
        $subcategory = Category::where('slug', $subcategory_slug)->where('parent_id', $category->id)->firstOrFail();
        
        // Check if this subcategory has content
        $hasContent = $subcategory->subcategories->isNotEmpty() || 
                     Product::where('category_id', $subcategory->id)->exists();
        
        if (!$hasContent) {
            // Share with view that this page has no content (for noindex)
            View::share('emptyCategory', true);
        }
        
        if ($subcategory->subcategories->isNotEmpty()) {
            // Eager load products for subsubcategories
            $subcategory->load(['subcategories.products']);
            return view('categories.subsubcategories', compact('category', 'subcategory'));
        }
        
        // Start building the query
        $query = Product::with(['category', 'inventory'])->where('category_id', $subcategory->id);
        
        // Apply all filters
        $query = $this->applyFilters($request, $query);
        
        // Get paginated results
        $products = $query->paginate(16);
        $products->appends($request->all());
        
        return view('categories.products', compact('subcategory', 'products'));
    }

    public function showSubSubcategory(Request $request, $category_slug, $subcategory_slug, $subsubcategory_slug)
    {
        // Check if URL is www version and redirect to non-www
        if (strpos($request->getHost(), 'www.') === 0) {
            return redirect()->to('https://maxmedme.com' . $request->getRequestUri(), 301);
        }
        
        // Find categories by slug
        $category = Category::where('slug', $category_slug)->firstOrFail();
        $subcategory = Category::where('slug', $subcategory_slug)->where('parent_id', $category->id)->firstOrFail();
        $subsubcategory = Category::where('slug', $subsubcategory_slug)->where('parent_id', $subcategory->id)->firstOrFail();
        
        // Check if this subsubcategory has content
        $hasContent = $subsubcategory->subcategories->isNotEmpty() || 
                     Product::where('category_id', $subsubcategory->id)->exists();
        
        if (!$hasContent) {
            // Share with view that this page has no content (for noindex)
            View::share('emptyCategory', true);
        }
        
        // Check if this subsubcategory has subcategories
        if ($subsubcategory->subcategories->isNotEmpty()) {
            // Eager load products for showing counts
            $subsubcategory->load(['subcategories.products']);
            return view('categories.subsubsubcategories', compact('category', 'subcategory', 'subsubcategory'));
        }
        
        // Start building the query
        $query = Product::with(['category', 'inventory'])->where('category_id', $subsubcategory->id);
        
        // Apply all filters
        $query = $this->applyFilters($request, $query);
        
        // Get paginated results
        $products = $query->paginate(16);
        $products->appends($request->all());
        
        return view('categories.products', compact('subsubcategory', 'products'));
    }

    public function showSubSubSubcategory(Request $request, $category_slug, $subcategory_slug, $subsubcategory_slug, $subsubsubcategory_slug)
    {
        // Check if URL is www version and redirect to non-www
        if (strpos($request->getHost(), 'www.') === 0) {
            return redirect()->to('https://maxmedme.com' . $request->getRequestUri(), 301);
        }
        
        // Find categories by slug
        $category = Category::where('slug', $category_slug)->firstOrFail();
        $subcategory = Category::where('slug', $subcategory_slug)->where('parent_id', $category->id)->firstOrFail();
        $subsubcategory = Category::where('slug', $subsubcategory_slug)->where('parent_id', $subcategory->id)->firstOrFail();
        $subsubsubcategory = Category::where('slug', $subsubsubcategory_slug)->where('parent_id', $subsubcategory->id)->firstOrFail();
        
        // Check if this subsubsubcategory has content
        $hasContent = Product::where('category_id', $subsubsubcategory->id)->exists();
        
        if (!$hasContent) {
            // Share with view that this page has no content (for noindex)
            View::share('emptyCategory', true);
        }
        
        // Start building the query
        $query = Product::with(['category', 'inventory'])->where('category_id', $subsubsubcategory->id);
        
        // Apply all filters
        $query = $this->applyFilters($request, $query);
        
        // Get paginated results
        $products = $query->paginate(16);
        $products->appends($request->all());
        
        return view('categories.products', compact('subsubsubcategory', 'products'));
    }
    
    /**
     * Apply all filters from the request to the query
     *
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyFilters(Request $request, $query)
    {
        // Filter by search query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
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
            // Assuming you have a brand field in products
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
        
        return $query;
    }
} 