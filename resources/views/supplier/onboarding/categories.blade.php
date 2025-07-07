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
                        Select the categories that best match your products and services. These are the specific product categories where you can list your items.
                    </p>

                    <!-- Category Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="category-search" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Search categories...">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" id="categories-grid">
                        @foreach($categories as $category)
                            <!-- Individual Category (Leaf Category) -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                               data-category-name="{{ $category->name }}"
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                               id="category-{{ $category->id }}">
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <label for="category-{{ $category->id }}" class="text-sm font-medium text-gray-900 cursor-pointer">{{ $category->name }}</label>
                                        @if($category->parent)
                                            <p class="text-xs text-gray-500 mt-1">Under: {{ $category->parent->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Selected categories summary -->
                    <div id="selected-categories-summary" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md text-blue-800 text-sm hidden"></div>
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
    const categoryCheckboxes = document.querySelectorAll('input[name="categories[]"]');
    const submitButton = form.querySelector('button[type="submit"]');
    const searchInput = document.getElementById('category-search');
    const categoriesGrid = document.getElementById('categories-grid');
    
    // Category search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const categoryCards = categoriesGrid.querySelectorAll('.border');
        
        categoryCards.forEach(card => {
            const categoryName = card.querySelector('label').textContent.toLowerCase();
            const parentName = card.querySelector('.text-xs.text-gray-500')?.textContent.toLowerCase() || '';
            
            if (categoryName.includes(searchTerm) || parentName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update selection count after filtering
        updateSelectionCount();
    });
    
    // Category selection feedback
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryCard = this.closest('.border');
            if (this.checked) {
                categoryCard.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
                categoryCard.classList.remove('hover:bg-gray-50');
            } else {
                categoryCard.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
                categoryCard.classList.add('hover:bg-gray-50');
            }
            updateSelectionCount();
        });
    });
    
    // Update selection count
    function updateSelectionCount() {
        const visibleCheckboxes = Array.from(categoryCheckboxes).filter(checkbox => {
            const card = checkbox.closest('.border');
            return card.style.display !== 'none';
        });
        
        const selectedCount = visibleCheckboxes.filter(checkbox => checkbox.checked).length;
        const totalVisibleCount = visibleCheckboxes.length;
        const totalCount = categoryCheckboxes.length;
        
        // Update submit button text
        if (selectedCount > 0) {
            submitButton.innerHTML = `Complete Registration (${selectedCount} categories selected)`;
        } else {
            submitButton.innerHTML = 'Complete Registration';
        }

        // Show selected categories summary
        const summaryElement = document.getElementById('selected-categories-summary');
        const selectedNames = Array.from(categoryCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.getAttribute('data-category-name'));
        if (selectedNames.length > 0) {
            summaryElement.textContent = `You have selected: ${selectedNames.join(', ')}`;
            summaryElement.classList.remove('hidden');
        } else {
            summaryElement.textContent = '';
            summaryElement.classList.add('hidden');
        }
    }
    
    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if at least one category is selected
        const selectedCategories = document.querySelectorAll('input[name="categories[]"]:checked');
        if (selectedCategories.length === 0) {
            showNotification('Please select at least one category to continue.', 'error');
            return;
        }
        
        // Handle specializations textarea
        const specializationLines = specializationsTextarea.value.split('\n').filter(line => line.trim() !== '');
        
        if (specializationLines.length === 0) {
            showNotification('Please enter at least one specialization.', 'error');
            specializationsTextarea.focus();
            return;
        }
        
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
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        
        // Submit the form
        form.submit();
    });
    
    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
            type === 'error' ? 'bg-red-500 text-white' : 
            type === 'success' ? 'bg-green-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span>${message}</span>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Initialize selection count
    updateSelectionCount();
    
    // Show success message if redirected with success
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    // Show error message if redirected with error
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
});
</script>
@endpush

@endsection 