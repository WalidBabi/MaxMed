<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        // Get top-level categories
        $topCategories = Category::whereNull('parent_id')->get();
        
        // Get second-level categories
        $secondLevelCategories = Category::whereIn('parent_id', $topCategories->pluck('id'))->get();
        
        // Get third-level categories
        $thirdLevelCategories = Category::whereIn('parent_id', $secondLevelCategories->pluck('id'))->get();
        
        // Organize categories for dropdown
        $categoriesForDropdown = [];
        
        // Add top level categories
        foreach ($topCategories as $topCategory) {
            $categoriesForDropdown[$topCategory->id] = $topCategory->name;
        }
        
        // Add second level categories (subcategories)
        foreach ($secondLevelCategories as $subCategory) {
            // Find parent name
            $parentName = $topCategories->where('id', $subCategory->parent_id)->first()->name;
            $categoriesForDropdown[$subCategory->id] = $parentName . ' › ' . $subCategory->name;
        }
        
        // Add third level categories (subsubcategories)
        foreach ($thirdLevelCategories as $subsubCategory) {
            // Find parent and grandparent
            $parent = $secondLevelCategories->where('id', $subsubCategory->parent_id)->first();
            $grandparent = $topCategories->where('id', $parent->parent_id)->first();
            $categoriesForDropdown[$subsubCategory->id] = $grandparent->name . ' › ' . $parent->name . ' › ' . $subsubCategory->name;
        }
        
        return view('admin.categories.create', compact('categoriesForDropdown'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Validate image
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

    public function edit(Category $category)
    {
        // Get top-level categories excluding the current category and its children
        $topCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();
            
        // Get second-level categories excluding the current category
        $secondLevelCategories = Category::whereIn('parent_id', $topCategories->pluck('id'))
            ->where('id', '!=', $category->id)
            ->get();
            
        // Get third-level categories excluding the current category
        $thirdLevelCategories = Category::whereIn('parent_id', $secondLevelCategories->pluck('id'))
            ->where('id', '!=', $category->id)
            ->get();
            
        // Don't allow creating circular dependencies
        $childrenIds = $this->getAllChildrenIds($category);
        
        // Organize categories for dropdown
        $categoriesForDropdown = [];
        
        // Add top level categories
        foreach ($topCategories as $topCategory) {
            if (!in_array($topCategory->id, $childrenIds)) {
                $categoriesForDropdown[$topCategory->id] = $topCategory->name;
            }
        }
        
        // Add second level categories (subcategories)
        foreach ($secondLevelCategories as $subCategory) {
            if (!in_array($subCategory->id, $childrenIds)) {
                // Find parent name
                $parentName = $topCategories->where('id', $subCategory->parent_id)->first()->name;
                $categoriesForDropdown[$subCategory->id] = $parentName . ' › ' . $subCategory->name;
            }
        }
        
        // Add third level categories (subsubcategories)
        foreach ($thirdLevelCategories as $subsubCategory) {
            if (!in_array($subsubCategory->id, $childrenIds)) {
                // Find parent and grandparent names
                $parent = $secondLevelCategories->where('id', $subsubCategory->parent_id)->first();
                $grandparent = $topCategories->where('id', $parent->parent_id)->first();
                $categoriesForDropdown[$subsubCategory->id] = $grandparent->name . ' › ' . $parent->name . ' › ' . $subsubCategory->name;
            }
        }
        
        return view('admin.categories.edit', compact('category', 'categoriesForDropdown'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($category->image_url && \Storage::disk('public')->exists(str_replace(asset('storage/'), '', $category->image_url))) {
                \Storage::disk('public')->delete(str_replace(asset('storage/'), '', $category->image_url));
            }

            // Store new image
            $path = $request->file('image')->store('categories', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
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

    /**
     * Get all children IDs recursively to prevent circular dependencies
     */
    private function getAllChildrenIds(Category $category)
    {
        $ids = [];
        
        foreach ($category->subcategories as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllChildrenIds($child));
        }
        
        return $ids;
    }
} 