@extends('admin.layouts.app')

@section('title', 'Product Details - ' . $product->name)

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header with Actions -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                                <span class="sr-only">Products</span>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <a href="{{ route('admin.products.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Products</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-500">{{ $product->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-gray-600 mt-1">Product Details and Information</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Product
                </a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Product Images -->
        <div class="lg:col-span-2">
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Images</h3>
                    
                    @if($product->images && $product->images->isNotEmpty())
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Primary Image -->
                            @php
                                $primaryImage = $product->images->where('is_primary', true)->first() ?: $product->images->first();
                            @endphp
                            
                            @if($primaryImage)
                                <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden">
                                    <img src="{{ $primaryImage->image_url }}" alt="{{ $product->name }}" class="w-full h-96 object-cover object-center">
                                </div>
                            @endif
                            
                            <!-- Additional Images -->
                            @if($product->images->count() > 1)
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4">
                                    @foreach($product->images->where('is_primary', false) as $image)
                                        <div class="aspect-w-1 aspect-h-1 bg-gray-100 rounded-lg overflow-hidden">
                                            <img src="{{ $image->image_url }}" alt="{{ $product->name }}" class="w-full h-32 object-cover object-center">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @elseif($product->image_url)
                        <!-- Fallback to main image_url if no images in images table -->
                        <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-96 object-cover object-center">
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No images available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Description -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                    <div class="prose max-w-none text-gray-700">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>

            <!-- Files Section -->
            @if($product->pdf_file)
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Attached Files</h3>
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <svg class="h-8 w-8 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0-1.125-.504-1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Product Manual / Specification</p>
                                <p class="text-xs text-gray-500">PDF Document</p>
                            </div>
                            <a href="{{ asset('storage/' . $product->pdf_file) }}" target="_blank" class="inline-flex items-center px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Information Sidebar -->
        <div class="lg:col-span-1">
            <!-- Basic Information -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price (USD)</dt>
                            <dd class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price (AED)</dt>
                            <dd class="text-xl font-semibold text-gray-700">AED {{ number_format($product->price_aed, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</dd>
                        </div>
                        @if($product->brand)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Brand</dt>
                                <dd class="text-sm text-gray-900">{{ $product->brand->name }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Inventory Information -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Stock Quantity</dt>
                            <dd class="flex items-center">
                                <span class="text-2xl font-bold text-gray-900">{{ $product->inventory->quantity ?? 0 }}</span>
                                <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ ($product->inventory->quantity ?? 0) > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ($product->inventory->quantity ?? 0) > 0 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Size Options -->
            @if($product->has_size_options && $product->size_options)
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Sizes</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(json_decode($product->size_options) as $size)
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                    {{ $size }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Meta Information -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Meta Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Product ID</dt>
                            <dd class="text-sm text-gray-900">#{{ $product->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $product->created_at->format('M d, Y \a\t H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Last Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $product->updated_at->format('M d, Y \a\t H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 