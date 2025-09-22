<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:brands.view')->only(['index', 'show']);
        $this->middleware('permission:brands.create')->only(['create', 'store', 'storeAjax']);
        $this->middleware('permission:brands.edit')->only(['edit', 'update']);
        $this->middleware('permission:brands.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the brands.
     */
    public function index()
    {
        $brands = Brand::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'logo' => 'nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048',
            'sort_order' => 'nullable|integer'
        ]);

        // Generate slug from name
        $slug = Str::slug($validated['name']);
        
        // Process logo upload if provided
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $logoUrl = asset('storage/' . $path);
        }

        // Create the brand
        Brand::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'logo_url' => $logoUrl,
            'is_featured' => $request->has('is_featured'),
            'sort_order' => $validated['sort_order'] ?? 0
        ]);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully');
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048',
            'sort_order' => 'nullable|integer'
        ]);

        // Generate slug from name if name has changed
        if ($brand->name !== $validated['name']) {
            $brand->slug = Str::slug($validated['name']);
        }
        
        // Process logo upload if provided
        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($brand->logo_url) {
                $path = str_replace(asset('storage/'), '', $brand->logo_url);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            
            // Store new logo
            $path = $request->file('logo')->store('brands', 'public');
            $brand->logo_url = asset('storage/' . $path);
        }

        // Update the brand
        $brand->name = $validated['name'];
        $brand->description = $validated['description'] ?? null;
        $brand->is_featured = $request->has('is_featured');
        $brand->sort_order = $validated['sort_order'] ?? 0;
        $brand->save();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully');
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand)
    {
        // Check if brand has products
        if ($brand->products()->exists()) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'Cannot delete brand with associated products.');
        }
        
        // Delete logo if it exists
        if ($brand->logo_url) {
            $path = str_replace(asset('storage/'), '', $brand->logo_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        
        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully');
    }

    /**
     * Store a newly created brand via AJAX and return JSON.
     */
    public function storeAjax(Request $request)
    {
        // Authorization handled by controller middleware on constructor
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'logo' => 'nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048',
        ]);

        $slug = Str::slug($validated['name']);

        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $logoUrl = asset('storage/' . $path);
        }

        $brand = Brand::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'logo_url' => $logoUrl,
            'is_featured' => false,
            'sort_order' => $validated['sort_order'] ?? 0
        ]);

        return response()->json([
            'success' => true,
            'brand' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'logo_url' => $brand->logo_url,
            ],
            'message' => 'Brand created successfully.'
        ]);
    }
}
