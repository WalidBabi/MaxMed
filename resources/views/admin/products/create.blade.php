@extends('admin.layouts.app')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Create New Product</h1>
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Products
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    
                    <!-- Basic Information Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <h3 class="text-lg font-medium text-indigo-600 mb-6 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SKU (Auto-generated)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-barcode text-gray-400"></i>
                                    </div>
                                    <input type="text" id="sku_preview" readonly 
                                           value="Will auto-generate based on selected brand"
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 sm:text-sm">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    SKU Format: MT-#### (MaxTest), MW-#### (MaxWare), MM-#### (Others)
                                </p>
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tags text-gray-400"></i>
                                    </div>
                                    <select name="category_id" id="category_id" required
                                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Brand -->
                            <div>
                                <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-trademark text-gray-400"></i>
                                    </div>
                                    <select name="brand_id" id="brand_id"
                                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select a brand</option>
                                        @foreach(App\Models\Brand::orderBy('name')->get() as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('brand_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <h3 class="text-lg font-medium text-indigo-600 mb-6 flex items-center">
                            <i class="fas fa-dollar-sign mr-2"></i>Pricing Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Customer Price USD -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Customer Price (USD) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" required
                                           class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Customer Price AED -->
                            <div>
                                <label for="price_aed" class="block text-sm font-medium text-gray-700 mb-2">Customer Price (AED) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">AED</span>
                                    </div>
                                    <input type="number" step="0.01" name="price_aed" id="price_aed" value="{{ old('price_aed') }}" required
                                           class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                @error('price_aed')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Procurement Price AED -->
                            <div>
                                <label for="procurement_price_aed" class="block text-sm font-medium text-gray-700 mb-2">Procurement Price (AED)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">AED</span>
                                    </div>
                                    <input type="number" step="0.01" name="procurement_price_aed" id="procurement_price_aed" value="{{ old('procurement_price_aed') }}"
                                           class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                @error('procurement_price_aed')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Procurement Price USD -->
                            <div>
                                <label for="procurement_price_usd" class="block text-sm font-medium text-gray-700 mb-2">Procurement Price (USD)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" name="procurement_price_usd" id="procurement_price_usd" value="{{ old('procurement_price_usd') }}"
                                           class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                @error('procurement_price_usd')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Stock -->
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-boxes text-gray-400"></i>
                                    </div>
                                    <input type="number" name="stock" id="stock" value="{{ old('stock') }}" required
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                @error('stock')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Product Description Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-indigo-600 flex items-center">
                                <i class="fas fa-align-left mr-2"></i>Product Description
                            </h3>
                            <div class="flex space-x-2">
                                <button type="button" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50" data-modal-target="parametersHelpModal">
                                    <i class="fas fa-question-circle mr-1"></i> Format Help
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-1 border border-indigo-300 rounded-md text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100" id="insertParametersTemplate">
                                    <i class="fas fa-plus-circle mr-1"></i> Insert Template
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" rows="8" required
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Add normal product description first, then add technical specifications using the "PRODUCT PARAMETERS" format.
                            </p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Size Options Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <h3 class="text-lg font-medium text-indigo-600 mb-6 flex items-center">
                            <i class="fas fa-ruler mr-2"></i>Size Options
                        </h3>
                        
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input id="has_size_options" name="has_size_options" type="checkbox" value="1" {{ old('has_size_options') ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="has_size_options" class="ml-2 text-sm font-medium text-gray-700">Enable size options</label>
                            </div>
                            @error('has_size_options')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="size_options_container" class="space-y-4" style="{{ old('has_size_options') ? '' : 'display: none;' }}">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600 mb-4">Add the available size options for this product. You can enter detailed specifications with commas, such as "capacity: 500ml Ø (mm): 140 hint(mm): 75 htotal (mm): 100".</p>
                                <div id="size_options_list" class="space-y-3">
                                    @if(old('size_options'))
                                        @foreach(old('size_options') as $index => $option)
                                        <div class="flex items-center space-x-3 size-option-row">
                                            <div class="flex-1 relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-ruler-combined text-gray-400"></i>
                                                </div>
                                                <input type="text" name="size_options[]" value="{{ $option }}" placeholder="Size option (e.g., Small, Medium, Large, or detailed specs like 'capacity: 500ml Ø (mm): 140')"
                                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            </div>
                                            <button type="button" class="px-3 py-2 border border-red-300 rounded-md text-red-700 bg-red-50 hover:bg-red-100 remove-size-option">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="flex items-center space-x-3 size-option-row">
                                            <div class="flex-1 relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-ruler-combined text-gray-400"></i>
                                                </div>
                                                <input type="text" name="size_options[]" placeholder="Size option (e.g., Small, Medium, Large)"
                                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            </div>
                                            <button type="button" class="px-3 py-2 border border-red-300 rounded-md text-red-700 bg-red-50 hover:bg-red-100 remove-size-option">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100" id="add_size_option">
                                        <i class="fas fa-plus mr-2"></i> Add Another Size Option
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Images Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <h3 class="text-lg font-medium text-indigo-600 mb-6 flex items-center">
                            <i class="fas fa-images mr-2"></i>Product Images
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Primary Image -->
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Primary Product Image <span class="text-red-500">*</span></label>
                                <input type="file" name="image" id="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif" required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Recommended size: 800x800px, Max file size: 5MB</p>
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Additional Images -->
                            <div>
                                <label for="additional_images" class="block text-sm font-medium text-gray-700 mb-2">Additional Product Images</label>
                                <input type="file" name="additional_images[]" id="additional_images" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif" multiple
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                                <p class="mt-1 text-xs text-gray-500">You can select multiple images (hold Ctrl or Cmd while selecting)</p>
                                @error('additional_images')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('additional_images.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Specification Image -->
                            <div>
                                <label for="specification_image" class="block text-sm font-medium text-gray-700 mb-2">Product Specification Image</label>
                                <input type="file" name="specification_image" id="specification_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                                <p class="mt-1 text-xs text-gray-500">Upload an image showing product specifications or technical details</p>
                                @error('specification_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Product Documentation Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <h3 class="text-lg font-medium text-indigo-600 mb-6 flex items-center">
                            <i class="fas fa-file-pdf mr-2"></i>Product Documentation
                        </h3>
                        
                        <div>
                            <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">Product PDF</label>
                            <input type="file" name="pdf_file" id="pdf_file" accept=".pdf"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                            <p class="mt-1 text-xs text-gray-500">Upload a PDF file containing product documentation or specifications</p>
                            @error('pdf_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dynamic Specifications Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <h3 class="text-lg font-medium text-indigo-600 mb-6 flex items-center">
                            <i class="fas fa-cogs mr-2"></i>Product Specifications
                        </h3>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-4">Select a category above to see relevant specification fields for this product type.</p>
                            <div id="specifications-container" class="space-y-6">
                                <!-- Specifications will be loaded here dynamically -->
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-plus mr-2"></i> Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Parameters Help Modal -->
<div id="parametersHelpModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            Product Parameters Format Help
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">To add technical specifications to a product, include them in the following format after your regular product description:</p>
                            
                            <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto"><code>PRODUCT PARAMETERS
Parameter Name 1
Value 1
Parameter Name 2
Value 2
...</code></pre>

                            <p class="text-sm text-gray-700 mt-4 mb-2">Example:</p>
                            <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto"><code>PRODUCT PARAMETERS
Cat.No.
8032220100
Motion type
Orbital Shaker
Amplitude
10mm
Speed range
100-500rpm</code></pre>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Important guidelines:</p>
                                <ul class="text-sm text-gray-600 space-y-1 ml-4">
                                    <li>• Start with the header "PRODUCT PARAMETERS" on its own line</li>
                                    <li>• Each parameter name and value should be on separate lines</li>
                                    <li>• Keep parameter names consistent across similar products when possible</li>
                                    <li>• Parameters will be automatically formatted as a table on the product page</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                    Got it
                </button>
            </div>
        </div>
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
            fetch(`/admin/api/category-specifications/${categoryId}`)
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
                                      rows="3" placeholder="Enter ${spec.name.toLowerCase()}">${spec.unit ? '' : ''}</textarea>
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

        // Parameters template insertion
        const insertTemplateBtn = document.getElementById('insertParametersTemplate');
        const descriptionTextarea = document.getElementById('description');
        
        if (insertTemplateBtn && descriptionTextarea) {
            insertTemplateBtn.addEventListener('click', function() {
                const template = `

PRODUCT PARAMETERS
Cat.No.

Motion type

Amplitude

Speed range

Timer display

Time settings range

Max. load capacity

Motor type

Power

Voltage

Frequency

Dimensions

Weight

`;
                // Append the template to existing content or insert at cursor position
                const currentPosition = descriptionTextarea.selectionStart;
                const currentContent = descriptionTextarea.value;
                
                descriptionTextarea.value = 
                    currentContent.substring(0, currentPosition) + 
                    template + 
                    currentContent.substring(currentPosition);
                
                // Focus back on textarea
                descriptionTextarea.focus();
                descriptionTextarea.selectionStart = 
                    currentPosition + template.indexOf('Cat.No.') + 'Cat.No.'.length + 1;
                descriptionTextarea.selectionEnd = descriptionTextarea.selectionStart;
            });
        }
        
        // Size options handling
        const hasSizeOptions = document.getElementById('has_size_options');
        const sizeOptionsContainer = document.getElementById('size_options_container');
        const addSizeOptionBtn = document.getElementById('add_size_option');
        const sizeOptionsList = document.getElementById('size_options_list');

        if (hasSizeOptions && sizeOptionsContainer) {
            hasSizeOptions.addEventListener('change', function() {
                if (this.checked) {
                    sizeOptionsContainer.style.display = 'block';
                } else {
                    sizeOptionsContainer.style.display = 'none';
                }
            });
        }

        if (addSizeOptionBtn && sizeOptionsList) {
            addSizeOptionBtn.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'flex items-center space-x-3 size-option-row';
                newRow.innerHTML = `
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-ruler-combined text-gray-400"></i>
                        </div>
                        <input type="text" name="size_options[]" placeholder="Size option (e.g., Small, Medium, Large, or detailed specs like 'capacity: 500ml Ø (mm): 140')"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <button type="button" class="px-3 py-2 border border-red-300 rounded-md text-red-700 bg-red-50 hover:bg-red-100 remove-size-option">
                        <i class="fas fa-trash"></i>
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

        // Modal functions
        const helpButtons = document.querySelectorAll('[data-modal-target="parametersHelpModal"]');
        const modal = document.getElementById('parametersHelpModal');
        
        helpButtons.forEach(button => {
            button.addEventListener('click', function() {
                modal.classList.remove('hidden');
            });
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    });

    function closeModal() {
        document.getElementById('parametersHelpModal').classList.add('hidden');
    }
</script>

<!-- Include price calculator script -->
<script src="{{ asset('js/product-price-calculator.js') }}"></script>
@endsection