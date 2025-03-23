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
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = asset('storage/' . $path); // Use asset() to generate URL
        }

        $product = Product::create($validated);

        // Create inventory record
        $product->inventory()->create([
            'quantity' => $validated['stock'] // Save quantity to inventory
        ]);

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($product->image_url && Storage::disk('public')->exists(str_replace(asset('storage/'), '', $product->image_url))) {
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $product->image_url));
            }
            // Store new image
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = asset('storage/' . $path); // Use asset() to generate URL
        }

        DB::transaction(function () use ($request, $product, $validated) {
            // Update product details
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'price_aed' => $request->price_aed,
                'category_id' => $request->category_id,
                'image_url' => $validated['image_url'] ?? $product->image_url,
            ]);

            // Update inventory quantity
            $product->inventory->update([
                'quantity' => $request->inventory_quantity
            ]);
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Delete the image
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
