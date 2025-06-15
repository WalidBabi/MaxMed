@extends('admin.layouts.app')

@section('title', 'View News Article')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">View News Article</h1>
                <p class="text-gray-600 mt-2">Article details and information</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.news.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to News
                </a>
                <a href="{{ route('admin.news.edit', $news) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Article
                </a>
            </div>
        </div>
    </div>

    <!-- Article Details -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Article Information</h3>
                <div class="flex items-center space-x-2">
                    @if($news->published)
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                            <svg class="-ml-0.5 mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Published
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">
                            <svg class="-ml-0.5 mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Draft
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Article Title -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <h2 class="text-2xl font-bold text-gray-900">{{ $news->title }}</h2>
            </div>

            <!-- Article Image -->
            @if($news->image)
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $news->image) }}" 
                         alt="{{ $news->title }}" 
                         class="h-64 w-full object-cover rounded-lg border border-gray-300">
                </div>
            </div>
            @endif

            <!-- Article Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                <div class="mt-2 prose max-w-none">
                    <div class="text-gray-900 leading-7 whitespace-pre-wrap">{{ $news->content }}</div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 pt-6 border-t border-gray-200">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Article ID</label>
                    <p class="mt-1 text-sm text-gray-900">#{{ $news->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $news->created_at->format('F j, Y \a\t g:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $news->updated_at->format('F j, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-8 flex items-center justify-end space-x-3">
        <a href="{{ route('news.show', $news) }}" target="_blank" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
            View on Site
        </a>
        <a href="{{ route('admin.news.edit', $news) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit Article
        </a>
        <form action="{{ route('admin.news.destroy', $news) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this news article?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                Delete Article
            </button>
        </form>
    </div>
@endsection 