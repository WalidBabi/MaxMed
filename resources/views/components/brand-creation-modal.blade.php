<!-- Brand Creation Modal -->
<div id="brandCreationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Create New Brand</h3>
                <button type="button" id="closeBrandModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="brandCreationForm" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <!-- Brand Name -->
                    <div>
                        <label for="brand_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Brand Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="brand_name" name="name" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Enter brand name">
                        <div id="brand_name_error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Brand Description -->
                    <div>
                        <label for="brand_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea id="brand_description" name="description" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="Enter brand description (optional)"></textarea>
                    </div>

                    <!-- Brand Logo -->
                    <div>
                        <label for="brand_logo" class="block text-sm font-medium text-gray-700 mb-1">
                            Logo
                        </label>
                        <input type="file" id="brand_logo" name="logo" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/avif"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF, WEBP, AVIF up to 2MB</p>
                        <div id="brand_logo_error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="brand_sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                            Sort Order
                        </label>
                        <input type="number" id="brand_sort_order" name="sort_order" min="0"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="0" value="0">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <button type="button" id="cancelBrandCreation" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="submit" id="submitBrandCreation"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg id="brand_loading_spinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Create Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('brandCreationModal');
    const form = document.getElementById('brandCreationForm');
    const closeBtn = document.getElementById('closeBrandModal');
    const cancelBtn = document.getElementById('cancelBrandCreation');
    const submitBtn = document.getElementById('submitBrandCreation');
    const loadingSpinner = document.getElementById('brand_loading_spinner');
    const brandSelect = document.getElementById('brand_id'); // This will be set by the parent page

    // Open modal function (to be called from parent page)
    window.openBrandModal = function() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    // Close modal function
    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        form.reset();
        clearErrors();
    }

    // Clear error messages
    function clearErrors() {
        document.querySelectorAll('[id$="_error"]').forEach(error => {
            error.classList.add('hidden');
            error.textContent = '';
        });
    }

    // Show error message
    function showError(fieldId, message) {
        const errorElement = document.getElementById(fieldId + '_error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    // Close modal event listeners
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        clearErrors();
        
        // Show loading state
        submitBtn.disabled = true;
        loadingSpinner.classList.remove('hidden');
        
        // Create FormData
        const formData = new FormData(form);
        
        // Determine the correct endpoint based on the current route
        const isAdmin = window.location.pathname.includes('/admin/');
        const endpoint = isAdmin ? '/admin/brands/ajax' : '/admin/brands/ajax'; // Both use admin endpoint for now
        
        // Submit form via AJAX
        fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new brand to select dropdown
                if (brandSelect) {
                    const newOption = document.createElement('option');
                    newOption.value = data.brand.id;
                    newOption.textContent = data.brand.name;
                    newOption.selected = true;
                    brandSelect.appendChild(newOption);
                }
                
                // Close modal
                closeModal();
                
                // Show success message
                showNotification('Brand created successfully!', 'success');
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showError('brand_' + field, data.errors[field][0]);
                    });
                } else {
                    showError('brand_name', data.message || 'An error occurred while creating the brand.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('brand_name', 'An error occurred while creating the brand.');
        })
        .finally(() => {
            // Hide loading state
            submitBtn.disabled = false;
            loadingSpinner.classList.add('hidden');
        });
    });

    // Simple notification function (you can replace this with your preferred notification system)
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
            type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
