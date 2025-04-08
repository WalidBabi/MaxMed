<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('products.index', compact('categories'));
    }

    public function show(Category $category)
    {
        // Check if category exists before proceeding
        if (!$category || (!$category->subcategories->count() && !$category->products->count())) {
            return Redirect::route('products.index')
                ->with('warning', 'The requested category has no content or does not exist.');
        }

        if ($category->subcategories->isNotEmpty()) {
            return view('categories.subcategories', compact('category'));
        }

        $products = $category->products;
        return view('categories.products', compact('category', 'products'));
    }

    public function showSubcategory(Category $category, Category $subcategory)
    {
        // Validate that subcategory belongs to the parent category
        if ($subcategory->parent_id != $category->id) {
            return Redirect::route('products.index')
                ->with('warning', 'The requested subcategory does not exist in this category.');
        }

        
        if ($subcategory->subcategories->isNotEmpty()) {
            return view('categories.subsubcategories', compact('category', 'subcategory'));
        }
        
        $products = $subcategory->products;
        return view('categories.products', compact('subcategory', 'products'));
    }

    public function showSubSubcategory(Category $category, Category $subcategory, Category $subsubcategory)
    {
        // Validate hierarchy
        if ($subcategory->parent_id != $category->id || $subsubcategory->parent_id != $subcategory->id) {
            return Redirect::route('products.index')
                ->with('warning', 'The requested category path is invalid.');
        }

        
        $products = $subsubcategory->products;
        return view('categories.products', compact('subsubcategory', 'products'));
    }
} 