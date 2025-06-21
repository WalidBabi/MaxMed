@extends('admin.layouts.app')

@section('title', 'Edit Supplier Categories')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Category Assignments</h1>
                <p class="text-gray-600 mt-2">Manage categories for {{ $supplier->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin.supplier-categories.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Supplier Info Card -->
        <div class="lg:col-span-1">
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Supplier Information</h3>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="mx-auto h-20 w-20 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl mb-4">
                            {{ strtoupper(substr($supplier->name, 0, 2)) }}
                        </div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $supplier->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $supplier->email }}</p>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-6">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="border-r border-gray-200">
                                <div class="text-2xl font-bold text-blue-600">{{ $supplier->products()->count() }}</div>
                                <div class="text-sm text-gray-500">Products</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-600">{{ $supplier->activeAssignedCategories->count() }}</div>
                                <div class="text-sm text-gray-500">Categories</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Assignment Form -->
        <div class="lg:col-span-2">
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Category Assignments</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $supplier->name }} will only be able to manage products in selected categories</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.supplier-categories.update', $supplier) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Select Categories
                            </label>
                            
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($categories as $category)
                                    <label class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" 
                                                   name="categories[]" 
                                                   value="{{ $category->id }}" 
                                                   id="category_{{ $category->id }}"
                                                   {{ in_array($category->id, $assignedCategoryIds) ? 'checked' : '' }}
                                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                            @if($category->products()->count() > 0)
                                                <div class="text-xs text-gray-500">{{ $category->products()->count() }} products available</div>
                                            @else
                                                <div class="text-xs text-gray-400">No products yet</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <div class="flex items-center space-x-3">
                                <button type="button" 
                                        id="selectAllBtn"
                                        class="inline-flex items-center rounded-md bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Select All
                                </button>
                                <button type="button" 
                                        id="clearAllBtn"
                                        class="inline-flex items-center rounded-md bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                                    </svg>
                                    Clear All
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75a.75.75 0 01.75-.75h7.5a.75.75 0 01.75.75z" />
                                    </svg>
                                    Update Assignments
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Products by Category -->
    @if($supplier->products()->count() > 0)
    <div class="mt-8">
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Current Products by Category</h3>
                <p class="text-sm text-gray-600 mt-1">Overview of existing products organized by category</p>
            </div>
            <div class="p-6">
                @php
                    $productsByCategory = $supplier->products()->with('category')->get()->groupBy('category.name');
                @endphp
                
                <div class="space-y-6">
                    @foreach($productsByCategory as $categoryName => $products)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-base font-medium text-gray-900">{{ $categoryName }}</h4>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $products->count() }} products
                                </span>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                                @foreach($products->take(6) as $product)
                                    <div class="rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:shadow-sm transition-all duration-200">
                                        <div class="text-xs font-medium text-blue-600 mb-1">
                                            {{ $product->sku ?? 'No SKU' }}
                                        </div>
                                        <div class="text-sm text-gray-900 font-medium">{{ Str::limit($product->name, 30) }}</div>
                                    </div>
                                @endforeach
                                @if($products->count() > 6)
                                    <div class="rounded-lg border border-dashed border-gray-300 p-3 flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-400">+{{ $products->count() - 6 }}</div>
                                            <div class="text-xs text-gray-500">more products</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="border-t border-gray-200"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAllBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const checkboxes = document.querySelectorAll('input[name="categories[]"]');

    // Select all categories
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    });

    // Clear all categories
    clearAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedCategories = document.querySelectorAll('input[name="categories[]"]:checked').length;
        
        if (checkedCategories === 0) {
            e.preventDefault();
            alert('Please select at least one category for this supplier.');
            return false;
        }

        // Confirm action
        if (!confirm(`Are you sure you want to update category assignments for {{ $supplier->name }}?`)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush 