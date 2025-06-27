@extends('supplier.layouts.app')

@section('title', 'Edit Product Specifications - ' . $product->name)

@section('content')
<div class="p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Edit Product Specifications
                </h1>
                <!-- Breadcrumb -->
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('supplier.dashboard') }}" class="text-gray-700 hover:text-gray-900 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('supplier.products.show', $product) }}" class="ml-1 text-gray-700 hover:text-gray-900 md:ml-2">{{ $product->name }}</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-gray-500 md:ml-2">Edit Specifications</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('supplier.products.show', $product) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Product
                </a>
            </div>
        </div>
    </div>

    <!-- Product Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
            <div class="flex items-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                         class="w-16 h-16 rounded-lg object-cover mr-4">
                @endif
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $product->category->name ?? 'No Category' }} • 
                        {{ $product->brand->name ?? 'No Brand' }} • 
                        SKU: {{ $product->sku }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Specifications Form -->
    <form action="{{ route('supplier.product-specifications.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3">
                <!-- Category-Specific Specifications -->
                @foreach($templates as $categoryName => $categorySpecs)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ $categoryName }} Specifications
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($categorySpecs as $spec)
                                    <div>
                                        <label for="{{ $spec['key'] }}" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $spec['name'] }}
                                            @if($spec['required'])
                                                <span class="text-red-600">*</span>
                                            @endif
                                            @if($spec['unit'])
                                                <span class="text-gray-500 text-xs">({{ $spec['unit'] }})</span>
                                            @endif
                                        </label>
                                        
                                        @php
                                            $currentValue = old('specifications.' . $spec['key']) ?? ($existingSpecs->get($spec['key'])->specification_value ?? '');
                                        @endphp
                                        
                                        @if($spec['type'] === 'select')
                                            <div class="relative">
                                                <select name="specifications[{{ $spec['key'] }}]" 
                                                        id="{{ $spec['key'] }}" 
                                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.'.$spec['key']) border-red-300 @enderror"
                                                        {{ $spec['required'] ? 'required' : '' }}>
                                                    <option value="">Select {{ $spec['name'] }}</option>
                                                    @foreach($spec['options'] as $option)
                                                        <option value="{{ $option }}" 
                                                                {{ $currentValue == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif($spec['type'] === 'boolean')
                                            <div class="flex items-center">
                                                <input type="hidden" name="specifications[{{ $spec['key'] }}]" value="0">
                                                <input type="checkbox" 
                                                       name="specifications[{{ $spec['key'] }}]" 
                                                       id="{{ $spec['key'] }}" 
                                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded @error('specifications.'.$spec['key']) border-red-300 @enderror"
                                                       value="1"
                                                       {{ $currentValue ? 'checked' : '' }}>
                                                <label class="ml-3 text-sm text-gray-700" for="{{ $spec['key'] }}">
                                                    Yes
                                                </label>
                                            </div>
                                        @elseif($spec['type'] === 'textarea')
                                            <textarea name="specifications[{{ $spec['key'] }}]" 
                                                      id="{{ $spec['key'] }}" 
                                                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.'.$spec['key']) border-red-300 @enderror"
                                                      rows="3"
                                                      placeholder="Enter {{ strtolower($spec['name']) }}"
                                                      {{ $spec['required'] ? 'required' : '' }}>{{ $currentValue }}</textarea>
                                        @else
                                            <input type="{{ $spec['type'] === 'decimal' ? 'number' : $spec['type'] }}" 
                                                   name="specifications[{{ $spec['key'] }}]" 
                                                   id="{{ $spec['key'] }}" 
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('specifications.'.$spec['key']) border-red-300 @enderror"
                                                   value="{{ $currentValue }}"
                                                   placeholder="Enter {{ strtolower($spec['name']) }}"
                                                   {{ $spec['type'] === 'decimal' ? 'step=0.01' : '' }}
                                                   {{ $spec['required'] ? 'required' : '' }}>
                                        @endif
                                        
                                        @error('specifications.'.$spec['key'])
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Form Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-3">
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Save Specifications
                                </button>
                                <a href="{{ route('supplier.products.show', $product) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Help Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Specification Guidelines
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-gray-600">
                            <h4 class="font-semibold text-indigo-600 mb-3">{{ $product->category->name ?? 'Product' }} Specifications</h4>
                            
                            @if(str_contains(strtolower($product->category->name ?? ''), 'rapid test'))
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Tests per Kit:</span>
                                        <span class="text-gray-600">Number of individual tests included</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Detection Time:</span>
                                        <span class="text-gray-600">Time required to get results</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Sensitivity:</span>
                                        <span class="text-gray-600">Ability to detect true positives (%)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Specificity:</span>
                                        <span class="text-gray-600">Ability to avoid false positives (%)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Sample Type:</span>
                                        <span class="text-gray-600">Required specimen for testing</span>
                                    </div>
                                </div>
                            @elseif(str_contains(strtolower($product->category->name ?? ''), 'foot test'))
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Tests per Kit:</span>
                                        <span class="text-gray-600">Number of individual tests included</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Detection Time:</span>
                                        <span class="text-gray-600">Time required to get results</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Target Pathogen:</span>
                                        <span class="text-gray-600">Type of pathogen or substance detected (e.g., ALP, ATP)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Sample Type:</span>
                                        <span class="text-gray-600">Method of sample collection</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Application Surface:</span>
                                        <span class="text-gray-600">Where the test can be applied (footwear, floors, etc.)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Test Method:</span>
                                        <span class="text-gray-600">Detection method used (enzymatic, colorimetric, etc.)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">HACCP Compliance:</span>
                                        <span class="text-gray-600">Meets HACCP food safety requirements</span>
                                    </div>
                                </div>
                            @elseif(str_contains(strtolower($product->category->name ?? ''), 'food test'))
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Tests per Kit:</span>
                                        <span class="text-gray-600">Number of individual tests included</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Detection Time:</span>
                                        <span class="text-gray-600">Time required to get results</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Target Pathogen/Analyte:</span>
                                        <span class="text-gray-600">What the test detects (ALP, ATP, E. coli, Salmonella, etc.)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Sample Type:</span>
                                        <span class="text-gray-600">Method of sample collection (swab, direct contact, food sample)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Application Surface:</span>
                                        <span class="text-gray-600">Where to use (food contact surfaces, utensils, cutting boards, etc.)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Test Method:</span>
                                        <span class="text-gray-600">Detection technology (ATP bioluminescence, enzymatic, etc.)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Food Safety Standards:</span>
                                        <span class="text-gray-600">Compliance with FDA, USDA FSIS, BRC, SQF, etc.</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">HACCP Compliance:</span>
                                        <span class="text-gray-600">Meets HACCP food safety requirements</span>
                                    </div>
                                </div>
                            @elseif(str_contains(strtolower($product->category->name ?? ''), 'shaker'))
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Speed Range:</span>
                                        <span class="text-gray-600">Minimum to maximum RPM</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Amplitude:</span>
                                        <span class="text-gray-600">Shaking distance in millimeters</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Load Capacity:</span>
                                        <span class="text-gray-600">Maximum weight it can handle</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Motion Type:</span>
                                        <span class="text-gray-600">Type of shaking motion</span>
                                    </div>
                                </div>
                            @elseif(str_contains(strtolower($product->category->name ?? ''), 'centrifuge'))
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Max Speed:</span>
                                        <span class="text-gray-600">Maximum rotations per minute</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Max RCF:</span>
                                        <span class="text-gray-600">Maximum relative centrifugal force</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Rotor Capacity:</span>
                                        <span class="text-gray-600">Number of tubes it can hold</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Temperature Control:</span>
                                        <span class="text-gray-600">Ambient or refrigerated</span>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Model Number:</span>
                                        <span class="text-gray-600">Manufacturer's model identifier</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Dimensions:</span>
                                        <span class="text-gray-600">Physical size (W×D×H)</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Weight:</span>
                                        <span class="text-gray-600">Product weight in kg</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="font-medium text-gray-700 mr-2">Power:</span>
                                        <span class="text-gray-600">Power consumption/requirements</span>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-xs text-blue-800">
                                    <strong>Tip:</strong> Accurate specifications help customers find the right products and improve your quotation success rate.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection 