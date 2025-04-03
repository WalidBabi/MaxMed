<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('products.index', compact('categories'));
    }

    public function show(Category $category)
    {
        if ($category->subcategories->isNotEmpty()) {
            return view('categories.subcategories', compact('category'));
        }

        $products = $category->products;
        return view('categories.products', compact('category', 'products'));
    }

    public function showSubcategory(Category $category, Category $subcategory)
    {
        if ($subcategory->subcategories->isNotEmpty()) {
            return view('categories.subsubcategories', compact('category', 'subcategory'));
        }
        
        $products = $subcategory->products;
        return view('categories.products', compact('subcategory', 'products'));
    }

    public function showSubSubcategory(Category $category, Category $subcategory, Category $subsubcategory)
    {
        $products = $subsubcategory->products;
        return view('categories.products', compact('subsubcategory', 'products'));
    }
} 