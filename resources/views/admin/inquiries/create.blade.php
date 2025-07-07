@extends('admin.layouts.app')

@section('title', 'Create Inquiry')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Inquiry</h1>
                    <p class="text-gray-600 mt-2">Create a new product inquiry for suppliers</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.inquiries.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 4.158a.75.75 0 11-1.04 1.04l-5.5-5.5a.75.75 0 010-1.08l5.5-5.5a.75.75 0 111.04 1.04L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            There were {{ count($errors) }} errors with your submission
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div id="success-notification" class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button type="button" class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50" onclick="this.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div id="error-notification" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button type="button" class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50" onclick="this.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('admin.inquiries.store') }}" method="POST" enctype="multipart/form-data" id="inquiryForm">
                @csrf
            
            <!-- Basic Information -->
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer Reference (Optional)</label>
                            <input type="text" name="customer_reference" value="{{ old('customer_reference') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('customer_reference')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Internal Notes (Optional)</label>
                            <input type="text" name="internal_notes" value="{{ old('internal_notes') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('internal_notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        </div>

                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">General Requirements (Optional)</label>
                        <textarea name="requirements" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="Enter general requirements that apply to all products...">{{ old('requirements') }}</textarea>
                        @error('requirements')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                    </div>
                </div>
                        </div>

            <!-- Products Section -->
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <div>
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Products <span class="text-sm text-gray-500 font-normal">(Optional)</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Add products to this inquiry or leave empty if only attachments are needed.</p>
                    </div>
                    <button type="button" id="addProduct" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Add Product
                    </button>
                </div>
                <div class="p-6">
                    <div class="table-container" style="overflow: visible !important;">
                        <table class="w-full divide-y divide-gray-200 products-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 60px;">Order</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 300px;">Product Details</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">Specifications</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Size</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Quantity</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">Requirements</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Notes</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="productsTable" class="bg-white divide-y divide-gray-200">
                                <!-- Products will be added dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- File Upload -->
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Attachments</h3>
                </div>
                <div class="p-6">
                                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md" id="file-drop-zone">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload PDF files</span>
                                    <input id="file-upload" name="attachments[]" type="file" multiple accept=".pdf" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF files only, max 10MB each</p>
                                </div>
                            </div>
                    
                    <!-- File Preview Area -->
                    <div id="file-preview" class="mt-4 hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Files:</h4>
                        <div id="file-list" class="space-y-2"></div>
                    </div>
                    
                            @error('attachments')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('attachments.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Supplier Targeting -->
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Supplier Targeting</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Targeting Method</label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="radio" id="target_all" name="targeting_method" value="all" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                                <label for="target_all" class="ml-3 block text-sm font-medium text-gray-700">
                                    Send to all suppliers
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="target_categories" name="targeting_method" value="categories" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <label for="target_categories" class="ml-3 block text-sm font-medium text-gray-700">
                                    Send to specific categories only
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="category-selection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Product Categories</label>
                        <p class="text-sm text-gray-600 mb-4">Choose the categories that best match your inquiry requirements. Suppliers assigned to these categories will receive this inquiry.</p>
                        <div id="pdf-only-notice" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
                            <div class="flex">
                                <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">PDF-Only Inquiry</h4>
                                    <p class="mt-1 text-sm text-blue-700">
                                        Since you're uploading PDFs without specific products, please select the relevant categories to ensure the right suppliers receive this inquiry.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($categories as $category)
                                <div class="flex items-center">
                                    <input type="checkbox" id="category_{{ $category->id }}" name="target_categories[]" value="{{ $category->id }}" 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="category_{{ $category->id }}" class="ml-3 block text-sm font-medium text-gray-700">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        @error('target_categories')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="bg-white shadow-md rounded-lg">
                <div class="px-6 py-3 bg-gray-50 text-right">
                    <a href="{{ route('admin.inquiries.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Inquiry
                    </button>
                </div>
                </div>
            </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Products Table Layout */
    .products-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .products-table th,
    .products-table td {
        padding: 12px 8px;
        vertical-align: top;
        border-bottom: 1px solid #e5e7eb;
        word-wrap: break-word;
        overflow-wrap: break-word;
        position: relative;
    }
    
    .products-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #6b7280;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .products-table td {
        background-color: #ffffff;
        overflow: visible;
    }
    
    .products-table tr:hover td {
        background-color: #f9fafb;
    }
    
    /* Table Container - Ensure overflow is visible for dropdowns */
    .overflow-x-auto {
        overflow-x: auto;
        overflow-y: visible !important;
    }

    /* Product Dropdown Styles */
    .product-dropdown-container {
        position: relative;
        width: 100%;
        z-index: 1000;
    }
    
    .product-dropdown-container {
        position: relative;
    }
    
    .product-dropdown-list {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        right: 0 !important;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        max-height: 300px;
        overflow-y: auto;
        background: white;
        z-index: 99999 !important;
        margin-top: 4px;
        min-width: 280px;
        max-width: 400px;
    }
    
    /* Specifications Dropdown Styles */
    .specifications-dropdown-list {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        right: 0 !important;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        max-height: 300px;
        overflow-y: auto;
        background: white;
        z-index: 99999 !important;
        margin-top: 4px;
        min-width: 250px;
    }
    
    .specifications-dropdown-list .specifications-content {
        max-height: 250px;
        overflow-y: auto;
    }
    
    .specifications-dropdown-list .spec-checkbox {
        margin-right: 8px;
    }
    
    .specifications-dropdown-list .select-all-checkbox {
        margin-right: 8px;
    }
    
    .products-table {
        overflow: visible !important;
    }
    
    .table-container {
        overflow: visible !important;
    }
    
    .product-dropdown-list .dropdown-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .product-dropdown-list .dropdown-item:hover,
    .product-dropdown-list .dropdown-item.bg-indigo-50 {
        background-color: #eef2ff;
    }
    
    .product-dropdown-list .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .product-dropdown-list .dropdown-no-results {
        padding: 12px 16px;
        text-align: center;
        color: #6b7280;
        font-style: italic;
    }
    
    .product-search-input {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
    
    .hidden {
        display: none;
    }
    
    /* Ensure table rows don't clip dropdowns */
    .product-row {
        position: relative;
    }
    
    /* Fix scrollbars */
    .product-dropdown-list::-webkit-scrollbar,
    .specifications-dropdown-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .product-dropdown-list::-webkit-scrollbar-track,
    .specifications-dropdown-list::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    .product-dropdown-list::-webkit-scrollbar-thumb,
    .specifications-dropdown-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .product-dropdown-list::-webkit-scrollbar-thumb:hover,
    .specifications-dropdown-list::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@push('scripts')
<script>
let productCounter = 0;
let products = @json($products);
console.log('Products loaded:', products.length);
console.log('Sample product:', products[0]);
if (products.length > 0) {
    console.log('Sample product specifications:', products[0].specifications);
    console.log('Sample product size options:', products[0].size_options);
    console.log('Sample product has_size_options:', products[0].has_size_options);
    console.log('Sample product specifications type:', typeof products[0].specifications);
    console.log('Sample product specifications JSON:', JSON.stringify(products[0].specifications));
}

// Add initial product row
document.addEventListener('DOMContentLoaded', function() {
    addProduct();
    
    // Add event listeners for size changes on existing items
    document.querySelectorAll('.size-options-select').forEach(sizeSelect => {
        sizeSelect.addEventListener('change', function() {
            const row = this.closest('tr');
            updateSelectedSpecificationsForRow(row);
        });
    });
});

function addProduct() {
    const tbody = document.getElementById('productsTable');
    const row = document.createElement('tr');
    row.className = 'product-row bg-white hover:bg-gray-50';
    row.innerHTML = `
        <td class="px-3 py-4 text-center">
            <span class="text-sm text-gray-500">${productCounter + 1}</span>
        </td>
        <td class="px-3 py-4">
            <div class="space-y-2">
                <!-- Product Type Selection -->
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="items[${productCounter}][product_type]" value="listed" class="form-radio product-type-radio" checked>
                        <span class="ml-2 text-sm">Listed Product</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="items[${productCounter}][product_type]" value="unlisted" class="form-radio product-type-radio">
                        <span class="ml-2 text-sm">Unlisted Product</span>
                    </label>
                </div>
                
                <!-- Listed Product Selection -->
                <div class="listed-product-section">
                    <div class="relative product-dropdown-container">
                        <input type="text" 
                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 product-search-input" 
                               placeholder="Search products..." 
                               autocomplete="off">
                        <input type="hidden" name="items[${productCounter}][product_id]" class="product-id-input">
                        <input type="hidden" name="items[${productCounter}][product_name]" class="product-name-hidden">
                        
                        <!-- Dropdown List -->
                        <div class="product-dropdown-list hidden">
                            <div class="dropdown-items">
                                @foreach($products as $product)
                                    <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" 
                                         data-id="{{ $product->id }}"
                                         data-name="{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}"
                                         data-description="{{ $product->description }}"
                                         data-price-aed="{{ $product->price_aed ?? $product->price }}"
                                         data-price-usd="{{ $product->price }}"
                                         data-specifications="{{ $product->specifications ? json_encode($product->specifications->map(function($spec) { return $spec->display_name . ': ' . $spec->formatted_value; })->toArray()) : '[]' }}"
                                         data-has-size-options="{{ $product->has_size_options ? 'true' : 'false' }}"
                                         data-size-options="{{ is_array($product->size_options) ? json_encode($product->size_options) : ($product->size_options ?: '[]') }}"
                                         data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                        <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                        @if($product->description)
                                            <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                        @endif
                                        @if($product->price_aed ?? $product->price)
                                            <div class="price-display-aed text-indigo-600 text-sm font-medium mt-1">AED {{ number_format($product->price_aed ?? $product->price, 2) }}</div>
                                            <div class="price-display-usd text-indigo-600 text-sm font-medium mt-1 hidden">USD {{ number_format($product->price, 2) }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="dropdown-no-results hidden">
                                <div class="text-center py-4 text-gray-500">No products found</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Unlisted Product Fields -->
                <div class="unlisted-product-section hidden">
                    <div class="space-y-2">
                        <input type="text" 
                               name="items[${productCounter}][product_name]" 
                               placeholder="Product Name"
                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <textarea name="items[${productCounter}][product_description]" 
                                  placeholder="Product Description"
                                  rows="2"
                                  class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        <input type="text" 
                               name="items[${productCounter}][product_brand]" 
                               placeholder="Brand"
                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <input type="text" 
                               name="items[${productCounter}][product_category]" 
                               placeholder="Category"
                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <textarea name="items[${productCounter}][product_specifications]" 
                                  placeholder="Specifications"
                                  rows="2"
                                  class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                </div>
            </div>
        </td>
        <td class="px-3 py-4">
            <!-- Specifications Dropdown -->
            <div class="relative">
                <input type="text" 
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 specifications-input" 
                       placeholder="Click to select specifications..." 
                       readonly>
                <input type="hidden" name="items[${productCounter}][specifications]" class="specifications-hidden">
                
                <!-- Specifications Dropdown List -->
                <div class="specifications-dropdown-list hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto" style="position: absolute !important; z-index: 9999 !important; min-width: 250px;">
                    <div class="specifications-content">
                        <div class="p-2 text-sm text-gray-500">No specifications available</div>
                    </div>
                </div>
            </div>
        </td>
        <td class="px-3 py-4">
            <!-- Size Dropdown -->
            <select name="items[${productCounter}][size]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 size-options-select">
                <option value="">Select Size (if applicable)</option>
            </select>
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${productCounter}][quantity]" value="1.00" min="0"
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </td>
        <td class="px-3 py-4">
            <textarea name="items[${productCounter}][requirements]" rows="3" placeholder="Specific requirements for this product..."
                      class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </td>
        <td class="px-3 py-4">
            <textarea name="items[${productCounter}][notes]" rows="3" placeholder="Additional notes..."
                      class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </td>
        <td class="px-3 py-4 text-center">
            <button type="button" onclick="removeProduct(this)" class="inline-flex items-center p-1 border border-transparent rounded-full text-red-600 hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Initialize event listeners for this row
    initializeRowEventListeners(row);
    
    productCounter++;
}

function initializeRowEventListeners(row) {
    // Product type radio buttons
    const productTypeRadios = row.querySelectorAll('.product-type-radio');
    const listedSection = row.querySelector('.listed-product-section');
    const unlistedSection = row.querySelector('.unlisted-product-section');
    
    productTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'listed') {
                listedSection.classList.remove('hidden');
                unlistedSection.classList.add('hidden');
            } else {
                listedSection.classList.add('hidden');
                unlistedSection.classList.remove('hidden');
            }
        });
    });
    
    // Product search functionality
    const searchInput = row.querySelector('.product-search-input');
    const dropdownList = row.querySelector('.product-dropdown-list');
    const dropdownItems = row.querySelector('.dropdown-items');
    const dropdownNoResults = row.querySelector('.dropdown-no-results');
    const productIdInput = row.querySelector('.product-id-input');
    
    initializeProductSearch(searchInput, dropdownList, dropdownItems, dropdownNoResults, productIdInput);
    
    // Specifications dropdown functionality
    const specificationsInput = row.querySelector('.specifications-input');
    const specificationsDropdown = row.querySelector('.specifications-dropdown-list');
    
    if (specificationsInput && specificationsDropdown) {
        specificationsInput.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Specifications input clicked');
            console.log('Current dropdown state:', specificationsDropdown.classList.contains('hidden'));
            specificationsDropdown.classList.toggle('hidden');
            console.log('New dropdown state:', specificationsDropdown.classList.contains('hidden'));
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!specificationsInput.contains(e.target) && !specificationsDropdown.contains(e.target)) {
                specificationsDropdown.classList.add('hidden');
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        specificationsDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

function initializeProductSearch(searchInput, dropdownList, dropdownItems, dropdownNoResults, productIdInput) {
    const allDropdownItems = dropdownItems.querySelectorAll('.dropdown-item');
    let selectedIndex = -1;
    
    console.log('Initializing product search for:', searchInput);
    console.log('Dropdown list:', dropdownList);
    console.log('Dropdown items count:', allDropdownItems.length);
    
    // Show dropdown when input is focused
    searchInput.addEventListener('focus', function() {
        console.log('Input focused, showing dropdown');
        dropdownList.classList.remove('hidden');
        filterDropdownItems('');
    });
    
    // Filter items as user types
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        console.log('Input changed, filtering:', searchTerm);
        filterDropdownItems(searchTerm);
        selectedIndex = -1;
        dropdownList.classList.remove('hidden');
    });
    
    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const visibleItems = dropdownItems.querySelectorAll('.dropdown-item:not(.hidden)');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, visibleItems.length - 1);
            updateSelection(visibleItems);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(visibleItems);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
                selectProduct(visibleItems[selectedIndex]);
            }
        } else if (e.key === 'Escape') {
            dropdownList.classList.add('hidden');
            selectedIndex = -1;
        }
    });
    
    // Handle item clicks
    allDropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            selectProduct(this);
        });
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
            dropdownList.classList.add('hidden');
            selectedIndex = -1;
        }
    });
    
    function filterDropdownItems(searchTerm) {
        let visibleCount = 0;
        
        allDropdownItems.forEach(item => {
            const searchText = item.dataset.searchText || '';
            
            if (searchTerm === '' || searchText.includes(searchTerm)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            dropdownNoResults.classList.remove('hidden');
    } else {
            dropdownNoResults.classList.add('hidden');
        }
    }
    
    function updateSelection(visibleItems) {
        // Remove previous selection
        visibleItems.forEach(item => item.classList.remove('bg-indigo-50'));
        
        // Add selection to current item
        if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
            visibleItems[selectedIndex].classList.add('bg-indigo-50');
        }
    }
    
    function selectProduct(item) {
        const productId = item.dataset.id;
        const productName = item.dataset.name;
        const specifications = item.dataset.specifications;
        
        console.log('Product selected:', { productId, productName, specifications });
        
        // Get the row that contains this product selection
        const row = searchInput.closest('tr');
        
        // Get all the necessary elements from the row
        const productIdInput = row.querySelector('.product-id-input');
        const productNameHidden = row.querySelector('.product-name-hidden');
        
        // Set basic product info
        searchInput.value = productName;
        productIdInput.value = productId;
        productNameHidden.value = productName;
        
        // Handle specifications
        const specificationsInput = row.querySelector('.specifications-input');
        const specificationsHidden = row.querySelector('.specifications-hidden');
        const specificationsContent = row.querySelector('.specifications-content');
        
        if (specifications && specifications !== '[]') {
            try {
                const specsArray = JSON.parse(specifications);
                if (specsArray.length > 0) {
                    specificationsInput.value = 'Click to select specifications...';
                    specificationsHidden.value = JSON.stringify(specsArray);
                    
                    // Create checkboxes for specifications
                    let checkboxesHtml = '';
                    specsArray.forEach(spec => {
                        checkboxesHtml += `
                            <label class="flex items-center p-2 hover:bg-gray-50">
                                <input type="checkbox" class="spec-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" data-spec="${spec}">
                                <span class="ml-2 text-sm text-gray-700">${spec}</span>
                            </label>
                        `;
                    });
                    specificationsContent.innerHTML = checkboxesHtml;
                    
                    // Add event listeners to checkboxes
                    const checkboxes = specificationsContent.querySelectorAll('.spec-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', updateSelectedSpecifications);
                    });
                }
            } catch (e) {
                console.error('Error parsing specifications:', e);
                specificationsInput.value = '';
                specificationsHidden.value = '';
                specificationsContent.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
            }
        } else {
            specificationsInput.value = '';
            specificationsHidden.value = '';
            specificationsContent.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
        }
        
        // Handle size options
        const sizeSelect = row.querySelector('.size-options-select');
        if (sizeSelect) {
            const hasSizeOptions = item.dataset.hasSizeOptions === 'true';
            const sizeOptions = item.dataset.sizeOptions ? JSON.parse(item.dataset.sizeOptions) : [];
            
            if (hasSizeOptions && sizeOptions && sizeOptions.length > 0) {
                let options = '<option value="">Select Size (if applicable)</option>';
                sizeOptions.forEach(size => {
                    options += `<option value="${size}">${size}</option>`;
                });
                sizeSelect.innerHTML = options;
                
                // Add event listener for size changes
                sizeSelect.addEventListener('change', updateSelectedSpecifications);
            } else {
                sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
            }
        }
        
        // Hide dropdown
        dropdownList.classList.add('hidden');
    }
}

// Global function to update selected specifications for a specific row
function updateSelectedSpecificationsForRow(row) {
    const specificationsInput = row.querySelector('.specifications-input');
    const specificationsHidden = row.querySelector('.specifications-hidden');
    const checkboxes = row.querySelectorAll('.spec-checkbox:checked');
    const selectedSpecs = Array.from(checkboxes).map(cb => cb.dataset.spec);
    
    if (selectedSpecs.length > 0) {
        specificationsInput.value = selectedSpecs.join(', ');
        specificationsHidden.value = JSON.stringify(selectedSpecs);
    } else {
        specificationsInput.value = 'Click to select specifications...';
        specificationsHidden.value = '';
    }
}

function removeProduct(button) {
    const row = button.closest('.product-row');
    const tbody = document.getElementById('productsTable');
    
    // Don't remove if it's the only row
    if (tbody.children.length <= 1) {
        alert('At least one product is required.');
        return;
    }
    
    row.remove();
    
    // Update row numbers
    const rows = tbody.querySelectorAll('.product-row');
    rows.forEach((row, index) => {
        const numberCell = row.querySelector('td:first-child span');
        numberCell.textContent = index + 1;
    });
}

// Add Product button event listener
document.getElementById('addProduct').addEventListener('click', function() {
    console.log('Add product button clicked');
    addProduct();
});

// Debug: Check if elements exist
console.log('Form elements check:');
console.log('Form:', document.getElementById('inquiryForm'));
console.log('Success notification:', document.getElementById('success-notification'));
console.log('Error notification:', document.getElementById('error-notification'));
console.log('Add product button:', document.getElementById('addProduct'));

// Auto-dismiss messages after 5 seconds
setTimeout(function() {
    const successNotification = document.getElementById('success-notification');
    const errorNotification = document.getElementById('error-notification');
    
    if (successNotification) {
        successNotification.style.display = 'none';
    }
    if (errorNotification) {
        errorNotification.style.display = 'none';
    }
}, 5000);

// File upload functionality
document.getElementById('file-upload').addEventListener('change', function(e) {
    handleFileSelection(e.target.files);
});

// Drag and drop functionality
const dropZone = document.getElementById('file-drop-zone');

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    dropZone.classList.add('border-indigo-400', 'bg-indigo-50');
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');
    handleFileSelection(e.dataTransfer.files);
});

function handleFileSelection(files) {
    const fileList = document.getElementById('file-list');
    const filePreview = document.getElementById('file-preview');
    
    // Clear existing files
    fileList.innerHTML = '';
    
    if (files.length > 0) {
        filePreview.classList.remove('hidden');
        
        Array.from(files).forEach((file, index) => {
            if (file.type === 'application/pdf' && file.size <= 10 * 1024 * 1024) {
            const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                fileItem.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900">${file.name}</p>
                            <p class="text-sm text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
            fileList.appendChild(fileItem);
            } else {
                alert(`File "${file.name}" is not a valid PDF or exceeds 10MB limit.`);
            }
        });
        
        // Trigger category selection update if targeting categories is selected
        const targetCategoriesRadio = document.getElementById('target_categories');
        if (targetCategoriesRadio && targetCategoriesRadio.checked) {
            updateCategorySelection();
        }
    } else {
        filePreview.classList.add('hidden');
        
        // Trigger category selection update if targeting categories is selected
        const targetCategoriesRadio = document.getElementById('target_categories');
        if (targetCategoriesRadio && targetCategoriesRadio.checked) {
            updateCategorySelection();
        }
    }
}

function removeFile(index) {
    const fileInput = document.getElementById('file-upload');
    const dt = new DataTransfer();
    const files = Array.from(fileInput.files);
    
    files.splice(index, 1);
    
    files.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
    
    handleFileSelection(fileInput.files);
}

// Targeting method radio button functionality
document.addEventListener('DOMContentLoaded', function() {
    const targetAllRadio = document.getElementById('target_all');
    const targetCategoriesRadio = document.getElementById('target_categories');
    const categorySelection = document.getElementById('category-selection');
    const pdfOnlyNotice = document.getElementById('pdf-only-notice');
    const productsTable = document.getElementById('productsTable');
    
    function updateCategorySelection() {
        if (targetCategoriesRadio.checked) {
            categorySelection.classList.remove('hidden');
            
            // Show PDF-only notice if there are no products but there are files
            const hasProducts = productsTable && productsTable.children.length > 0;
            const hasFiles = document.getElementById('file-preview') && !document.getElementById('file-preview').classList.contains('hidden');
            
            if (!hasProducts && hasFiles) {
                pdfOnlyNotice.classList.remove('hidden');
            } else {
                pdfOnlyNotice.classList.add('hidden');
            }
        } else {
            categorySelection.classList.add('hidden');
            pdfOnlyNotice.classList.add('hidden');
        }
    }
    
    targetAllRadio.addEventListener('change', updateCategorySelection);
    targetCategoriesRadio.addEventListener('change', updateCategorySelection);
    
    // Initialize on page load
    updateCategorySelection();
});

// Update selected specifications display
function updateSelectedSpecifications() {
    const row = this.closest('tr');
    const specificationsInput = row.querySelector('.specifications-input');
    const specificationsHidden = row.querySelector('.specifications-hidden');
    const checkboxes = row.querySelectorAll('.spec-checkbox:checked');
    
    if (checkboxes.length > 0) {
        const selectedSpecs = Array.from(checkboxes).map(cb => cb.dataset.spec);
        specificationsInput.value = selectedSpecs.join(', ');
        specificationsHidden.value = JSON.stringify(selectedSpecs);
    } else {
        specificationsInput.value = 'Click to select specifications...';
        specificationsHidden.value = '';
    }
}
</script>
@endpush 