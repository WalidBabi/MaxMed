@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-3xl font-semibold text-gray-800">Search Results for "{{ $query }}"</h2>
        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4" role="alert">
                <p>{{ $error }}</p>
            </div>
        @else
            <p class="text-gray-600 mt-2">Found {{ $products->total() }} {{ Str::plural('result', $products->total()) }}</p>
        @endif
    </div>

    <!-- Search Tips -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <span class="font-medium">Search Tips:</span> Try using specific product names, categories, or product codes for better results.
                </p>
            </div>
        </div>
    </div>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <a href="{{ route('product.show', $product->id) }}" class="block h-full">
                        <div class="relative">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">No image available</span>
                                </div>
                            @endif
                            @if(isset($product->category))
                                <span class="absolute top-2 right-2 bg-[#171e60] text-white text-xs px-2 py-1 rounded">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($product->description, 100) }}</p>
                            <div class="flex justify-between items-center">
                                <!-- Product SKU for identification -->
                                <span class="text-xs text-gray-500">{{ $product->sku ?? 'No SKU' }}</span>
                                <div class="mt-2">
                                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">View Details</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->appends(['query' => $query])->links() }}
        </div>

        <!-- Related searches suggestion -->
        <div class="mt-10 bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-800 mb-3">You might also be interested in:</h3>
            <div class="flex flex-wrap gap-2">
                @php
                    $relatedTerms = [
                        $query . ' equipment',
                        $query . ' products',
                        'medical ' . $query,
                        'new ' . $query,
                        $query . ' accessories'
                    ];
                @endphp
                
                @foreach($relatedTerms as $term)
                    <a href="{{ route('search', ['query' => $term]) }}" 
                       class="inline-block px-3 py-1 bg-white border border-gray-300 rounded-full text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        {{ $term }}
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found</h3>
            <p class="text-gray-600">We couldn't find any products matching your search "{{ $query }}".</p>
            
            <!-- Try alternative search suggestion -->
            <div class="mt-4 mb-6">
                <p class="text-gray-700">You can try searching for:</p>
                <div class="flex flex-wrap justify-center gap-2 mt-2">
                    <a href="{{ route('search', ['query' => strtolower($query)]) }}" 
                       class="inline-block px-3 py-1 bg-blue-50 border border-blue-200 rounded-full text-sm text-blue-700 hover:bg-blue-100 transition-colors duration-200">
                        {{ strtolower($query) }}
                    </a>
                    <a href="{{ route('search', ['query' => strtoupper($query)]) }}" 
                       class="inline-block px-3 py-1 bg-blue-50 border border-blue-200 rounded-full text-sm text-blue-700 hover:bg-blue-100 transition-colors duration-200">
                        {{ strtoupper($query) }}
                    </a>
                </div>
            </div>
            
            <!-- Search suggestions -->
            <div class="mt-8 text-left">
                <h4 class="text-md font-medium text-gray-700 mb-3">Suggestions:</h4>
                <ul class="list-disc list-inside text-gray-600 space-y-2">
                    <li>Check the spelling of your search term</li>
                    <li>Try using more general keywords</li>
                    <li>Try searching by product category</li>
                    <li>Search for related products</li>
                </ul>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="inline-block bg-[#171e60] hover:bg-[#0a5694] text-white px-4 py-2 rounded-md transition-colors duration-300">
                    Browse All Products
                </a>
            </div>
        </div>
    @endif
    
</div>
@endsection 