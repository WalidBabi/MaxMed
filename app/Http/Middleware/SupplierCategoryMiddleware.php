<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;

class SupplierCategoryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Allow admins to bypass this middleware
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Check if user is a supplier
        if (!$user || !$user->isSupplier()) {
            abort(403, 'Access denied. Only suppliers can access this resource.');
        }

        // Get the product from route parameters if it exists
        $product = $request->route('product');
        
        if ($product) {
            // If product is passed as ID, fetch the model
            if (is_numeric($product)) {
                $product = Product::findOrFail($product);
            }

            // Check if the product belongs to the supplier
            if ($product->supplier_id !== $user->id) {
                abort(403, 'Access denied. You can only manage your own products.');
            }

            // Check if supplier is assigned to this product's category
            if (!$user->isAssignedToCategory($product->category_id)) {
                abort(403, 'Access denied. You are not assigned to manage products in this category.');
            }
        }

        // Get category from route parameters if it exists
        $category = $request->route('category');
        if ($category) {
            // If category is passed as ID, fetch the model
            if (is_numeric($category)) {
                $category = Category::findOrFail($category);
            }

            // Check if supplier is assigned to this category
            if (!$user->isAssignedToCategory($category->id)) {
                abort(403, 'Access denied. You are not assigned to this category.');
            }
        }

        // For product creation, check if supplier can create products in the requested category
        if ($request->isMethod('post') && $request->route()->getName() === 'supplier.products.store') {
            $categoryId = $request->input('category_id');
            if ($categoryId && !$user->isAssignedToCategory($categoryId)) {
                return redirect()->back()
                    ->withErrors(['category_id' => 'You are not assigned to create products in this category.'])
                    ->withInput();
            }
        }

        return $next($request);
    }

    /**
     * Check if supplier has permission for specific category-based action
     */
    protected function hasPermissionForCategory($user, $action, $categoryId = null)
    {
        // Check if user has the specific supplier permission
        if (!$user->hasPermission("supplier.{$action}")) {
            return false;
        }

        // If category ID is provided, check category assignment
        if ($categoryId && !$user->isAssignedToCategory($categoryId)) {
            return false;
        }

        return true;
    }

    /**
     * Get supplier's allowed categories for filtering
     */
    protected function getAllowedCategories($user)
    {
        return $user->activeAssignedCategories->pluck('id')->toArray();
    }
} 