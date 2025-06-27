@extends('supplier.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Product</h1>
                <p class="text-gray-600 mt-2">Update your product information</p>
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
        <form action="{{ route('supplier.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            
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
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11a2 2 0 01-2 2H8a2 2 0 01-2-2V9a2 2 0 012-2h8a2 2 0 012 2v11zM8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h4"></path>
                                </svg>
                            </div>
                            <input type="text" readonly value="{{ $product->sku }}"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 sm:text-sm">
                        </div>
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
                              placeholder="Add normal product description first, then add technical specifications using the 'PRODUCT PARAMETERS' format.">{{ old('description', $product->description) }}</textarea>
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
                               {{ old('has_size_options', $product->has_size_options) ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm font-medium text-gray-700">Enable size options</span>
                    </label>
                    @error('has_size_options')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="size-options-container" class="{{ old('has_size_options', $product->has_size_options) ? '' : 'hidden' }}">
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
                        @if($product->size_options)
                            @foreach($product->size_options as $index => $option)
                                <div class="size-option-row grid grid-cols-3 gap-4">
                                    <div>
                                        <input type="text" name="size_options[{{ $index }}][size]" value="{{ $option['size'] }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                               placeholder="Size (e.g., Small, 10ml, etc.)">
                                    </div>
                                    <div>
                                        <input type="number" name="size_options[{{ $index }}][price]" value="{{ $option['price'] }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                               placeholder="Price" step="0.01">
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" class="remove-size-option text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
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
                <div class="space-y-8">
                    <!-- Performance Section -->
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Performance Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tests per Kit -->
                            <div>
                                <label for="tests_per_kit" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tests per Kit (tests)
                                </label>
                                <input type="number" name="specifications[tests_per_kit]" id="tests_per_kit"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.tests_per_kit') border-red-300 @enderror"
                                       placeholder="Enter tests per kit"
                                       value="{{ old('specifications.tests_per_kit', $existingSpecs->get('tests_per_kit')->specification_value ?? '') }}">
                                @error('specifications.tests_per_kit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Detection Time -->
                            <div>
                                <label for="detection_time" class="block text-sm font-medium text-gray-700 mb-1">
                                    Detection Time (minutes)
                                </label>
                                <input type="number" name="specifications[detection_time]" id="detection_time"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.detection_time') border-red-300 @enderror"
                                       placeholder="Enter detection time"
                                       value="{{ old('specifications.detection_time', $existingSpecs->get('detection_time')->specification_value ?? '') }}">
                                @error('specifications.detection_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sensitivity -->
                            <div>
                                <label for="sensitivity" class="block text-sm font-medium text-gray-700 mb-1">
                                    Sensitivity (%)
                                </label>
                                <input type="number" name="specifications[sensitivity]" id="sensitivity"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.sensitivity') border-red-300 @enderror"
                                       placeholder="Enter sensitivity"
                                       value="{{ old('specifications.sensitivity', $existingSpecs->get('sensitivity')->specification_value ?? '') }}"
                                       min="0" max="100" step="0.1">
                                @error('specifications.sensitivity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Specificity -->
                            <div>
                                <label for="specificity" class="block text-sm font-medium text-gray-700 mb-1">
                                    Specificity (%)
                                </label>
                                <input type="number" name="specifications[specificity]" id="specificity"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.specificity') border-red-300 @enderror"
                                       placeholder="Enter specificity"
                                       value="{{ old('specifications.specificity', $existingSpecs->get('specificity')->specification_value ?? '') }}"
                                       min="0" max="100" step="0.1">
                                @error('specifications.specificity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Target Pathogen/Analyte -->
                            <div>
                                <label for="target_pathogen" class="block text-sm font-medium text-gray-700 mb-1">
                                    Target Pathogen/Analyte
                                </label>
                                <select name="specifications[target_pathogen]" id="target_pathogen"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.target_pathogen') border-red-300 @enderror">
                                    <option value="">Select Target Pathogen/Analyte</option>
                                                                   @php $targetPathogenValue = old('specifications.target_pathogen', $existingSpecs->get('target_pathogen')->specification_value ?? ''); @endphp
                               <option value="Bacteria" {{ $targetPathogenValue == 'Bacteria' ? 'selected' : '' }}>Bacteria</option>
                               <option value="Virus" {{ $targetPathogenValue == 'Virus' ? 'selected' : '' }}>Virus</option>
                               <option value="Fungi" {{ $targetPathogenValue == 'Fungi' ? 'selected' : '' }}>Fungi</option>
                               <option value="Parasite" {{ $targetPathogenValue == 'Parasite' ? 'selected' : '' }}>Parasite</option>
                               <option value="Toxin" {{ $targetPathogenValue == 'Toxin' ? 'selected' : '' }}>Toxin</option>
                                </select>
                                @error('specifications.target_pathogen')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Physical Section -->
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Physical Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Sample Type -->
                            <div>
                                <label for="sample_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Sample Type
                                </label>
                                <select name="specifications[sample_type]" id="sample_type"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.sample_type') border-red-300 @enderror">
                                    <option value="">Select Sample Type</option>
                                                                   @php $sampleTypeValue = old('specifications.sample_type', $existingSpecs->get('sample_type')->specification_value ?? ''); @endphp
                               <option value="Blood" {{ $sampleTypeValue == 'Blood' ? 'selected' : '' }}>Blood</option>
                               <option value="Urine" {{ $sampleTypeValue == 'Urine' ? 'selected' : '' }}>Urine</option>
                               <option value="Saliva" {{ $sampleTypeValue == 'Saliva' ? 'selected' : '' }}>Saliva</option>
                               <option value="Swab" {{ $sampleTypeValue == 'Swab' ? 'selected' : '' }}>Swab</option>
                               <option value="Tissue" {{ $sampleTypeValue == 'Tissue' ? 'selected' : '' }}>Tissue</option>
                                </select>
                                @error('specifications.sample_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sample Area -->
                            <div>
                                <label for="sample_area" class="block text-sm font-medium text-gray-700 mb-1">
                                    Sample Area (cm²)
                                </label>
                                <input type="number" name="specifications[sample_area]" id="sample_area"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.sample_area') border-red-300 @enderror"
                                       placeholder="Enter sample area"
                                       value="{{ old('specifications.sample_area', $existingSpecs->get('sample_area')->specification_value ?? '') }}"
                                       step="0.01">
                                @error('specifications.sample_area')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Storage Temperature -->
                            <div>
                                <label for="storage_temperature" class="block text-sm font-medium text-gray-700 mb-1">
                                    Storage Temperature (°C)
                                </label>
                                <input type="text" name="specifications[storage_temperature]" id="storage_temperature"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.storage_temperature') border-red-300 @enderror"
                                       placeholder="Enter storage temperature"
                                       value="{{ old('specifications.storage_temperature', $existingSpecs->get('storage_temperature')->specification_value ?? '') }}">
                                @error('specifications.storage_temperature')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Shelf Life -->
                            <div>
                                <label for="shelf_life" class="block text-sm font-medium text-gray-700 mb-1">
                                    Shelf Life (months)
                                </label>
                                <input type="number" name="specifications[shelf_life]" id="shelf_life"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.shelf_life') border-red-300 @enderror"
                                       placeholder="Enter shelf life"
                                       value="{{ old('specifications.shelf_life', $existingSpecs->get('shelf_life')->specification_value ?? '') }}">
                                @error('specifications.shelf_life')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Application Surface -->
                            <div>
                                <label for="application_surface" class="block text-sm font-medium text-gray-700 mb-1">
                                    Application Surface
                                </label>
                                <select name="specifications[application_surface]" id="application_surface"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.application_surface') border-red-300 @enderror">
                                    <option value="">Select Application Surface</option>
                                                                   @php $applicationSurfaceValue = old('specifications.application_surface', $existingSpecs->get('application_surface')->specification_value ?? ''); @endphp
                               <option value="Skin" {{ $applicationSurfaceValue == 'Skin' ? 'selected' : '' }}>Skin</option>
                               <option value="Mucosa" {{ $applicationSurfaceValue == 'Mucosa' ? 'selected' : '' }}>Mucosa</option>
                               <option value="Surface" {{ $applicationSurfaceValue == 'Surface' ? 'selected' : '' }}>Surface</option>
                                </select>
                                @error('specifications.application_surface')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Regulatory Section -->
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Regulatory Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- CE Marking -->
                            <div>
                                <label for="ce_marking" class="block text-sm font-medium text-gray-700 mb-1">
                                    CE Marking
                                </label>
                                <select name="specifications[ce_marking]" id="ce_marking"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.ce_marking') border-red-300 @enderror">
                                    <option value="">Select CE Marking</option>
                                                                   @php $ceMarkingValue = old('specifications.ce_marking', $existingSpecs->get('ce_marking')->specification_value ?? ''); @endphp
                               <option value="Yes" {{ $ceMarkingValue == 'Yes' ? 'selected' : '' }}>Yes</option>
                               <option value="No" {{ $ceMarkingValue == 'No' ? 'selected' : '' }}>No</option>
                               <option value="Pending" {{ $ceMarkingValue == 'Pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                                @error('specifications.ce_marking')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- FDA Status -->
                            <div>
                                <label for="fda_status" class="block text-sm font-medium text-gray-700 mb-1">
                                    FDA Status
                                </label>
                                <select name="specifications[fda_status]" id="fda_status"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.fda_status') border-red-300 @enderror">
                                    <option value="">Select FDA Status</option>
                                                                   @php $fdaStatusValue = old('specifications.fda_status', $existingSpecs->get('fda_status')->specification_value ?? ''); @endphp
                               <option value="Approved" {{ $fdaStatusValue == 'Approved' ? 'selected' : '' }}>Approved</option>
                               <option value="Cleared" {{ $fdaStatusValue == 'Cleared' ? 'selected' : '' }}>Cleared</option>
                               <option value="Pending" {{ $fdaStatusValue == 'Pending' ? 'selected' : '' }}>Pending</option>
                               <option value="Not Required" {{ $fdaStatusValue == 'Not Required' ? 'selected' : '' }}>Not Required</option>
                                </select>
                                @error('specifications.fda_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ISO Certification -->
                            <div>
                                <label for="iso_certification" class="block text-sm font-medium text-gray-700 mb-1">
                                    ISO Certification
                                </label>
                                <input type="text" name="specifications[iso_certification]" id="iso_certification"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.iso_certification') border-red-300 @enderror"
                                       placeholder="Enter ISO certification"
                                       value="{{ old('specifications.iso_certification', $existingSpecs->get('iso_certification')->specification_value ?? '') }}">
                                @error('specifications.iso_certification')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- HACCP Compliance -->
                            <div>
                                <label for="haccp_compliance" class="block text-sm font-medium text-gray-700 mb-1">
                                    HACCP Compliance
                                </label>
                                <select name="specifications[haccp_compliance]" id="haccp_compliance"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.haccp_compliance') border-red-300 @enderror">
                                    <option value="">Select HACCP Compliance</option>
                                                                   @php $haccpComplianceValue = old('specifications.haccp_compliance', $existingSpecs->get('haccp_compliance')->specification_value ?? ''); @endphp
                               <option value="Yes" {{ $haccpComplianceValue == 'Yes' ? 'selected' : '' }}>Yes</option>
                               <option value="No" {{ $haccpComplianceValue == 'No' ? 'selected' : '' }}>No</option>
                               <option value="In Progress" {{ $haccpComplianceValue == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                </select>
                                @error('specifications.haccp_compliance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Food Safety Standards -->
                            <div>
                                <label for="food_safety_standards" class="block text-sm font-medium text-gray-700 mb-1">
                                    Food Safety Standards
                                </label>
                                <select name="specifications[food_safety_standards]" id="food_safety_standards"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.food_safety_standards') border-red-300 @enderror">
                                    <option value="">Select Food Safety Standards</option>
                                                                   @php $foodSafetyStandardsValue = old('specifications.food_safety_standards', $existingSpecs->get('food_safety_standards')->specification_value ?? ''); @endphp
                               <option value="ISO 22000" {{ $foodSafetyStandardsValue == 'ISO 22000' ? 'selected' : '' }}>ISO 22000</option>
                               <option value="FSSC 22000" {{ $foodSafetyStandardsValue == 'FSSC 22000' ? 'selected' : '' }}>FSSC 22000</option>
                               <option value="BRC" {{ $foodSafetyStandardsValue == 'BRC' ? 'selected' : '' }}>BRC</option>
                               <option value="IFS" {{ $foodSafetyStandardsValue == 'IFS' ? 'selected' : '' }}>IFS</option>
                               <option value="Not Applicable" {{ $foodSafetyStandardsValue == 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                                </select>
                                @error('specifications.food_safety_standards')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Technical Section -->
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Technical Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Test Method -->
                            <div>
                                <label for="test_method" class="block text-sm font-medium text-gray-700 mb-1">
                                    Test Method
                                </label>
                                <select name="specifications[test_method]" id="test_method"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.test_method') border-red-300 @enderror">
                                    <option value="">Select Test Method</option>
                                                                   @php $testMethodValue = old('specifications.test_method', $existingSpecs->get('test_method')->specification_value ?? ''); @endphp
                               <option value="Lateral Flow" {{ $testMethodValue == 'Lateral Flow' ? 'selected' : '' }}>Lateral Flow</option>
                               <option value="ELISA" {{ $testMethodValue == 'ELISA' ? 'selected' : '' }}>ELISA</option>
                               <option value="PCR" {{ $testMethodValue == 'PCR' ? 'selected' : '' }}>PCR</option>
                               <option value="Culture" {{ $testMethodValue == 'Culture' ? 'selected' : '' }}>Culture</option>
                                </select>
                                @error('specifications.test_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reading Method -->
                            <div>
                                <label for="reading_method" class="block text-sm font-medium text-gray-700 mb-1">
                                    Reading Method
                                </label>
                                <select name="specifications[reading_method]" id="reading_method"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.reading_method') border-red-300 @enderror">
                                    <option value="">Select Reading Method</option>
                                                                   @php $readingMethodValue = old('specifications.reading_method', $existingSpecs->get('reading_method')->specification_value ?? ''); @endphp
                               <option value="Visual" {{ $readingMethodValue == 'Visual' ? 'selected' : '' }}>Visual</option>
                               <option value="Reader Required" {{ $readingMethodValue == 'Reader Required' ? 'selected' : '' }}>Reader Required</option>
                               <option value="Automated" {{ $readingMethodValue == 'Automated' ? 'selected' : '' }}>Automated</option>
                                </select>
                                @error('specifications.reading_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Detection Limit -->
                            <div>
                                <label for="detection_limit" class="block text-sm font-medium text-gray-700 mb-1">
                                    Detection Limit (CFU/ml or RLU)
                                </label>
                                <input type="text" name="specifications[detection_limit]" id="detection_limit"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.detection_limit') border-red-300 @enderror"
                                       placeholder="Enter detection limit"
                                       value="{{ old('specifications.detection_limit', $existingSpecs->get('detection_limit')->specification_value ?? '') }}">
                                @error('specifications.detection_limit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Validation Studies -->
                            <div class="md:col-span-2">
                                <label for="validation_studies" class="block text-sm font-medium text-gray-700 mb-1">
                                    Validation Studies
                                </label>
                                <textarea name="specifications[validation_studies]" id="validation_studies"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.validation_studies') border-red-300 @enderror"
                                          rows="4"
                                          placeholder="Enter validation studies details">{{ old('specifications.validation_studies', $existingSpecs->get('validation_studies')->specification_value ?? '') }}</textarea>
                                @error('specifications.validation_studies')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
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
                <!-- Current Images Display -->
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
                                        <input id="image" name="image" type="file" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                            </div>
                        </div>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="additional_images" class="block text-sm font-medium text-gray-700 mb-2">Additional Product Images</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="additional_images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload files</span>
                                        <input id="additional_images" name="additional_images[]" type="file" class="sr-only" multiple>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each</p>
                            </div>
                        </div>
                        @error('additional_images')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('additional_images.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="specification_image" class="block text-sm font-medium text-gray-700 mb-2">Product Specification Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="specification_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="specification_image" name="specification_image" type="file" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                            </div>
                        </div>
                        @error('specification_image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Product Documentation -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Product Documentation
                </h3>
                <p class="text-gray-600 mt-1">Upload and manage product documentation</p>
            </div>

            @if($product->pdf_file)
                <div class="p-6">
                    <div class="alert alert-info rounded-input">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-pdf me-2"></i>
                                Current PDF: <a href="{{ asset('storage/' . $product->pdf_file) }}" target="_blank" class="btn btn-sm btn-outline-primary border border-primary">View PDF</a>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="delete_pdf" name="delete_pdf" value="1">
                                <label class="form-check-label text-danger" for="delete_pdf">
                                    Delete current PDF
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-6">
                <div class="form-group">
                    <label for="pdf_file" class="form-label fw-medium">
                        @if($product->pdf_file)
                            Replace Product PDF
                        @else
                            Product PDF
                        @endif
                    </label>
                    <div class="input-group">
                        <input type="file" name="pdf_file" id="pdf_file" class="form-control rounded-input" accept=".pdf">
                    </div>
                    <small class="text-muted">Upload a PDF file containing product documentation or specifications</small>
                    @error('pdf_file')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Dynamic Specifications -->
            <div id="specifications-container">
                @if(isset($categorySpecs) && count($categorySpecs) > 0)
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Category-Specific Specifications</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($categorySpecs as $spec)
                                <div class="form-group">
                                    <label for="spec_{{ $spec->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ $spec->name }}
                                        @if($spec->unit)
                                            ({{ $spec->unit }})
                                        @endif
                                    </label>

                                    @if($spec->type === 'text')
                                        <input type="text" 
                                               name="specifications[{{ $spec->key }}]" 
                                               id="spec_{{ $spec->id }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.' . $spec->key) border-red-300 @enderror"
                                               placeholder="Enter {{ strtolower($spec->name) }}"
                                               value="{{ old('specifications.' . $spec->key, $existingSpecs->get($spec->key)->specification_value ?? '') }}">
                                    @elseif($spec->type === 'textarea')
                                        <textarea name="specifications[{{ $spec->key }}]" 
                                                  id="spec_{{ $spec->id }}"
                                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.' . $spec->key) border-red-300 @enderror"
                                                  rows="3"
                                                  placeholder="Enter {{ strtolower($spec->name) }}">{{ old('specifications.' . $spec->key, $existingSpecs->get($spec->key)->specification_value ?? '') }}</textarea>
                                    @elseif($spec->type === 'boolean')
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="specifications[{{ $spec->key }}]" 
                                                   id="spec_{{ $spec->id }}" 
                                                   value="1"
                                                   class="form-check-input @error('specifications.' . $spec->key) border-red-300 @enderror"
                                                   {{ old('specifications.' . $spec->key, $existingSpecs->get($spec->key)->specification_value ?? '') ? 'checked' : '' }}>
                                            <label for="spec_{{ $spec->id }}" class="form-check-label">Yes</label>
                                        </div>
                                    @else
                                        <input type="{{ $spec->type === 'decimal' ? 'number' : $spec->type }}" 
                                               name="specifications[{{ $spec->key }}]" 
                                               id="spec_{{ $spec->id }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.' . $spec->key) border-red-300 @enderror"
                                               placeholder="Enter {{ strtolower($spec->name) }}"
                                               value="{{ old('specifications.' . $spec->key, $existingSpecs->get($spec->key)->specification_value ?? '') }}"
                                               @if($spec->type === 'decimal') step="0.01" @endif>
                                    @endif

                                    @error('specifications.' . $spec->key)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Submit Section -->
            <div class="px-6 py-4 bg-gray-50">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('supplier.products.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 