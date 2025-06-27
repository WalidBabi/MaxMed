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
                        Select the categories that best match your products and services. This will help us connect you with relevant customers.
                    </p>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($categories as $category)
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                           id="category-{{ $category->id }}">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="category-{{ $category->id }}" class="font-medium text-gray-700">{{ $category->name }}</label>
                                    @if($category->children->count() > 0)
                                        <div class="mt-1 ml-6">
                                            @foreach($category->children as $child)
                                                <div class="flex items-start mt-2">
                                                    <div class="flex items-center h-5">
                                                        <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                               id="category-{{ $child->id }}">
                                                    </div>
                                                    <div class="ml-3">
                                                        <label for="category-{{ $child->id }}" class="text-sm text-gray-600">{{ $child->name }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
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
    // Handle specializations textarea
    const specializationsTextarea = document.getElementById('specializations');
    const form = specializationsTextarea.closest('form');
    
    // Add event listeners to parent category checkboxes
    document.querySelectorAll('input[type="checkbox"][name="categories[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Find the parent div that contains subcategories
            const parentDiv = this.closest('.relative');
            if (!parentDiv) return;
            
            // Find all subcategory checkboxes within this parent
            const subcategoryCheckboxes = parentDiv.querySelectorAll('.mt-1.ml-6 input[type="checkbox"]');
            
            // Set all subcategory checkboxes to match the parent's checked state
            subcategoryCheckboxes.forEach(subCheckbox => {
                subCheckbox.checked = this.checked;
            });
        });
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Convert textarea lines to array and remove empty lines
        const lines = specializationsTextarea.value.split('\n').filter(line => line.trim() !== '');
        
        // Remove existing hidden inputs
        form.querySelectorAll('input[name="specializations[]"]').forEach(input => input.remove());
        
        // Create hidden inputs for each line
        lines.forEach(line => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'specializations[]';
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