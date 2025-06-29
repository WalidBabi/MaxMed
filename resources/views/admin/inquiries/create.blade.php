@extends('admin.layouts.app')

@section('title', 'Create Inquiry')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Inquiry</h1>
                    <p class="text-gray-600 mt-2">Create a new product inquiry for suppliers</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.inquiries.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 4.158a.75.75 0 11-1.04 1.04l-5.5-5.5a.75.75 0 010-1.08l5.5-5.5a.75.75 0 111.04 1.04L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            There were {{ count($errors) }} errors with your submission
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

        @if (session('success'))
            <div id="success-notification" class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div id="error-notification" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg">
            <form action="{{ route('admin.inquiries.store') }}" method="POST" class="divide-y divide-gray-200" id="inquiryForm">
                @csrf
                <div class="p-6 space-y-6">
                    <!-- Product Selection Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Type</label>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="product_type" value="listed" class="form-radio" checked
                                       onclick="toggleProductFields('listed')">
                                <span class="ml-2">Listed Product</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="product_type" value="unlisted" class="form-radio"
                                       onclick="toggleProductFields('unlisted')">
                                <span class="ml-2">Unlisted Product</span>
                            </label>
                        </div>
                        @error('product_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Listed Product Fields -->
                    <div id="listed_product_fields">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Product</label>
                        <select name="product_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unlisted Product Fields -->
                    <div id="unlisted_product_fields" class="hidden">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product Name</label>
                                <input type="text" name="product_name" value="{{ old('product_name') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('product_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product Description</label>
                                <textarea name="product_description" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('product_description') }}</textarea>
                                @error('product_description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product Category</label>
                                <select name="product_category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('product_category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_category')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Common Fields -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" name="quantity" value="{{ old('quantity') }}" min="1"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Requirements</label>
                            <textarea name="requirements" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Internal Notes</label>
                            <textarea name="internal_notes" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('internal_notes') }}</textarea>
                            @error('internal_notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Customer Reference (Optional)</label>
                            <input type="text" name="customer_reference" value="{{ old('customer_reference') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('customer_reference')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Supplier Targeting -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Send To Suppliers</label>
                        <div class="text-sm text-gray-600">
                            This inquiry will be sent to all suppliers with relevant product categories.
                        </div>
                        <input type="hidden" name="supplier_broadcast" value="all">
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <a href="{{ route('admin.inquiries.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Inquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleProductFields(type) {
    console.log('Toggling product fields to:', type);
    
    // Toggle visibility of fields
    if (type === 'listed') {
        document.getElementById('listed_product_fields').classList.remove('hidden');
        document.getElementById('unlisted_product_fields').classList.add('hidden');
        
        // Clear unlisted product fields
        document.querySelector('input[name="product_name"]').value = '';
        document.querySelector('textarea[name="product_description"]').value = '';
        document.querySelector('select[name="product_category"]').value = '';
    } else {
        document.getElementById('listed_product_fields').classList.add('hidden');
        document.getElementById('unlisted_product_fields').classList.remove('hidden');
        
        // Clear listed product field
        document.querySelector('select[name="product_id"]').value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('inquiryForm');
    const selectedType = document.querySelector('input[name="product_type"]:checked').value;
    console.log('Initial product type:', selectedType);
    toggleProductFields(selectedType);
    
    // Supplier broadcast is now always 'all'

    // Add form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form from submitting immediately
        
        const productType = document.querySelector('input[name="product_type"]:checked').value;
        console.log('Submitting form with product type:', productType);
        
        if (productType === 'unlisted') {
            const productName = document.querySelector('input[name="product_name"]').value.trim();
            const productDescription = document.querySelector('textarea[name="product_description"]').value.trim();
            const productCategory = document.querySelector('select[name="product_category"]').value;
            
            console.log('Unlisted product data:', {
                productName,
                productDescription,
                productCategory
            });
            
            if (!productName || !productDescription || !productCategory) {
                alert('Please fill in all required fields for unlisted product');
                return;
            }
        }
        
        // Log all form data before submission
        const formData = new FormData(form);
        console.log('Form data before submission:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        // Submit the form
        form.submit();
    });
});
</script>
@endpush 