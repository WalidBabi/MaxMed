<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

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
        $categories = Category::all();

        return view('supplier.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('supplier.products.create')) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        // Get or create "Yooning" brand
        $yooningBrand = Brand::firstOrCreate(['name' => 'Yooning']);
        
        return view('supplier.products.create', compact('categories', 'yooningBrand'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10000',
            'has_size_options' => 'nullable|boolean',
            'size_options' => 'nullable|array',
            'size_options.*' => 'nullable|string|max:50'
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Get or create "Yooning" brand
            $yooningBrand = Brand::firstOrCreate(['name' => 'Yooning']);

            // Create product with supplier_id and fixed brand
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'brand_id' => $yooningBrand->id,
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

        $categories = Category::all();
        $yooningBrand = Brand::firstOrCreate(['name' => 'Yooning']);
        
        return view('supplier.products.edit', compact('product', 'categories', 'yooningBrand'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'delete_images' => 'nullable|string',
            'primary_image_id' => 'nullable|exists:product_images,id',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10000',
            'delete_pdf' => 'nullable|boolean',
            'has_size_options' => 'nullable|boolean',
            'size_options' => 'nullable|array',
            'size_options.*' => 'nullable|string|max:50'
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
} 