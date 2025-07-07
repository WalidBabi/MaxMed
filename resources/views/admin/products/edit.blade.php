@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Product</h1>
                <p class="text-gray-600 mt-2">Update product information and settings</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Products
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    Basic Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- SKU Display (Read-only) -->
                    <div>
                        <label for="sku_display" class="block text-sm font-medium leading-6 text-gray-900">SKU (Auto-generated)</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 bg-gray-50">
                                <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75H16.5v-.75zM13.5 13.5h.75v.75H13.5v-.75zM13.5 16.5h.75v.75H13.5v-.75zM16.5 13.5h.75v.75H16.5v-.75zM19.5 13.5h.75v.75H19.5v-.75zM19.5 16.5h.75v.75H19.5v-.75zM16.5 16.5h.75v.75H16.5v-.75z" />
                                    </svg>
                                </span>
                                <input type="text" id="sku_display" readonly value="{{ $product->sku }}"
                                       class="block flex-1 border-0 bg-gray-50 py-1.5 pl-3 text-gray-900 sm:text-sm sm:leading-6">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">SKU will regenerate if brand is changed</p>
                        </div>
                    </div>

                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Product Name <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                                <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                    </svg>
                                </span>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                                       class="block flex-1 border-0 bg-transparent py-1.5 pl-3 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" 
                                       placeholder="Enter product name" required>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium leading-6 text-gray-900">Category <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <select name="category_id" id="category_id" required
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Brand -->
                    <div>
                        <label for="brand_id" class="block text-sm font-medium leading-6 text-gray-900">Brand</label>
                        <div class="mt-2">
                            <select name="brand_id" id="brand_id"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="">Select a brand</option>
                                @foreach(App\Models\Brand::orderBy('name')->get() as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Stock Quantity -->
                    <div>
                        <label for="inventory_quantity" class="block text-sm font-medium leading-6 text-gray-900">Stock Quantity <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                                <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </span>
                                <input type="number" name="inventory_quantity" id="inventory_quantity" 
                                       value="{{ old('inventory_quantity', $product->inventory->quantity ?? 0) }}" min="0"
                                       class="block flex-1 border-0 bg-transparent py-1.5 pl-3 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" 
                                       placeholder="0" required>
                            </div>
                            @error('inventory_quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pricing Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Price USD -->
                    <div>
                        <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Price (USD) <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                                <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">$</span>
                                <input type="number" step="0.01" name="price" id="price" 
                                       value="{{ old('price', $product->price) }}"
                                       class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" 
                                       placeholder="0.00" required>
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Price AED -->
                    <div>
                        <label for="price_aed" class="block text-sm font-medium leading-6 text-gray-900">Price (AED) <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                                <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">AED</span>
                                <input type="number" step="0.01" name="price_aed" id="price_aed" 
                                       value="{{ old('price_aed', $product->price_aed) }}"
                                       class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" 
                                       placeholder="0.00" required>
                            </div>
                            @error('price_aed')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Procurement Price AED -->
                    <div>
                        <label for="procurement_price_aed" class="block text-sm font-medium leading-6 text-gray-900">Procurement Price (AED)</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                                <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">AED</span>
                                <input type="number" step="0.01" name="procurement_price_aed" id="procurement_price_aed" 
                                       value="{{ old('procurement_price_aed', $product->procurement_price_aed) }}"
                                       class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" 
                                       placeholder="0.00">
                            </div>
                            @error('procurement_price_aed')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Procurement Price USD -->
                    <div>
                        <label for="procurement_price_usd" class="block text-sm font-medium leading-6 text-gray-900">Procurement Price (USD)</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                                <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">$</span>
                                <input type="number" step="0.01" name="procurement_price_usd" id="procurement_price_usd" 
                                       value="{{ old('procurement_price_usd', $product->procurement_price_usd) }}"
                                       class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" 
                                       placeholder="0.00">
                            </div>
                            @error('procurement_price_usd')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                    </svg>
                    Product Description
                </h3>
            </div>
            <div class="p-6">
                <div>
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description <span class="text-red-500">*</span></label>
                    <div class="mt-2">
                        <textarea name="description" id="description" rows="4"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  placeholder="Enter product description...">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Size Options -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125m0-15.75c0-1.036.84-1.875 1.875-1.875h5.25c1.035 0 1.875.84 1.875 1.875v15.75c0 .621-.504 1.125-1.125 1.125H9.75M8.25 9.75h4.5v2.25H8.25V9.75z" />
                    </svg>
                    Size Options
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-center">
                    <input id="has_size_options" name="has_size_options" type="checkbox" value="1" 
                           {{ old('has_size_options', $product->has_size_options) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <label for="has_size_options" class="ml-3 text-sm font-medium leading-6 text-gray-900">Enable size options for this product</label>
                </div>
                @error('has_size_options')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div id="size_options_container" class="mt-6" style="{{ old('has_size_options', $product->has_size_options) ? '' : 'display: none;' }}">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm text-gray-600 mb-4">Add the available size options for this product.</p>
                        <div id="size_options_list">
                            @php
                                $sizeOptions = old('size_options', $product->size_options ?? []);
                                if (!is_array($sizeOptions) && !empty($sizeOptions)) {
                                    $sizeOptions = json_decode($sizeOptions, true) ?? [];
                                }
                                // Handle simple string array format (which is what we actually use)
                                if (!empty($sizeOptions) && !is_array(reset($sizeOptions))) {
                                    // Already a simple array of strings, keep as is
                                    $sizeOptions = array_values($sizeOptions);
                                }
                            @endphp
                            
                            @if(count($sizeOptions) > 0)
                                @foreach($sizeOptions as $index => $option)
                                    <div class="size-option-item flex items-center space-x-3 mb-3">
                                        <input type="text" name="size_options[]" 
                                               value="{{ $option }}" 
                                               placeholder="Size (e.g., S, M, L, XL)"
                                               class="block flex-1 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <button type="button" class="remove-size-option inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" id="add_size_option" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Add Size Option
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Images Section -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    Product Images
                </h3>
            </div>
            <div class="p-6">
                <!-- Current Images -->
                @if($product->images->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Current Images</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="current-images">
                            @foreach($product->images as $image)
                                <div class="image-item relative group" data-image-id="{{ $image->id }}">
                                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200">
                                        <img src="{{ $image->image_url }}" alt="Product Image" class="object-cover">
                                        @if($image->is_primary)
                                            <div class="absolute top-2 left-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Primary
                                                </span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity duration-200">
                                            <div class="absolute top-2 right-2 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                @if(!$image->is_primary)
                                                    <button type="button" class="set-primary inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" data-image-id="{{ $image->id }}">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                        </svg>
                                                        Set Primary
                                                    </button>
                                                @endif
                                                <button type="button" class="delete-image inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" data-image-id="{{ $image->id }}">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="delete_images" id="delete_images" value="">
                        <input type="hidden" name="primary_image_id" id="primary_image_id" value="">
                    </div>
                @endif

                <!-- New Images Upload -->
                <div class="space-y-6">
                    <!-- Primary Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">New Primary Product Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP, AVIF up to 5MB</p>
                            </div>
                        </div>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Images -->
                    <div>
                        <label for="additional_images" class="block text-sm font-medium text-gray-700 mb-2">Additional Images</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="additional_images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload files</span>
                                        <input id="additional_images" name="additional_images[]" type="file" multiple class="sr-only" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP, AVIF up to 5MB each</p>
                            </div>
                        </div>
                        @error('additional_images.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Specification Image -->
                    <div>
                        <label for="specification_image" class="block text-sm font-medium text-gray-700 mb-2">Specification Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="specification_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="specification_image" name="specification_image" type="file" class="sr-only" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP, AVIF up to 5MB</p>
                            </div>
                        </div>
                        @error('specification_image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PDF File -->
                    <div>
                        <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">Product PDF</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="pdf_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="pdf_file" name="pdf_file" type="file" class="sr-only" accept=".pdf">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF up to 10MB</p>
                            </div>
                        </div>
                        @if($product->pdf_file)
                            <div class="mt-2 flex items-center">
                                <input type="checkbox" name="delete_pdf" id="delete_pdf" value="1" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="delete_pdf" class="ml-2 block text-sm text-red-600">Delete existing PDF</label>
                            </div>
                        @endif
                        @error('pdf_file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Specifications Section -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-orange-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Product Specifications
                </h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">Product specifications based on the selected category.</p>
                    <div id="specifications-container" class="space-y-6">
                        @if(isset($templates) && !empty($templates))
                            @foreach($templates as $categoryName => $specs)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-list-ul mr-2 text-indigo-600"></i>
                                        {{ $categoryName }}
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($specs as $spec)
                                            @php
                                                $fieldName = "specifications[{$spec['key']}]";
                                                $fieldId = "spec_{$spec['key']}";
                                                $currentValue = old($fieldName, $existingSpecs->get($spec['key'])->specification_value ?? '');
                                                $required = $spec['required'] ? 'required' : '';
                                                $requiredMark = $spec['required'] ? '<span class="text-red-500">*</span>' : '';
                                            @endphp
                                            
                                            <div>
                                                <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {!! $spec['name'] . ' ' . $requiredMark !!}
                                                    @if(isset($spec['unit']) && $spec['unit'])
                                                        <span class="text-gray-500 text-xs">({{ $spec['unit'] }})</span>
                                                    @endif
                                                </label>
                                                
                                                @if($spec['type'] === 'select')
                                                    <select name="{{ $fieldName }}" id="{{ $fieldId }}" {{ $required }}
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option value="">Select {{ $spec['name'] }}</option>
                                                        @foreach($spec['options'] as $option)
                                                            <option value="{{ $option }}" {{ $currentValue == $option ? 'selected' : '' }}>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @elseif($spec['type'] === 'textarea')
                                                    <textarea name="{{ $fieldName }}" id="{{ $fieldId }}" {{ $required }}
                                                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                              rows="3" placeholder="Enter {{ strtolower($spec['name']) }}">{{ $currentValue }}</textarea>
                                                @elseif($spec['type'] === 'boolean')
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="{{ $fieldName }}" id="{{ $fieldId }}" value="1" {{ $required }}
                                                               {{ $currentValue == '1' || $currentValue == 'Yes' ? 'checked' : '' }}
                                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                        <label for="{{ $fieldId }}" class="ml-2 block text-sm text-gray-900">
                                                            Yes
                                                        </label>
                                                    </div>
                                                @else
                                                    @php
                                                        $inputType = $spec['type'] === 'decimal' ? 'number' : $spec['type'];
                                                        $step = $spec['type'] === 'decimal' ? 'step="0.01"' : '';
                                                    @endphp
                                                    <input type="{{ $inputType }}" name="{{ $fieldName }}" id="{{ $fieldId }}" {{ $required }} {{ $step }}
                                                           value="{{ $currentValue }}"
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                           placeholder="Enter {{ strtolower($spec['name']) }}">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">No specifications available for this category.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-x-6 pt-6">
            <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Update Product
            </button>
        </div>
    </form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image management functionality
        const deleteImagesInput = document.getElementById('delete_images');
        const primaryImageIdInput = document.getElementById('primary_image_id');
        let deletedImageIds = [];
        let newPrimaryImageId = null;

        // Handle image deletion
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-image')) {
                const imageId = e.target.closest('.delete-image').getAttribute('data-image-id');
                const imageItem = e.target.closest('.image-item');
                
                if (confirm('Are you sure you want to delete this image?')) {
                    deletedImageIds.push(imageId);
                    deleteImagesInput.value = deletedImageIds.join(',');
                    imageItem.remove();
                }
            }
        });

        // Handle setting primary image
        document.addEventListener('click', function(e) {
            if (e.target.closest('.set-primary')) {
                const imageId = e.target.closest('.set-primary').getAttribute('data-image-id');
                newPrimaryImageId = imageId;
                primaryImageIdInput.value = imageId;
                
                // Update UI to show new primary image
                document.querySelectorAll('.image-item').forEach(item => {
                    const primaryBadge = item.querySelector('.bg-green-100');
                    const setPrimaryBtn = item.querySelector('.set-primary');
                    
                    if (item.getAttribute('data-image-id') === imageId) {
                        if (!primaryBadge) {
                            const badge = document.createElement('div');
                            badge.className = 'absolute top-2 left-2';
                            badge.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">Primary</span>';
                            item.querySelector('.aspect-w-1').appendChild(badge);
                        }
                        if (setPrimaryBtn) {
                            setPrimaryBtn.remove();
                        }
                    } else {
                        if (primaryBadge) {
                            primaryBadge.remove();
                        }
                        if (!setPrimaryBtn) {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'set-primary inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
                            btn.setAttribute('data-image-id', item.getAttribute('data-image-id'));
                            btn.innerHTML = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>Set Primary';
                            item.querySelector('.flex.flex-col.space-y-2').insertBefore(btn, item.querySelector('.delete-image'));
                        }
                    }
                });
            }
        });

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

        // Size options functionality
        const hasSizeOptionsCheckbox = document.getElementById('has_size_options');
        const sizeOptionsContainer = document.getElementById('size_options_container');
        
        if (hasSizeOptionsCheckbox && sizeOptionsContainer) {
            hasSizeOptionsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    sizeOptionsContainer.style.display = 'block';
                } else {
                    sizeOptionsContainer.style.display = 'none';
                }
            });
        }

        let sizeOptionIndex = {{ count($sizeOptions ?? []) }};

        const addSizeOptionBtn = document.getElementById('add_size_option');
        if (addSizeOptionBtn) {
            addSizeOptionBtn.addEventListener('click', function() {
                const container = document.getElementById('size_options_list');
                const newOption = document.createElement('div');
                newOption.className = 'size-option-item flex items-center space-x-3 mb-3';
                newOption.innerHTML = `
                    <input type="text" name="size_options[]" 
                           placeholder="Size (e.g., S, M, L, XL)"
                           class="block flex-1 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <button type="button" class="remove-size-option inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                container.appendChild(newOption);
                sizeOptionIndex++;
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-size-option')) {
                e.target.closest('.size-option-item').remove();
            }
        });
    });
</script>
@endpush
@endsection
