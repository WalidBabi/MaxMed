@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-12">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                <span class="ml-2 font-medium text-green-500">Company Info</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold">2</div>
                <span class="ml-2 text-gray-600">Documents</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold">3</div>
                <span class="ml-2 text-gray-600">Categories</span>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-5">Company Information</h2>
            
            <form action="{{ route('supplier.onboarding.company') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- General Validation Errors -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There were some errors with your submission:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <!-- Company Name -->
                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" name="company_name" id="company_name" 
                               value="{{ old('company_name', $supplierInfo->company_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Brand Name -->
                    <div class="sm:col-span-2">
                        <label for="brand_name" class="block text-sm font-medium text-gray-700">Brand Name</label>
                        <input type="text" name="brand_name" id="brand_name"
                               value="{{ old('brand_name', $supplierInfo->brand ? $supplierInfo->brand->name : '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="text-sm text-gray-500 mt-1">This will be shown as your product brand. Leave blank to use your company name.</p>
                        @error('brand_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Brand Logo -->
                    <div class="sm:col-span-2">
                        <label for="brand_logo" class="block text-sm font-medium text-gray-700">Brand Logo</label>
                        <div class="flex items-center space-x-4">
                            @if($supplierInfo->brand && $supplierInfo->brand->logo_url)
                                <img src="{{ asset($supplierInfo->brand->logo_url) }}" alt="Brand Logo" class="h-16 w-16 object-contain bg-white border rounded">
                            @endif
                            <input type="file" name="brand_logo" id="brand_logo" accept="image/*" class="block">
                        </div>
                        <p class="text-sm text-gray-500 mt-1">JPG, PNG, GIF, or WEBP. Max size 2MB.</p>
                        @error('brand_logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Registration Numbers -->
                    <div>
                        <label for="business_registration_number" class="block text-sm font-medium text-gray-700">Business Registration Number</label>
                        <input type="text" name="business_registration_number" id="business_registration_number"
                               value="{{ old('business_registration_number', $supplierInfo->business_registration_number ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('business_registration_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_registration_number" class="block text-sm font-medium text-gray-700">Tax Registration Number (Optional)</label>
                        <input type="text" name="tax_registration_number" id="tax_registration_number"
                               value="{{ old('tax_registration_number', $supplierInfo->tax_registration_number ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('tax_registration_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="trade_license_number" class="block text-sm font-medium text-gray-700">Trade License Number</label>
                        <input type="text" name="trade_license_number" id="trade_license_number"
                               value="{{ old('trade_license_number', $supplierInfo->trade_license_number ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('trade_license_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Address -->
                    <div class="sm:col-span-2">
                        <label for="business_address" class="block text-sm font-medium text-gray-700">Business Address</label>
                        <textarea name="business_address" id="business_address" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  required>{{ old('business_address', $supplierInfo->business_address ?? '') }}</textarea>
                        @error('business_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" id="city"
                               value="{{ old('city', $supplierInfo->city ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- State/Province -->
                    <div>
                        <label for="state_province" class="block text-sm font-medium text-gray-700">State/Province</label>
                        <input type="text" name="state_province" id="state_province"
                               value="{{ old('state_province', $supplierInfo->state_province ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('state_province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code"
                               value="{{ old('postal_code', $supplierInfo->postal_code ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input type="text" name="country" id="country"
                               value="{{ old('country', $supplierInfo->country ?? 'UAE') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone_primary" class="block text-sm font-medium text-gray-700">Primary Phone</label>
                        <input type="text" name="phone_primary" id="phone_primary"
                               value="{{ old('phone_primary', $supplierInfo->phone_primary ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('phone_primary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input type="text" name="website" id="website"
                               value="{{ old('website', $supplierInfo->website ?? '') }}"
                               placeholder="e.g., www.yourcompany.com or yourcompany.com"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Years in Business -->
                    <div>
                        <label for="years_in_business" class="block text-sm font-medium text-gray-700">Years in Business</label>
                        <input type="number" name="years_in_business" id="years_in_business"
                               value="{{ old('years_in_business', $supplierInfo->years_in_business ?? '') }}"
                               min="0" step="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('years_in_business')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Description -->
                    <div class="sm:col-span-2">
                        <label for="company_description" class="block text-sm font-medium text-gray-700">Company Description</label>
                        <textarea name="company_description" id="company_description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  required>{{ old('company_description', $supplierInfo->company_description ?? '') }}</textarea>
                        @error('company_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Primary Contact Information</h3>
                    </div>

                    <!-- Primary Contact Name -->
                    <div>
                        <label for="primary_contact_name" class="block text-sm font-medium text-gray-700">Contact Name</label>
                        <input type="text" name="primary_contact_name" id="primary_contact_name"
                               value="{{ old('primary_contact_name', $supplierInfo->primary_contact_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('primary_contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primary Contact Position -->
                    <div>
                        <label for="primary_contact_position" class="block text-sm font-medium text-gray-700">Position</label>
                        <input type="text" name="primary_contact_position" id="primary_contact_position"
                               value="{{ old('primary_contact_position', $supplierInfo->primary_contact_position ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('primary_contact_position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primary Contact Email -->
                    <div>
                        <label for="primary_contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="primary_contact_email" id="primary_contact_email"
                               value="{{ old('primary_contact_email', $supplierInfo->primary_contact_email ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('primary_contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primary Contact Phone -->
                    <div>
                        <label for="primary_contact_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="primary_contact_phone" id="primary_contact_phone"
                               value="{{ old('primary_contact_phone', $supplierInfo->primary_contact_phone ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('primary_contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Continue to Documents
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Form validation and feedback
    form.addEventListener('submit', function(e) {
        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        
        // Basic client-side validation
        const requiredFields = form.querySelectorAll('[required]');
        let hasErrors = false;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                hasErrors = true;
                field.classList.add('border-red-500');
                
                // Add error message if not already present
                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('text-red-600')) {
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'mt-1 text-sm text-red-600';
                    errorDiv.textContent = 'This field is required.';
                    field.parentNode.appendChild(errorDiv);
                }
            } else {
                field.classList.remove('border-red-500');
                // Remove error message if field is now valid
                const errorDiv = field.parentNode.querySelector('.text-red-600');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });
        
        // Email validation
        const emailField = document.getElementById('primary_contact_email');
        if (emailField && emailField.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                hasErrors = true;
                emailField.classList.add('border-red-500');
                
                if (!emailField.nextElementSibling || !emailField.nextElementSibling.classList.contains('text-red-600')) {
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'mt-1 text-sm text-red-600';
                    errorDiv.textContent = 'Please enter a valid email address.';
                    emailField.parentNode.appendChild(errorDiv);
                }
            }
        }
        
        // Website validation
        const websiteField = document.getElementById('website');
        if (websiteField && websiteField.value) {
            const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
            if (!urlRegex.test(websiteField.value)) {
                hasErrors = true;
                websiteField.classList.add('border-red-500');
                
                if (!websiteField.nextElementSibling || !websiteField.nextElementSibling.classList.contains('text-red-600')) {
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'mt-1 text-sm text-red-600';
                    errorDiv.textContent = 'Please enter a valid website URL.';
                    websiteField.parentNode.appendChild(errorDiv);
                }
            }
        }
        
        if (hasErrors) {
            e.preventDefault();
            submitButton.disabled = false;
            submitButton.innerHTML = 'Continue to Documents';
            
            // Scroll to first error
            const firstError = form.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Show error message
            showNotification('Please correct the errors above and try again.', 'error');
        }
    });
    
    // Real-time validation feedback
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            // Remove error styling on input
            this.classList.remove('border-red-500');
            const errorDiv = this.parentNode.querySelector('.text-red-600');
            if (errorDiv) {
                errorDiv.remove();
            }
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            field.classList.add('border-red-500');
            if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('text-red-600')) {
                const errorDiv = document.createElement('p');
                errorDiv.className = 'mt-1 text-sm text-red-600';
                errorDiv.textContent = 'This field is required.';
                field.parentNode.appendChild(errorDiv);
            }
            return false;
        }
        
        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                field.classList.add('border-red-500');
                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('text-red-600')) {
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'mt-1 text-sm text-red-600';
                    errorDiv.textContent = 'Please enter a valid email address.';
                    field.parentNode.appendChild(errorDiv);
                }
                return false;
            }
        }
        
        // Website validation
        if (field.id === 'website' && value) {
            const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
            if (!urlRegex.test(value)) {
                field.classList.add('border-red-500');
                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('text-red-600')) {
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'mt-1 text-sm text-red-600';
                    errorDiv.textContent = 'Please enter a valid website URL.';
                    field.parentNode.appendChild(errorDiv);
                }
                return false;
            }
        }
        
        // Years in business validation
        if (field.id === 'years_in_business' && value) {
            const years = parseInt(value);
            if (isNaN(years) || years < 0) {
                field.classList.add('border-red-500');
                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('text-red-600')) {
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'mt-1 text-sm text-red-600';
                    errorDiv.textContent = 'Please enter a valid number of years.';
                    field.parentNode.appendChild(errorDiv);
                }
                return false;
            }
        }
        
        // Remove error styling if field is valid
        field.classList.remove('border-red-500');
        const errorDiv = field.parentNode.querySelector('.text-red-600');
        if (errorDiv) {
            errorDiv.remove();
        }
        
        return true;
    }
    
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