<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->get(); // Fetch only top-level categories
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $imagePath = asset('storage/' . $path); // Use asset() to generate URL
        }

        Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'image_url' => $imagePath, // Save image path
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function destroy(Category $category)
    {
        // Check if the category has subcategories
        if ($category->subcategories()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete category with subcategories.');
        }

        // Delete the image if it exists
        if ($category->image_url && \Storage::disk('public')->exists(str_replace(asset('storage/'), '', $category->image_url))) {
            \Storage::disk('public')->delete(str_replace(asset('storage/'), '', $category->image_url));
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
} 