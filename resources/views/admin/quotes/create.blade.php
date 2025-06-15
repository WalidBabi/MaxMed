@extends('admin.layouts.app')

@section('title', 'Create Quote')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Quote</h1>
                <p class="text-gray-600 mt-2">Create a new quote for customers</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.quotes.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Quotes
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form id="quoteForm" action="{{ route('admin.quotes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Quote Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer <span class="text-red-500">*</span></label>
                                <select id="customer_id" name="customer_id" required 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('customer_id') border-red-300 @enderror">
                                    <option value="">Select a customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}{{ $customer->company_name ? ' - ' . $customer->company_name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quote_number" class="block text-sm font-medium text-gray-700 mb-2">Quote Number</label>
                                <input type="text" id="quote_number" 
                                       value="{{ \App\Models\Quote::generateQuoteNumber() }}" readonly
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                                <p class="mt-1 text-sm text-gray-500">Auto-generated</p>
                            </div>

                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                                <input type="text" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('reference_number') border-red-300 @enderror">
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quote_date" class="block text-sm font-medium text-gray-700 mb-2">Quote Date <span class="text-red-500">*</span></label>
                                <input type="date" id="quote_date" name="quote_date" 
                                       value="{{ old('quote_date', date('Y-m-d')) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('quote_date') border-red-300 @enderror">
                                @error('quote_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date <span class="text-red-500">*</span></label>
                                <input type="date" id="expiry_date" name="expiry_date" 
                                       value="{{ old('expiry_date', date('Y-m-d', strtotime('+30 days'))) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('expiry_date') border-red-300 @enderror">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="salesperson" class="block text-sm font-medium text-gray-700 mb-2">Salesperson</label>
                                <input type="text" id="salesperson" name="salesperson" 
                                       value="{{ old('salesperson') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('salesperson') border-red-300 @enderror">
                                @error('salesperson')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                <input type="text" id="subject" name="subject" 
                                       value="{{ old('subject') }}"
                                       placeholder="Let your customer know what this Quote is for"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('subject') border-red-300 @enderror">
                                @error('subject')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v1a2 2 0 002 2h2m0-4v4m0-4a2 2 0 012-2h1a2 2 0 012 2v1a2 2 0 01-2 2h-1m-2-4v4"></path>
                            </svg>
                            Item Table
                        </h3>
                        <button type="button" id="addItem" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Add Item
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 items-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">Drag</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Details</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Quantity</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Rate</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Discount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Amount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Items will be added dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <div class="w-full max-w-sm">
                                <div class="rounded-lg bg-gray-50 p-4">
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">Sub Total:</span>
                                        <span id="subTotal" class="text-sm font-semibold text-gray-900">0.00</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2">
                                        <div class="flex justify-between">
                                            <span class="text-base font-semibold text-gray-900">Total (AED):</span>
                                            <span id="totalAmount" class="text-base font-bold text-indigo-600">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Notes -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2z"></path>
                            </svg>
                            Customer Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <textarea id="customer_notes" name="customer_notes" rows="3"
                                  placeholder="Looking forward for your business."
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('customer_notes') border-red-300 @enderror">{{ old('customer_notes', 'Looking forward for your business.') }}</textarea>
                        @error('customer_notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Terms & Conditions
                        </h3>
                    </div>
                    <div class="p-6">
                        <textarea id="terms_conditions" name="terms_conditions" rows="4"
                                  placeholder="Enter the terms and conditions of your business to be displayed in your transaction"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('terms_conditions') border-red-300 @enderror">{{ old('terms_conditions') }}</textarea>
                        @error('terms_conditions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Attachments -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Attach File(s) to Quote
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-300 transition-colors">
                            <input type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden">
                            <label for="attachments" class="cursor-pointer block">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-900">Click to upload files or drag and drop</p>
                                <p class="mt-1 text-xs text-gray-500">You can upload a maximum of 5 files, 10MB each</p>
                            </label>
                        </div>
                        <div id="fileList" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Status and Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Actions
                        </h3>
                    </div>
                    <div class="p-6">
                        <input type="hidden" name="status" value="draft">
                        
                        <div class="space-y-3">
                            <button type="submit" onclick="return validateForm()" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                </svg>
                                Save Quote
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            Quick Tips
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-gray-600 space-y-2">
                            <p><span class="font-medium text-gray-900">Customer:</span> Select from existing customers list</p>
                            <p><span class="font-medium text-gray-900">Quote Date:</span> Defaults to today's date</p>
                            <p><span class="font-medium text-gray-900">Expiry Date:</span> Defaults to 30 days from quote date</p>
                            <p><span class="font-medium text-gray-900">Items:</span> Add at least one item to create the quote</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
<style>
    /* Select2 Custom Styles */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(1.5em + 0.5rem + 2px);
        font-size: 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .select2-container--bootstrap-5 .select2-selection:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
    }
    
    /* Fixed table layout for consistent column widths */
    .items-table {
        table-layout: fixed;
        width: 100%;
    }
    
    .items-table th,
    .items-table td {
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .items-table input,
    .items-table select {
        min-width: 100px;
    }
</style>
@endpush

@push('scripts')
<script>
let itemCounter = 0;

function addItem() {
    const tbody = document.getElementById('itemsTable');
    const row = document.createElement('tr');
    row.className = 'item-row bg-white hover:bg-gray-50';
    row.draggable = true;
    row.innerHTML = `
        <td class="px-3 py-4 text-center">
            <svg class="w-4 h-4 text-gray-400 cursor-pointer drag-handle mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
        </td>
        <td class="px-3 py-4">
            <select name="items[${itemCounter}][product_id]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 product-select" required>
                <option value="">Select a product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                            data-name="{{ $product->name }}"
                            data-description="{{ $product->description }}"
                            data-price="{{ $product->price_aed ?? $product->price }}">
                        {{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="items[${itemCounter}][item_details]" class="item-details-hidden">
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemCounter}][quantity]" value="1.00" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 quantity-input">
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemCounter}][rate]" value="0.00" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rate-input">
        </td>
        <td class="px-3 py-4">
            <div class="flex">
                <input type="number" step="0.01" name="items[${itemCounter}][discount]" value="0" min="0" max="100"
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 discount-input">
                <span class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">%</span>
            </div>
        </td>
        <td class="px-3 py-4 text-right">
            <span class="amount-display font-medium text-gray-900">0.00</span>
        </td>
        <td class="px-3 py-4 text-center">
            <button type="button" onclick="removeItem(this)" class="inline-flex items-center p-1 border border-transparent rounded-full text-red-600 hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    itemCounter++;
    
    // Add event listeners for calculation
    const quantityInput = row.querySelector('.quantity-input');
    const rateInput = row.querySelector('.rate-input');
    const discountInput = row.querySelector('.discount-input');
    const productSelect = row.querySelector('.product-select');
    const itemDetailsHidden = row.querySelector('.item-details-hidden');
    
    [quantityInput, rateInput, discountInput].forEach(input => {
        input.addEventListener('input', calculateRowAmount);
    });
    
    // Add event listener for product selection
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const productName = selectedOption.getAttribute('data-name');
            const productPrice = selectedOption.getAttribute('data-price');
            
            // Set the item details (name only)
            itemDetailsHidden.value = productName;
            
            // Set the rate
            rateInput.value = productPrice || 0;
            
            // Trigger calculation
            calculateRowAmount({ target: rateInput });
        } else {
            itemDetailsHidden.value = '';
            rateInput.value = 0;
            calculateRowAmount({ target: rateInput });
        }
    });
    
    calculateTotals();
}

function removeItem(button) {
    button.closest('tr').remove();
    calculateTotals();
}

function calculateRowAmount(event) {
    const row = event.target.closest('tr');
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
    
    const subtotal = quantity * rate;
    const discountAmount = (subtotal * discount) / 100;
    const amount = subtotal - discountAmount;
    
    row.querySelector('.amount-display').textContent = amount.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    const amounts = document.querySelectorAll('.amount-display');
    let total = 0;
    
    amounts.forEach(amount => {
        total += parseFloat(amount.textContent) || 0;
    });
    
    document.getElementById('subTotal').textContent = total.toFixed(2);
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

function validateForm() {
    // Validate that we have at least one item
    const itemRows = document.querySelectorAll('#itemsTable tr');
    if (itemRows.length === 0) {
        alert('Please add at least one item before saving the quote.');
        return false;
    }
    
    // Check if required fields are filled
    const customerSelect = document.getElementById('customer_id');
    if (!customerSelect.value) {
        alert('Please select a customer before saving the quote.');
        customerSelect.focus();
        return false;
    }
    
    // Validate that all item rows have required data
    let hasEmptyItems = false;
    itemRows.forEach(row => {
        const productSelect = row.querySelector('select[name*="[product_id]"]');
        const quantity = row.querySelector('input[name*="[quantity]"]');
        const rate = row.querySelector('input[name*="[rate]"]');
        
        if (!productSelect || !productSelect.value) {
            hasEmptyItems = true;
        }
        if (!quantity || parseFloat(quantity.value) <= 0) {
            hasEmptyItems = true;
        }
        if (!rate || parseFloat(rate.value) < 0) {
            hasEmptyItems = true;
        }
    });
    
    if (hasEmptyItems) {
        alert('Please select products and fill in all quantities and rates.');
        return false;
    }
    
    // If validation passes, show loading state
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (submitButton && !submitButton.disabled) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<svg class="-ml-0.5 mr-1.5 h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';
    }
    
    return true;
}

// Initialize with one item
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addItem').addEventListener('click', addItem);
    addItem(); // Add initial item
    
    // File upload handling
    const fileInput = document.getElementById('attachments');
    const fileList = document.getElementById('fileList');
    
    fileInput.addEventListener('change', function() {
        fileList.innerHTML = '';
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            const fileDiv = document.createElement('div');
            fileDiv.className = 'flex justify-between items-center bg-gray-50 p-3 rounded-md mt-2';
            fileDiv.innerHTML = `
                <span class="text-sm text-gray-700">${file.name}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
            `;
            fileList.appendChild(fileDiv);
        }
    });
});
</script>
@endpush
@endsection 