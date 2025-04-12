<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory', 'brand']);
        
        // Filter by product name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        
        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }
        
        // Filter by application
        if ($request->filled('application')) {
            $query->where('application', $request->input('application'));
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }
        
        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->input('stock_status') === 'in_stock') {
                $query->whereHas('inventory', function($q) {
                    $q->where('quantity', '>', 0);
                });
            } elseif ($request->input('stock_status') === 'out_of_stock') {
                $query->whereHas('inventory', function($q) {
                    $q->where('quantity', '=', 0);
                });
            }
        }
        
        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $products = $query->paginate(10);
        
        // Preserve query parameters in pagination links
        $products->appends($request->all());
        
        // Get all categories for the filter dropdown
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        // Fetch categories that do not have subcategories
        $categories = Category::doesntHave('subcategories')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'price_aed' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'application' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5000',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000'
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Create the product first
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'price_aed' => $validated['price_aed'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'application' => $validated['application'] ?? null,
                'image_url' => null, // Will be replaced by primary image
            ]);

            // Create inventory record
            $product->inventory()->create([
                'quantity' => $validated['stock']
            ]);

            // Handle primary image
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $imageUrl = asset('storage/' . $path);
                
                // Save primary image in product_images table
                $primaryImage = $product->images()->create([
                    'image_path' => $path,
                    'image_url' => $imageUrl,
                    'is_primary' => true,
                    'sort_order' => 0
                ]);
                
                // Also save in product's main image_url field for backward compatibility
                $product->update(['image_url' => $imageUrl]);
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

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'price_aed' => 'required|numeric|min:0',
            'inventory_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'application' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            'delete_images' => 'nullable|string',
            'primary_image_id' => 'nullable|exists:product_images,id'
        ]);

        DB::transaction(function () use ($request, $product, $validated) {
            // Update product details
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'price_aed' => $request->price_aed,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'application' => $request->application,
            ]);

            // Update inventory quantity
            $product->inventory->update([
                'quantity' => $request->inventory_quantity
            ]);

            // Handle primary image upload if provided
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $imageUrl = asset('storage/' . $path);
                
                // If this product already has a primary image, update it
                if ($primaryImage = $product->images()->where('is_primary', true)->first()) {
                    // Delete the old primary image file
                    if (Storage::disk('public')->exists($primaryImage->image_path)) {
                        Storage::disk('public')->delete($primaryImage->image_path);
                    }
                    
                    // Update the primary image record
                    $primaryImage->update([
                        'image_path' => $path,
                        'image_url' => $imageUrl
                    ]);
                } else {
                    // Create a new primary image record
                    $product->images()->create([
                        'image_path' => $path,
                        'image_url' => $imageUrl,
                        'is_primary' => true,
                        'sort_order' => 0
                    ]);
                }
                
                // Update product's main image_url field for backward compatibility
                $product->update(['image_url' => $imageUrl]);
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

            // Handle image deletions
            if ($request->filled('delete_images')) {
                $imageIds = explode(',', $request->delete_images);
                
                foreach ($imageIds as $imageId) {
                    $image = $product->images()->find($imageId);
                    
                    if ($image) {
                        // Extract the relative path from the image_path
                        // If image_path already contains the relative path
                        if (Storage::disk('public')->exists($image->image_path)) {
                            Storage::disk('public')->delete($image->image_path);
                        } 
                        // If image_path contains full URL
                        else if (str_contains($image->image_url, 'storage/')) {
                            $relativePath = str_replace(asset('storage/'), '', $image->image_url);
                            if (Storage::disk('public')->exists($relativePath)) {
                                Storage::disk('public')->delete($relativePath);
                            }
                        }
                        
                        // Delete the database record
                        $image->delete();
                    }
                }
            }

            // Handle changing the primary image
            if ($request->filled('primary_image_id')) {
                // Reset all images to non-primary
                $product->images()->update(['is_primary' => false]);
                
                // Set the new primary image
                $newPrimaryImage = $product->images()->find($request->primary_image_id);
                if ($newPrimaryImage) {
                    $newPrimaryImage->update(['is_primary' => true, 'sort_order' => 0]);
                    
                    // Update the product's main image_url field
                    $product->update(['image_url' => $newPrimaryImage->image_url]);
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Delete all product images
        foreach ($product->images as $image) {
            // Extract the relative path from the image_path
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            } 
            // If image_path contains full URL
            else if (str_contains($image->image_url, 'storage/')) {
                $relativePath = str_replace(asset('storage/'), '', $image->image_url);
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }
        }
        
        // Delete the image records
        $product->images()->delete();

        // Delete the main image if it exists
        if ($product->image_url && Storage::disk('public')->exists(str_replace(asset('storage/'), '', $product->image_url))) {
            Storage::disk('public')->delete(str_replace(asset('storage/'), '', $product->image_url));
        }

        // Delete the inventory record
        $product->inventory()->delete();

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }
}
