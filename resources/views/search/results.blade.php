@extends('layouts.app')

@section('content')
@php
    // Helper to highlight search terms
    function highlight($text, $query) {
        if (!$query) return $text;
        $pattern = '/(' . preg_quote($query, '/') . ')/i';
        return preg_replace($pattern, '<span class="bg-yellow-200 font-bold">$1</span>', $text);
    }
@endphp
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-3xl font-semibold text-[#171e60]">Search Results for "{{ $query }}"</h2>
        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4" role="alert">
                <p>{{ $error }}</p>
            </div>
        @else
            <p class="text-gray-600 mt-2">Found {{ $products->total() }} {{ Str::plural('result', $products->total()) }}</p>
        @endif
    </div>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300 border border-gray-100 flex flex-col h-full group">
                    <a href="{{ route('product.show', $product) }}" class="block h-full">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-56 object-contain bg-gray-50 p-4 group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-56 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">No image available</span>
                            </div>
                        @endif
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="text-lg font-bold text-[#171e60] mb-1 leading-tight line-clamp-2">{!! highlight(e($product->name), $query) !!}</h3>
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{!! highlight(e(Str::limit($product->description, 100)), $query) !!}</p>
                            <div class="flex flex-wrap items-center text-xs text-gray-500 mb-2 gap-2">
                                @if($product->brand)
                                    <span class="bg-gray-100 px-2 py-1 rounded">{{ $product->brand->name }}</span>
                                @endif
                                @if($product->category)
                                    <span class="bg-gray-100 px-2 py-1 rounded">{{ $product->category->name }}</span>
                                @endif
                            </div>
                            @if(isset($product->price) && $product->price > 0)
                                {{-- <div class="text-[#171e60] font-bold text-base mb-3">${{ number_format($product->price, 2) }}</div> --}}
                            @endif
                            <a href="{{ route('product.show', $product) }}" class="mt-auto inline-block bg-[#171e60] hover:bg-[#0a5694] text-white px-4 py-2 rounded-lg font-semibold shadow transition-colors duration-200 text-center w-full">View Details</a>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-8 flex justify-center">
            {{ $products->appends(['query' => $query])->links() }}
        </div>
    @else
        <div class="bg-white p-12 rounded-2xl shadow-lg text-center flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-gray-300 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-2xl font-semibold text-[#171e60] mb-2">No products found</h3>
            <p class="text-gray-600 mb-6">We couldn't find any products matching your search "{{ $query }}".</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-[#171e60] hover:bg-[#0a5694] text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-300">Browse All Products</a>
        </div>
    @endif
</div>
@endsection 