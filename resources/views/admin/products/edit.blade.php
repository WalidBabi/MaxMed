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
                                       value="{{ old('inventory_quantity', $product->inventory->quantity) }}" min="0"
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
