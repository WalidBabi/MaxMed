@extends('supplier.layouts.app')

@section('title', 'Create New Product')

@section('content')
<div class="p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Product</h1>
                <p class="text-gray-600 mt-2">Add a new product to your catalog</p>
            </div>
            <a href="{{ route('supplier.products.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to My Products
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            
            <!-- Basic Information Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Basic Information
                </h3>
                <p class="text-gray-600 mt-1">Essential product details and categorization</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                   placeholder="Enter product name" required>
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <select name="category_id" id="category_id" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Brand -->
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="brand" value="Yooning" readonly
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 sm:text-sm">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Brand is automatically set to Yooning for all supplier products</p>
                    </div>

                    <!-- SKU -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SKU (Auto-generated)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11a2 2 0 01-2 2H8a2 2 0 01-2-2V9a2 2 0 012-2h8a2 2 0 012 2v11zM8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h4"></path>
                                </svg>
                            </div>
                            <input type="text" readonly value="Will auto-generate with MM-#### format"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 sm:text-sm">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">SKU will be automatically assigned after product creation</p>
                    </div>
                </div>
            </div>

            <!-- Product Description Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    Product Description
                </h3>
                <p class="text-gray-600 mt-1">Detailed product information and specifications</p>
            </div>
            
            <div class="p-6">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-600">*</span>
                    </label>
                    <textarea name="description" id="description" rows="6" required
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                              placeholder="Add normal product description first, then add technical specifications using the 'PRODUCT PARAMETERS' format.">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Size Options Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Size Options
                </h3>
                <p class="text-gray-600 mt-1">Configure product size variations</p>
            </div>
            
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="has_size_options" name="has_size_options" value="1" 
                               {{ old('has_size_options') ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm font-medium text-gray-700">Enable size options</span>
                    </label>
                    @error('has_size_options')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="size-options-container" class="hidden">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-gray-700">Size Options</label>
                        <button type="button" id="add-size-option"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Size Option
                        </button>
                    </div>
                    <div id="size-options-list" class="space-y-3">
                        <!-- Size options will be added here -->
                    </div>
                </div>
            </div>

            <!-- Product Images Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Product Images
                </h3>
                <p class="text-gray-600 mt-1">Upload high-quality product images</p>
            </div>
            
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Primary Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            Primary Product Image <span class="text-red-600">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="image" name="image" type="file" class="sr-only" required accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, WEBP, AVIF up to 5MB</p>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Recommended size: 800x800px</p>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Images -->
                    <div>
                        <label for="additional_images" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Product Images
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="additional_images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Select multiple files</span>
                                        <input id="additional_images" name="additional_images[]" type="file" class="sr-only" multiple accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple images</p>
                            </div>
                        </div>
                        @error('additional_images')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Specification Image -->
                    <div>
                        <label for="specification_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Specification Image
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path d="M9 12h6m6 0h6m-6 6v6m6-6v6m6-6v6M9 12v6m6 0v6m-6-6h6m6 0h6M9 12h6m6 0h6m-6 6v6m6-6v6m6-6v6M9 12v6m6 0v6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="specification_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload specification image</span>
                                        <input id="specification_image" name="specification_image" type="file" class="sr-only" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">Technical specifications or chart</p>
                            </div>
                        </div>
                        @error('specification_image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Product Specifications Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Product Specifications
                </h3>
                <p class="text-gray-600 mt-1">Technical specifications based on product category</p>
                
                <!-- Feedback Message -->
                <div class="mt-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Need a specific specification field that's not listed here? Please submit a feedback through our feedback system. We continuously improve our product specifications based on supplier needs.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">Select a category above to see relevant specification fields for this product type.</p>
                    <div id="specifications-container" class="space-y-6">
                        <!-- Specifications will be loaded here dynamically -->
                    </div>
                </div>
            </div>

            <!-- Product Documentation Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Product Documentation
                </h3>
                <p class="text-gray-600 mt-1">Additional documentation and technical files</p>
            </div>
            
            <div class="p-6">
                <div>
                    <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">Product PDF</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                <path d="M12 4v44m16-32h8l-8-8v8zm0 0v8a2 2 0 002 2h6m-8-10H12a2 2 0 00-2 2v28a2 2 0 002 2h24a2 2 0 002-2V16m-8-10l8 8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="pdf_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload PDF file</span>
                                    <input id="pdf_file" name="pdf_file" type="file" class="sr-only" accept=".pdf">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PDF files up to 10MB</p>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Upload a PDF file containing product documentation or specifications</p>
                    @error('pdf_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Section -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('supplier.products.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category change handler for dynamic specifications
    const categorySelect = document.getElementById('category_id');
    const specificationsContainer = document.getElementById('specifications-container');
    
    if (categorySelect && specificationsContainer) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            if (categoryId) {
                loadSpecifications(categoryId);
            } else {
                specificationsContainer.innerHTML = '<p class="text-sm text-gray-500">Select a category to see specification fields.</p>';
            }
        });
    }

    // Function to load specifications based on category
    function loadSpecifications(categoryId) {
        fetch(`/supplier/api/category-specifications/${categoryId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.templates) {
                    renderSpecifications(data.templates);
                } else {
                    specificationsContainer.innerHTML = '<p class="text-sm text-gray-500">No specifications available for this category.</p>';
                }
            })
            .catch(error => {
                console.error('Error loading specifications:', error);
                specificationsContainer.innerHTML = '<p class="text-sm text-red-500">Error loading specifications. Please try again.</p>';
            });
    }

    // Function to render specification fields
    function renderSpecifications(templates) {
        let html = '';
        
        Object.entries(templates).forEach(([categoryName, specs]) => {
            html += `
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-list-ul mr-2 text-indigo-600"></i>
                        ${categoryName}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            `;
            
            specs.forEach(spec => {
                const fieldName = `specifications[${spec.key}]`;
                const fieldId = `spec_${spec.key}`;
                const required = spec.required ? 'required' : '';
                const requiredMark = spec.required ? '<span class="text-red-500">*</span>' : '';
                
                html += `
                    <div>
                        <label for="${fieldId}" class="block text-sm font-medium text-gray-700 mb-2">
                            ${spec.name} ${requiredMark}
                            ${spec.unit ? `<span class="text-gray-500 text-xs">(${spec.unit})</span>` : ''}
                        </label>
                `;
                
                if (spec.type === 'select') {
                    html += `
                        <select name="${fieldName}" id="${fieldId}" ${required}
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select ${spec.name}</option>
                    `;
                    spec.options.forEach(option => {
                        html += `<option value="${option}">${option}</option>`;
                    });
                    html += `</select>`;
                } else if (spec.type === 'textarea') {
                    html += `
                        <textarea name="${fieldName}" id="${fieldId}" ${required}
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  rows="3" placeholder="Enter ${spec.name.toLowerCase()}"></textarea>
                    `;
                } else if (spec.type === 'boolean') {
                    html += `
                        <div class="flex items-center">
                            <input type="checkbox" name="${fieldName}" id="${fieldId}" value="1" ${required}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="${fieldId}" class="ml-2 block text-sm text-gray-900">
                                Yes
                            </label>
                        </div>
                    `;
                } else {
                    const inputType = spec.type === 'decimal' ? 'number' : spec.type;
                    const step = spec.type === 'decimal' ? 'step="0.01"' : '';
                    html += `
                        <input type="${inputType}" name="${fieldName}" id="${fieldId}" ${required} ${step}
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Enter ${spec.name.toLowerCase()}">
                    `;
                }
                
                html += `</div>`;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        specificationsContainer.innerHTML = html;
    }

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Size options functionality
    const hasSizeOptions = document.getElementById('has_size_options');
    const sizeOptionsContainer = document.getElementById('size-options-container');
    const addSizeOptionBtn = document.getElementById('add-size-option');
    const sizeOptionsList = document.getElementById('size-options-list');

    if (hasSizeOptions && sizeOptionsContainer) {
        hasSizeOptions.addEventListener('change', function() {
            if (this.checked) {
                sizeOptionsContainer.classList.remove('hidden');
            } else {
                sizeOptionsContainer.classList.add('hidden');
            }
        });
    }

    if (addSizeOptionBtn && sizeOptionsList) {
        addSizeOptionBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center space-x-3 size-option-row';
            newRow.innerHTML = `
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="text" name="size_options[]" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                               placeholder="Size option (e.g., Small, Medium, Large)">
                    </div>
                </div>
                <button type="button" class="remove-size-option inline-flex items-center p-2 border border-transparent rounded-md text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            sizeOptionsList.appendChild(newRow);
            
            // Attach event listener to the new remove button
            const removeBtn = newRow.querySelector('.remove-size-option');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });
            }
        });
    }

    // Attach event listeners to existing remove buttons
    document.querySelectorAll('.remove-size-option').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.size-option-row').remove();
        });
    });

    // File input preview functionality
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const files = this.files;
            if (files.length > 0) {
                const fileNames = Array.from(files).map(file => file.name).join(', ');
                const label = this.closest('.border-dashed').querySelector('label span');
                if (label) {
                    label.textContent = fileNames.length > 50 ? fileNames.substring(0, 50) + '...' : fileNames;
                }
            }
        });
    });
});
</script>
@endsection 