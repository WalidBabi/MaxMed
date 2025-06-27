@extends('supplier.layouts.app')

@section('title', $product->name)

@section('content')
<div class="p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
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
                                <a href="{{ route('supplier.products.index') }}" class="ml-1 text-gray-700 hover:text-gray-900 md:ml-2">Products</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-gray-500 md:ml-2">{{ $product->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('supplier.product-specifications.edit', $product) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Specifications
                </a>
                <a href="{{ route('supplier.products.edit', $product) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Product
                </a>
                <a href="{{ route('supplier.products.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Products
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Product Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-6">
            <div class="flex items-start">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                         class="w-32 h-32 rounded-lg object-cover mr-6 flex-shrink-0">
                @endif
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Category:</span>
                            <span class="text-gray-600">{{ $product->category->name ?? 'No Category' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Brand:</span>
                            <span class="text-gray-600">{{ $product->brand->name ?? 'No Brand' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">SKU:</span>
                            <span class="text-gray-600">{{ $product->sku }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Stock:</span>
                            <span class="text-gray-600">{{ $product->inventory->quantity ?? 0 }} units</span>
                        </div>
                    </div>
                    @if($product->description)
                        <div class="mt-4">
                            <span class="font-medium text-gray-700">Description:</span>
                            <p class="text-gray-600 mt-1">{{ $product->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Specifications -->
    @if($specsByCategory->count() > 0)
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Product Specifications</h2>
            @foreach($specsByCategory as $categoryName => $specs)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $categoryName }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($specs as $spec)
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $spec->display_name }}</h4>
                                        @if($spec->unit)
                                            <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">{{ $spec->unit }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $spec->specification_value }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Specifications State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No specifications defined</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding specifications for this product.</p>
                <div class="mt-6">
                    <a href="{{ route('supplier.product-specifications.edit', $product) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Specifications
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Additional Images -->
    @if($product->images->where('is_primary', false)->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Additional Images</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($product->images->where('is_primary', false) as $image)
                        <div class="aspect-square">
                            <img src="{{ $image->image_url }}" alt="Product image" 
                                 class="w-full h-full object-cover rounded-lg border border-gray-200">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 