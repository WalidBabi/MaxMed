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

    @if($products->count() > 0)
        <div class="products-grid">
            @foreach($products as $product)
                <div class="product-card-wrapper">
                    <div class="card h-100 product-card">
                        <a href="{{ route('product.show', $product) }}" class="product-image-container">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">No image</span>
                                </div>
                            @endif
                            @if($product->category)
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-light text-dark px-2 py-1 rounded-pill shadow-sm">{{ $product->category->name }}</span>
                                </div>
                            @endif
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="{{ route('product.show', $product) }}">{{ $product->name }}</a>
                            </h5>
                            <div class="card-footer">
                                <div class="button-group">
                                    <a href="{{ route('product.show', $product) }}" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <a href="{{ route('quotation.form', ['product' => $product->id]) }}" class="btn btn-quote">
                                        <i class="fas fa-file-invoice"></i> Request Quote
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->appends(['query' => $query])->links() }}
        </div>
    @else
        <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found</h3>
            <p class="text-gray-600">We couldn't find any products matching your search "{{ $query }}".</p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="inline-block bg-[#171e60] hover:bg-[#0a5694] text-white px-4 py-2 rounded-md transition-colors duration-300">
                    Browse All Products
                </a>
            </div>
        </div>
    @endif
    
</div>
@endsection 