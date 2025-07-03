@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-12">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">✓</div>
                <span class="ml-2 text-green-500">Company Info</span>
            </div>
            <div class="flex-1 h-0.5 bg-green-500"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">✓</div>
                <span class="ml-2 text-green-500">Documents</span>
            </div>
            <div class="flex-1 h-0.5 bg-green-500"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                <span class="ml-2 font-medium text-green-500">Categories</span>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-5">Select Product Categories</h2>
            
            <form action="{{ route('supplier.onboarding.categories') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-6">
                    <p class="text-sm text-gray-500">
                        Select the categories that best match your products and services. Main categories with subcategories are shown as section headers, while standalone categories can be selected directly.
                    </p>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($categories as $category)
                            @if($category->children->count() > 0)
                                <!-- Main Category with Subcategories (Section Header) -->
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">{{ $category->name }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">Select specific subcategories below</p>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        @foreach($category->children as $child)
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                           id="category-{{ $child->id }}">
                                                </div>
                                                <div class="ml-3">
                                                    <label for="category-{{ $child->id }}" class="text-sm text-gray-700">{{ $child->name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <!-- Standalone Main Category (with checkbox) -->
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                   id="category-{{ $category->id }}">
                                        </div>
                                        <div class="ml-3">
                                            <label for="category-{{ $category->id }}" class="text-lg font-semibold text-gray-900">{{ $category->name }}</label>
                                            <p class="text-xs text-gray-500 mt-1">Standalone category</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @error('categories')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Specializations -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Specializations</label>
                        <p class="text-sm text-gray-500 mt-1">Enter your key specializations or areas of expertise (one per line)</p>
                        <div class="mt-2">
                            <textarea id="specializations" name="specializations" rows="5"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                      placeholder="e.g.&#10;Laboratory Equipment Calibration&#10;Medical Device Maintenance&#10;Analytical Chemistry Equipment&#10;Clinical Diagnostics"
                                      required></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Your specializations will help customers find your products and services more easily.</p>
                        @error('specializations')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Suggest Categories -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Suggest New Categories</label>
                        <p class="text-sm text-gray-500 mt-1">Don't see a category that fits your products? Suggest new categories here (one per line)</p>
                        <div class="mt-2">
                            <textarea id="suggested_categories" name="suggested_categories" rows="4"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                      placeholder="e.g.&#10;Advanced Molecular Diagnostics&#10;Bioinformatics Solutions&#10;Next-Generation Sequencing"></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">We'll review your suggestions and may add them to our category list for all suppliers.</p>
                        @error('suggested_categories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('supplier.onboarding.documents') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Complete Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle specializations and suggested categories textareas
    const specializationsTextarea = document.getElementById('specializations');
    const suggestedCategoriesTextarea = document.getElementById('suggested_categories');
    const form = specializationsTextarea.closest('form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Handle specializations textarea
        const specializationLines = specializationsTextarea.value.split('\n').filter(line => line.trim() !== '');
        
        // Remove existing hidden inputs for specializations
        form.querySelectorAll('input[name="specializations[]"]').forEach(input => input.remove());
        
        // Create hidden inputs for each specialization line
        specializationLines.forEach(line => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'specializations[]';
            input.value = line.trim();
            form.appendChild(input);
        });

        // Handle suggested categories textarea
        const suggestedCategoryLines = suggestedCategoriesTextarea.value.split('\n').filter(line => line.trim() !== '');
        
        // Remove existing hidden inputs for suggested categories
        form.querySelectorAll('input[name="suggested_categories[]"]').forEach(input => input.remove());
        
        // Create hidden inputs for each suggested category line
        suggestedCategoryLines.forEach(line => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'suggested_categories[]';
            input.value = line.trim();
            form.appendChild(input);
        });
        
        // Submit the form
        form.submit();
    });
});
</script>
@endpush

@endsection 