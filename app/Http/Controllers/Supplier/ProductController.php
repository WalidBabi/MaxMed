<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\ProductSpecificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $specificationService;

    public function __construct(ProductSpecificationService $specificationService)
    {
        $this->specificationService = $specificationService;
    }

    /**
     * Display a listing of supplier's products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'inventory', 'images'])
            ->where('supplier_id', Auth::id());

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->latest()->paginate(12);
        
        // Only show categories that the supplier is assigned to
        $assignedCategoryIds = Auth::user()->activeAssignedCategories->pluck('id');
        $categories = Category::whereIn('id', $assignedCategoryIds)->get();

        return view('supplier.products.index', compact('products', 'categories'));
    }

    /**
     * Show the specified product.
     */
    public function show(Product $product)
    {
        // Check if user owns this product
        if ($product->supplier_id !== Auth::id()) {
            abort(403, 'You can only view your own products.');
        }

        if (!Auth::user()->hasPermission('supplier.products.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Load product with relationships
        $product->load([
            'specifications' => function($query) {
                $query->orderBy('category', 'asc')
                      ->orderBy('sort_order', 'asc');
            }, 
            'category', 
            'brand', 
            'images' => function($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'inventory'
        ]);
        
        // Group specifications by category
        $specsByCategory = $product->specifications->groupBy('category');
        
        // Get category-specific templates for comparison
        $templates = $this->specificationService->getCategorySpecificationTemplates($product->category_id);
        
        return view('supplier.products.show', compact('product', 'specsByCategory', 'templates'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('supplier.products.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Only show categories that the supplier is assigned to
        $assignedCategoryIds = Auth::user()->activeAssignedCategories->pluck('id');
        $categories = Category::whereIn('id', $assignedCategoryIds)->get();
        
        // Get or create supplier's brand based on their company name
        $supplierBrand = Auth::user()->getOrCreateSupplierBrand();
        
        return view('supplier.products.create', compact('categories', 'supplierBrand'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('supplier.products.create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5000',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5000',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5000',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10000',
            'has_size_options' => 'nullable|boolean',
            'size_options' => 'nullable|array',
            'size_options.*' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Get or create supplier's brand based on their company name
            $supplierBrand = Auth::user()->getOrCreateSupplierBrand();

            // Create product with supplier_id and supplier's brand
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'brand_id' => $supplierBrand ? $supplierBrand->id : null,
                'supplier_id' => Auth::id(),
                'price' => 0, // Default price for supplier products
                'price_aed' => 0, // Default price for supplier products
                'has_size_options' => $validated['has_size_options'] ?? false,
                'size_options' => $validated['size_options'] ?? null,
            ]);

            // Create inventory record with 0 stock (to be updated by admin)
            $product->inventory()->create([
                'quantity' => 0,
                'reserved_quantity' => 0,
            ]);

            // Handle primary image
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $imageUrl = asset('storage/' . $path);
                $product->update(['image_url' => $imageUrl]);

                $product->images()->create([
                    'image_path' => $path,
                    'image_url' => $imageUrl,
                    'is_primary' => true,
                    'sort_order' => 0
                ]);
            }

            // Handle specification image
            if ($request->hasFile('specification_image')) {
                $path = $request->file('specification_image')->store('products/specifications', 'public');
                $imageUrl = asset('storage/' . $path);
                
                $product->images()->create([
                    'image_path' => $path,
                    'image_url' => $imageUrl,
                    'specification_image_url' => $imageUrl,
                    'is_primary' => false,
                    'sort_order' => 999
                ]);
            }

            // Handle PDF file
            if ($request->hasFile('pdf_file')) {
                $path = $request->file('pdf_file')->store('products/pdfs', 'public');
                $product->update(['pdf_file' => $path]);
            }

            // Handle additional images
            if ($request->hasFile('additional_images')) {
                $sortOrder = 1;
                foreach ($request->file('additional_images') as $image) {
                    $path = $image->store('products', 'public');
                    $imageUrl = asset('storage/' . $path);
                    
                    $product->images()->create([
                        'image_path' => $path,
                        'image_url' => $imageUrl,
                        'is_primary' => false,
                        'sort_order' => $sortOrder++
                    ]);
                }
            }

            // Handle specifications if provided
            if ($request->filled('specifications')) {
                $specificationsData = $this->transformSpecificationsData($request->specifications, $product->category_id);
                $this->specificationService->saveProductSpecifications($product->id, $specificationsData);
            }
        });

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        // Check if user owns this product
        if ($product->supplier_id !== Auth::id()) {
            abort(403, 'You can only edit your own products.');
        }

        if (!Auth::user()->hasPermission('supplier.products.edit')) {
            abort(403, 'Unauthorized action.');
        }

        // Only show categories that the supplier is assigned to
        $assignedCategoryIds = Auth::user()->activeAssignedCategories->pluck('id');
        $categories = Category::whereIn('id', $assignedCategoryIds)->get();
        
        // Get or create supplier's brand based on their company name
        $supplierBrand = Auth::user()->getOrCreateSupplierBrand();
        
        // Get existing specifications and index them by specification_key for easy access
        $existingSpecsCollection = $this->specificationService->getExistingSpecifications($product->id);
        $existingSpecs = $existingSpecsCollection->keyBy('specification_key');
        
        // Get category-specific templates
        $templates = $this->specificationService->getCategorySpecificationTemplates($product->category_id);
        
        return view('supplier.products.edit', compact('product', 'categories', 'supplierBrand', 'existingSpecs', 'templates'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Check if user owns this product
        if ($product->supplier_id !== Auth::id()) {
            abort(403, 'You can only edit your own products.');
        }

        if (!Auth::user()->hasPermission('supplier.products.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5000',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5000',
            'delete_images' => 'nullable|string',
            'primary_image_id' => 'nullable|exists:product_images,id',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5000',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10000',
            'delete_pdf' => 'nullable|boolean',
            'has_size_options' => 'nullable|boolean',
            'size_options' => 'nullable|array',
            'size_options.*' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $request, $product) {
            // Update product (keeping original prices and brand)
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'has_size_options' => $validated['has_size_options'] ?? false,
                'size_options' => $validated['size_options'] ?? null,
            ]);

            // Handle image deletions
            if ($request->filled('delete_images')) {
                $imageIds = explode(',', $request->input('delete_images'));
                foreach ($imageIds as $imageId) {
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }

            // Handle primary image setting
            if ($request->filled('primary_image_id')) {
                $product->images()->update(['is_primary' => false]);
                $primaryImage = $product->images()->find($request->input('primary_image_id'));
                if ($primaryImage) {
                    $primaryImage->update(['is_primary' => true]);
                    $product->update(['image_url' => $primaryImage->image_url]);
                }
            }

            // Handle new primary image
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $imageUrl = asset('storage/' . $path);
                $product->update(['image_url' => $imageUrl]);

                // Set previous primary images to non-primary
                $product->images()->where('is_primary', true)->update(['is_primary' => false]);

                $product->images()->create([
                    'image_path' => $path,
                    'image_url' => $imageUrl,
                    'is_primary' => true,
                    'sort_order' => 0
                ]);
            }

            // Handle specification image
            if ($request->hasFile('specification_image')) {
                $path = $request->file('specification_image')->store('products/specifications', 'public');
                $imageUrl = asset('storage/' . $path);
                
                $product->images()->create([
                    'image_path' => $path,
                    'image_url' => $imageUrl,
                    'specification_image_url' => $imageUrl,
                    'is_primary' => false,
                    'sort_order' => 999
                ]);
            }

            // Handle PDF file
            if ($request->hasFile('pdf_file')) {
                if ($product->pdf_file) {
                    Storage::disk('public')->delete($product->pdf_file);
                }
                $path = $request->file('pdf_file')->store('products/pdfs', 'public');
                $product->update(['pdf_file' => $path]);
            }

            // Handle PDF deletion
            if ($request->input('delete_pdf')) {
                if ($product->pdf_file) {
                    Storage::disk('public')->delete($product->pdf_file);
                    $product->update(['pdf_file' => null]);
                }
            }

            // Handle additional images
            if ($request->hasFile('additional_images')) {
                $sortOrder = $product->images()->max('sort_order') + 1;
                foreach ($request->file('additional_images') as $image) {
                    $path = $image->store('products', 'public');
                    $imageUrl = asset('storage/' . $path);
                    
                    $product->images()->create([
                        'image_path' => $path,
                        'image_url' => $imageUrl,
                        'is_primary' => false,
                        'sort_order' => $sortOrder++
                    ]);
                }
            }

            // Handle specifications if provided
            if ($request->filled('specifications')) {
                $specificationsData = $this->transformSpecificationsData($request->specifications, $product->category_id);
                $this->specificationService->saveProductSpecifications($product->id, $specificationsData);
            }
        });

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Check if user owns this product
        if ($product->supplier_id !== Auth::id()) {
            abort(403, 'You can only delete your own products.');
        }

        if (!Auth::user()->hasPermission('supplier.products.delete')) {
            abort(403, 'Unauthorized action.');
        }

        DB::transaction(function () use ($product) {
            // Delete associated images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Delete PDF file
            if ($product->pdf_file) {
                Storage::disk('public')->delete($product->pdf_file);
            }

            // Delete primary image
            if ($product->image_url) {
                $imagePath = str_replace(asset('storage/'), '', $product->image_url);
                Storage::disk('public')->delete($imagePath);
            }

            // Delete inventory record
            if ($product->inventory) {
                $product->inventory->delete();
            }

            // Delete the product
            $product->delete();
        });

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Transform specifications form data to the format expected by the service
     */
    private function transformSpecificationsData(array $formSpecifications, int $categoryId): array
    {
        $templates = $this->specificationService->getCategorySpecificationTemplates($categoryId);
        $transformedSpecs = [];
        
        // Flatten the grouped templates into a single array for easier lookup
        $flatTemplates = [];
        foreach ($templates as $categoryName => $categorySpecs) {
            foreach ($categorySpecs as $spec) {
                $flatTemplates[] = $spec;
            }
        }
        
        foreach ($formSpecifications as $key => $value) {
            if (empty($value)) continue;
            
            // Find the template for this specification key
            $template = null;
            foreach ($flatTemplates as $spec) {
                if ($spec['key'] === $key) {
                    $template = $spec;
                    break;
                }
            }
            
            if ($template) {
                $transformedSpecs[] = [
                    'specification_key' => $key,
                    'specification_value' => $value,
                    'unit' => $template['unit'] ?? '',
                    'category' => $template['category'] ?? 'General',
                    'display_name' => $template['name'] ?? $key,
                    'description' => $template['description'] ?? null,
                    'sort_order' => $template['sort_order'] ?? 0,
                    'is_filterable' => $template['is_filterable'] ?? false,
                    'is_searchable' => $template['is_searchable'] ?? false,
                    'show_on_listing' => $template['show_on_listing'] ?? false,
                    'show_on_detail' => $template['show_on_detail'] ?? true,
                ];
            } else {
                // Fallback for custom specifications not in templates
                $transformedSpecs[] = [
                    'specification_key' => $key,
                    'specification_value' => $value,
                    'unit' => null,
                    'category' => 'General',
                    'display_name' => ucwords(str_replace('_', ' ', $key)),
                    'description' => null,
                    'sort_order' => 0,
                    'is_filterable' => false,
                    'is_searchable' => false,
                    'show_on_listing' => false,
                    'show_on_detail' => true,
                ];
            }
        }
        
        return $transformedSpecs;
    }

    /**
     * Get category specifications for API
     */
    public function getCategorySpecifications($categoryId)
    {
        try {
            \Log::info("Supplier API: Requesting specifications for category ID: {$categoryId}");
            
            $templates = $this->specificationService->getCategorySpecificationTemplates($categoryId);
            
            \Log::info("Supplier API: Found " . count($templates) . " template groups for category ID: {$categoryId}");
            
            return response()->json([
                'success' => true,
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            \Log::error("Supplier API: Error loading specifications for category ID {$categoryId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading specifications'
            ], 500);
        }
    }
} 