@extends('admin.layouts.app')

@section('title', 'Edit Supplier Categories')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Supplier Categories</h1>
            <p class="text-gray-600 mt-2">Manage category assignments for {{ $supplier->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.supplier-categories.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Supplier Information -->
    <div class="lg:col-span-1">
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Supplier Information</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Company Details</h4>
                        <div class="mt-2 text-sm text-gray-600">
                            <p class="font-medium">{{ $supplier->name }}</p>
                            <p>{{ $supplier->email }}</p>
                        </div>
                    </div>

                    @if($supplier->supplierInformation)
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900">Business Information</h4>
                            <div class="mt-2 text-sm text-gray-600">
                                <p><span class="font-medium">Registration:</span> {{ $supplier->supplierInformation->business_registration_number }}</p>
                                <p><span class="font-medium">Years in Business:</span> {{ $supplier->supplierInformation->years_in_business }}</p>
                                <p><span class="font-medium">Location:</span> {{ $supplier->supplierInformation->city }}, {{ $supplier->supplierInformation->country }}</p>
                            </div>
                        </div>
                    @endif

                    @if($supplier->supplierInformation && $supplier->supplierInformation->specializations)
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900">Specializations</h4>
                            <div class="mt-2 space-y-2">
                                @foreach($supplier->supplierInformation->specializations as $specialization)
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $specialization }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($supplier->supplierInformation && $supplier->supplierInformation->suggested_categories)
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900">Suggested Categories</h4>
                            <div class="mt-2 space-y-2">
                                @foreach($supplier->supplierInformation->suggested_categories as $suggestedCategory)
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-yellow-500 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $suggestedCategory }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-gray-500">These categories have been suggested by the supplier and are pending review.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Category Assignment Form -->
    <div class="lg:col-span-2">
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Category Assignments</h3>
                <p class="text-sm text-gray-600 mt-1">Select the categories this supplier can provide products for</p>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.supplier-categories.update', $supplier) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('POST')
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center justify-end space-x-3 mb-4">
                        <button type="button" id="selectAllBtn" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16M4 12h16m-7 6h7" />
                            </svg>
                            Select All
                        </button>
                        <button type="button" id="clearAllBtn" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear All
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($categories as $category)
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="{{ $category->id }}"
                                           {{ $supplier->activeAssignedCategories->contains($category) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700">
                                        {{ $category->name }}
                                    </label>
                                    @if($category->description)
                                        <p class="text-xs text-gray-500">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Current Products by Category -->
@if($supplier->products()->count() > 0)
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Current Products by Category</h3>
            <p class="text-sm text-gray-600 mt-1">Overview of existing products organized by category</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($supplier->products->groupBy('category_id') as $categoryId => $products)
                    @php
                        $category = \App\Models\Category::find($categoryId);
                    @endphp
                    @if($category)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $category->name }}</h4>
                            <ul class="space-y-2">
                                @foreach($products as $product)
                                    <li class="text-sm text-gray-600">{{ $product->name }}</li>
                                @endforeach
                            </ul>
                            <p class="mt-3 text-xs text-gray-500">Total: {{ $products->count() }} products</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Pending Category Requests -->
@if($supplier->supplierCategories()->where('status', 'pending_approval')->exists())
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pending Category Requests</h3>
            <p class="text-sm text-gray-600 mt-1">Review and manage category assignment requests</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($supplier->supplierCategories()->where('status', 'pending_approval')->get() as $pendingCategory)
                    <div class="flex items-center justify-between bg-yellow-50 p-4 rounded-lg">
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $pendingCategory->category->name }}</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending Approval
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <form action="{{ route('admin.supplier-categories.approve', [$supplier->id, $pendingCategory->id]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.supplier-categories.reject', [$supplier->id, $pendingCategory->id]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

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
@endsection 