@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="relative bg-white py-16">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-indigo-50 opacity-70"></div>
        <svg class="absolute bottom-0 left-0 w-full text-white" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="currentColor" fill-opacity="1" d="M0,256L80,234.7C160,213,320,171,480,160C640,149,800,171,960,170.7C1120,171,1280,149,1360,138.7L1440,128L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
        </svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center">
            <h1 class="text-4xl  tracking-tight text-[#171e60] sm:text-5xl md:text-6xl">
                News & Updates
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl text-[#0a5694]">
                Stay updated with the latest information and developments from MaxMed
            </p>
        </div>
    </div>
</div>

<!-- News Categories -->
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center space-x-2 md:space-x-4 overflow-x-auto py-4">
            <button class="px-4 py-2 bg-[#171e60] text-white rounded-full font-medium text-sm">All News</button>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full font-medium text-sm">Company Updates</button>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full font-medium text-sm">Product Launches</button>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full font-medium text-sm">Industry Insights</button>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full font-medium text-sm">Events</button>
        </div>
    </div>
</div>

@if(count($news) > 0)
    <!-- Featured News -->
    @if(isset($news[0]))
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base font-semibold text-[#0a5694] uppercase tracking-wide">Latest Update</h2>
                <p class="mt-2 text-3xl  text-gray-900 sm:text-4xl">Featured News</p>
            </div>
            
            <div class="mt-12">
                <div class="lg:flex items-center bg-white rounded-xl shadow-lg overflow-hidden">
                    @if($news[0]->image_url)
                    <div class="lg:w-1/2">
                        <img src="{{ asset($news[0]->image_url) }}" alt="{{ $news[0]->title }}" class="h-96 w-full object-cover">
                    </div>
                    @endif
                    <div class="p-8 lg:w-1/2">
                        <div class="flex items-center mb-2">
                            <span class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Featured</span>
                            <span class="ml-2 text-sm text-gray-500">{{ $news[0]->created_at->format('F d, Y') }}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $news[0]->title }}</h3>
                        <p class="text-gray-600 mb-6">{{ Str::limit($news[0]->content, 280) }}</p>
                        <a href="{{ route('news.show', $news[0]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-[#171e60] hover:bg-[#0a5694]">
                            Read Full Article
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Articles -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-base font-semibold text-[#0a5694] uppercase tracking-wide">Recent</h2>
                <p class="mt-2 text-3xl  text-gray-900">Latest Articles</p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($news as $key => $item)
                    @if($key > 0) <!-- Skip the first item as it's already featured -->
                    <div class="bg-white overflow-hidden shadow-md rounded-xl border border-gray-100 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1">
                        @if($item->image_url)
                            <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" class="h-48 w-full object-cover">
                        @endif
                        <div class="p-6">
                            <div class="flex items-center mb-2">
                                <span class="text-sm text-gray-500">{{ formatDubaiDate($item->created_at, 'F d, Y') }}</span>
                                <!-- Assuming you might have a category field -->
                                @if(isset($item->category))
                                <span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">{{ $item->category }}</span>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $item->title }}</h3>
                            <p class="text-gray-600 mb-4">{{ Str::limit($item->content, 150) }}</p>
                            <a href="{{ route('news.show', $item) }}" class="text-[#0a5694] hover:text-[#171e60] font-medium flex items-center">
                                Read Article
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- Pagination - If you have pagination -->
            <div class="mt-12 flex justify-center">
                <!-- If you're using Laravel's pagination -->
                {{-- {{ $news->links() }} --}}
                
                <!-- Static pagination example -->
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" aria-current="page" class="z-10 bg-[#171e60] border-[#171e60] text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        1
                    </a>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        2
                    </a>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        3
                    </a>
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        8
                    </a>
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </nav>
            </div>
        </div>
    </div>
@else
    <!-- No News Available -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No news articles available</h3>
                <p class="mt-1 text-gray-500">We're working on bringing you the latest updates. Check back soon!</p>
            </div>
        </div>
    </div>
@endif


{{-- Footer is included in app.blade.php --}}
@endsection