<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory', 'brand', 'images']);
        
        // Filter by product name
        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        
        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
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

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'inventory', 'images']);
        
        return view('admin.products.show', compact('product'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5000',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10000',
            'has_size_options' => 'nullable|boolean',
            'size_options' => 'nullable|array',
            'size_options.*' => 'nullable|string|max:50'
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Create the product first (SKU will be auto-generated based on brand)
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'price_aed' => $validated['price_aed'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'image_url' => null, // Will be replaced by primary image
                'has_size_options' => $request->has('has_size_options'),
                'size_options' => $request->has('has_size_options') && $request->filled('size_options') ? 
                                  json_encode(array_filter($request->size_options)) : null,
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

            // Handle specification image if uploaded
            if ($request->hasFile('specification_image')) {
                $path = $request->file('specification_image')->store('products/specifications', 'public');
                $imageUrl = asset('storage/' . $path);
                
                $product->images()->create([
                    'image_path' => $path,
                    'image_url' => $imageUrl,
                    'specification_image_url' => $imageUrl,
                    'is_primary' => false,
                    'sort_order' => 999 // High sort order to appear at the end
                ]);
            }

            // Handle PDF file if uploaded
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

        try {
            DB::transaction(function () use ($request, $product, $validated) {
                // Update product details
                $oldBrandId = $product->brand_id;
                $product->update([
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'price' => $validated['price'],
                    'price_aed' => $validated['price_aed'],
                    'category_id' => $validated['category_id'],
                    'brand_id' => $validated['brand_id'],
                    'has_size_options' => $request->has('has_size_options'),
                    'size_options' => $request->has('has_size_options') && $request->filled('size_options') ? 
                                    json_encode(array_filter($request->size_options)) : null,
                ]);

                // If brand changed, regenerate SKU
                if ($oldBrandId != $validated['brand_id']) {
                    $newSku = Product::generateSku(null, $validated['brand_id']);
                    $product->update(['sku' => $newSku]);
                }

                // Update inventory quantity
                $product->inventory->update([
                    'quantity' => $validated['inventory_quantity']
                ]);

                // Handle primary image upload if provided
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    Log::info('Attempting to upload image', [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'error' => $file->getError()
                    ]);

                    // Ensure the storage directory exists
                    $storagePath = 'products';
                    if (!Storage::disk('public')->exists($storagePath)) {
                        Storage::disk('public')->makeDirectory($storagePath);
                        Log::info('Created storage directory', ['path' => $storagePath]);
                    }

                    // Store the file
                    $path = $file->store($storagePath, 'public');
                    if (!$path) {
                        throw new \Exception('Failed to store file in storage');
                    }
                    
                    Log::info('File stored successfully', ['path' => $path]);
                    
                    // Generate the full URL using the correct path
                    $imageUrl = url('storage/' . $path);
                    Log::info('Generated image URL', ['url' => $imageUrl]);
                    
                    // If this product already has a primary image, update it
                    if ($primaryImage = $product->images()->where('is_primary', true)->first()) {
                        Log::info('Updating existing primary image', ['image_id' => $primaryImage->id]);
                        
                        // Delete the old primary image file
                        if ($primaryImage->image_path && Storage::disk('public')->exists($primaryImage->image_path)) {
                            Storage::disk('public')->delete($primaryImage->image_path);
                            Log::info('Old primary image deleted', ['path' => $primaryImage->image_path]);
                        }
                        
                        // Update the primary image record
                        $primaryImage->update([
                            'image_path' => $path,
                            'image_url' => $imageUrl
                        ]);
                        Log::info('Primary image record updated');
                    } else {
                        Log::info('Creating new primary image record');
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
                    Log::info('Product image_url updated', ['url' => $imageUrl]);
                }

                // Handle additional images
                if ($request->hasFile('additional_images')) {
                    try {
                        $storagePath = 'products';
                        $sortOrder = $product->images()->max('sort_order') + 1;
                        foreach ($request->file('additional_images') as $image) {
                            $path = $image->store($storagePath, 'public');
                            if (!$path) {
                                throw new \Exception('Failed to store additional image');
                            }
                            
                            $imageUrl = url('storage/' . $path);
                            
                            $product->images()->create([
                                'image_path' => $path,
                                'image_url' => $imageUrl,
                                'is_primary' => false,
                                'sort_order' => $sortOrder++
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Additional images upload failed', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw new \Exception('Failed to upload additional images: ' . $e->getMessage());
                    }
                }

                // Handle specification image if uploaded
                if ($request->hasFile('specification_image')) {
                    try {
                        $specPath = 'products/specifications';
                        if (!Storage::disk('public')->exists($specPath)) {
                            Storage::disk('public')->makeDirectory($specPath);
                        }
                        
                        $path = $request->file('specification_image')->store($specPath, 'public');
                        if (!$path) {
                            throw new \Exception('Failed to store specification image');
                        }
                        
                        $imageUrl = url('storage/' . $path);
                        
                        // Check if there's an existing specification image
                        $existingSpecImage = $product->images()->whereNotNull('specification_image_url')->first();
                        
                        if ($existingSpecImage) {
                            // Delete the old specification image file
                            if ($existingSpecImage->image_path && Storage::disk('public')->exists($existingSpecImage->image_path)) {
                                Storage::disk('public')->delete($existingSpecImage->image_path);
                            }
                            
                            // Update the existing record
                            $existingSpecImage->update([
                                'image_path' => $path,
                                'image_url' => $imageUrl,
                                'specification_image_url' => $imageUrl
                            ]);
                        } else {
                            // Create a new specification image record
                            $product->images()->create([
                                'image_path' => $path,
                                'image_url' => $imageUrl,
                                'specification_image_url' => $imageUrl,
                                'is_primary' => false,
                                'sort_order' => 999
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Specification image upload failed', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw new \Exception('Failed to upload specification image: ' . $e->getMessage());
                    }
                }

                // Handle PDF file if uploaded
                if ($request->hasFile('pdf_file')) {
                    // Delete old PDF if exists
                    if ($product->pdf_file && Storage::disk('public')->exists($product->pdf_file)) {
                        Storage::disk('public')->delete($product->pdf_file);
                    }
                    
                    $path = $request->file('pdf_file')->store('products/pdfs', 'public');
                    $product->update(['pdf_file' => $path]);
                }

                // Handle PDF deletion if requested
                if ($request->has('delete_pdf') && $request->delete_pdf == '1') {
                    if ($product->pdf_file && Storage::disk('public')->exists($product->pdf_file)) {
                        Storage::disk('public')->delete($product->pdf_file);
                        $product->update(['pdf_file' => null]);
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
        } catch (\Exception $e) {
            Log::error('Product update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()]);
        }
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

        // Delete the PDF file if it exists
        if ($product->pdf_file && Storage::disk('public')->exists($product->pdf_file)) {
            Storage::disk('public')->delete($product->pdf_file);
        }

        // Delete the inventory record
        $product->inventory()->delete();

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }
}
