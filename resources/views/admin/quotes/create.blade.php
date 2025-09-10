@extends('admin.layouts.app')

@section('title', 'Create Quote')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
    <!-- Full Width Container -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8">
        <!-- Header -->
        <div class="mb-8 px-4 sm:px-6 lg:px-8">
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
            <div class="px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
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
            </div>
        @endif

        <form id="quoteForm" action="{{ route('admin.quotes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 px-4 sm:px-6 lg:px-8">
                <div class="lg:col-span-3 space-y-8">
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
                                    <label for="customer_search" class="block text-sm font-medium text-gray-700 mb-2">Customer <span class="text-red-500">*</span></label>
                                    <div class="relative product-dropdown-container">
                                        <input type="text"
                                               id="customer_search"
                                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 product-search-input"
                                               placeholder="Search customers..."
                                               autocomplete="off"
                                               value="{{ optional($customers->firstWhere('id', old('customer_id')))->name ? (optional($customers->firstWhere('id', old('customer_id')))->name . (optional($customers->firstWhere('id', old('customer_id')))->company_name ? ' - ' . optional($customers->firstWhere('id', old('customer_id')))->company_name : '')) : '' }}">
                                        <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                                        <div class="product-dropdown-list hidden" id="customer_dropdown_list">
                                            <div class="dropdown-items">
                                                @foreach($customers as $customer)
                                                    <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
                                                         data-id="{{ $customer->id }}"
                                                         data-name="{{ $customer->name }}"
                                                         data-company="{{ $customer->company_name }}"
                                                         data-search-text="{{ strtolower(trim($customer->name . ' ' . ($customer->company_name ?? ''))) }}">
                                                        <div class="font-medium text-gray-900">{{ $customer->name }}{{ $customer->company_name ? ' - ' . $customer->company_name : '' }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="p-3 text-sm text-gray-500 text-center dropdown-no-results hidden">No customers found</div>
                                        </div>
                                    </div>
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

                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency Rate <span class="text-red-500">*</span></label>
                                    <select id="currency" name="currency" required 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('currency') border-red-300 @enderror">
                                        <option value="AED" {{ old('currency', 'AED') == 'AED' ? 'selected' : '' }}>AED (Dirham)</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                                    </select>
                                    @error('currency')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="shipping_rate" class="block text-sm font-medium text-gray-700 mb-2">Shipping Rate</label>
                                    <input type="number" id="shipping_rate" name="shipping_rate" step="0.01" min="0"
                                           value="{{ old('shipping_rate', 0) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('shipping_rate') border-red-300 @enderror"
                                           onchange="updateTotals()">
                                    @error('shipping_rate')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                    <select id="payment_terms" name="payment_terms"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('payment_terms') border-red-300 @enderror">
                                        <option value="">Select Payment Terms</option>
                                        <option value="advance_50" {{ old('payment_terms') == 'advance_50' ? 'selected' : '' }}>50% Advance</option>
                                        <option value="advance_100" {{ old('payment_terms') == 'advance_100' ? 'selected' : '' }}>100% Advance</option>
                                        <option value="on_delivery" {{ old('payment_terms') == 'on_delivery' ? 'selected' : '' }}>Cash On Delivery</option>
                                        <option value="net_30" {{ old('payment_terms') == 'net_30' ? 'selected' : '' }}>Net 30</option>
                                        <option value="custom" {{ old('payment_terms') == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('payment_terms')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="vat_rate" class="block text-sm font-medium text-gray-700 mb-2">VAT Rate (%)</label>
                                    <input type="number" id="vat_rate" name="vat_rate" step="0.01" min="0" max="100"
                                           value="{{ old('vat_rate', 5) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('vat_rate') border-red-300 @enderror"
                                           onchange="updateTotals()" 
                                           oninput="this.setAttribute('data-manual-override', 'true')">
                                    @error('vat_rate')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="customs_clearance_fee" class="block text-sm font-medium text-gray-700 mb-2">Customs Clearance Fee</label>
                                    <input type="number" id="customs_clearance_fee" name="customs_clearance_fee" step="0.01" min="0"
                                           value="{{ old('customs_clearance_fee', 0) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('customs_clearance_fee') border-red-300 @enderror"
                                           onchange="updateTotals()" 
                                           oninput="this.setAttribute('data-manual-override', 'true')"
                                           placeholder="Auto-calculated at 10% of procurement cost">
                                    @error('customs_clearance_fee')
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

            <!-- Item Table - Full Width -->
            <div class="w-full px-4 sm:px-6 lg:px-8 mb-8">
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
                        <div class="overflow-x-auto w-full">
                            <table class="w-full divide-y divide-gray-200 items-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 60px;">Drag</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 300px;">Item Details</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">Specifications</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Quantity</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Rate</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Discount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Amount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 80px;">Action</th>
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
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">Shipping:</span>
                                        <span id="shippingAmount" class="text-sm font-semibold text-gray-900">0.00</span>
                                    </div>
                                    <div class="flex justify-between py-2" id="customsRow" style="display: none;">
                                        <span class="text-sm font-medium text-gray-700">Customs Clearance:</span>
                                        <span id="customsAmount" class="text-sm font-semibold text-gray-900">0.00</span>
                                    </div>
                                    <div class="flex justify-between py-2" id="vatRow" style="display: none;">
                                        <span class="text-sm font-medium text-gray-700">VAT (<span id="vatRateDisplay">0</span>%):</span>
                                        <span id="vatAmount" class="text-sm font-semibold text-gray-900">0.00</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2">
                                        <div class="flex justify-between">
                                            <span class="text-base font-semibold text-gray-900">Total (<span id="totalCurrency">AED</span>):</span>
                                            <span id="totalAmount" class="text-base font-bold text-indigo-600">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Sections - Back to Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 px-4 sm:px-6 lg:px-8">
                <div class="lg:col-span-3 space-y-8">
                    <!-- Customer Notes -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                                      placeholder="These products require 2-4 weeks to supply and ship to your company directly."
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('terms_conditions') border-red-300 @enderror">{{ old('terms_conditions','These products require 2-4 weeks to supply and ship to your company directly.') }}</textarea>
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
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 upload-area" id="uploadArea">
                                <input type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.bmp,.tiff,.txt,.xlsx,.xls" class="hidden">
                                <label for="attachments" class="cursor-pointer block">
                                    <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    </div>
                                    <p class="text-lg font-medium text-gray-900 mb-2">Upload Files</p>
                                    <p class="text-sm text-gray-600 mb-1">Click to browse or drag and drop files here</p>
                                    <p class="text-xs text-gray-500">Supports: PDF, DOC, DOCX, JPG, PNG, GIF, XLS, XLSX, TXT (Max 10MB each)</p>
                                </label>
                            </div>
                            
                            <!-- File Preview List -->
                            <div id="fileList" class="mt-4 space-y-3"></div>
                            
                            <!-- Upload Progress -->
                            <div id="uploadProgress" class="mt-4 hidden">
                                <div class="bg-gray-200 rounded-full h-2">
                                    <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <p id="progressText" class="text-sm text-gray-600 mt-2">Uploading files...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
    
    /* Table Layout Styles */
    .items-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .items-table th,
    .items-table td {
        padding: 12px 8px;
        vertical-align: top;
        border-bottom: 1px solid #e5e7eb;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .items-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #6b7280;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .items-table td {
        background-color: #ffffff;
    }
    
    .items-table tr:hover td {
        background-color: #f9fafb;
    }
    
    /* Column Widths */
    .items-table th:nth-child(1),
    .items-table td:nth-child(1) { width: 60px; } /* Drag */
    
    .items-table th:nth-child(2),
    .items-table td:nth-child(2) { width: 300px; } /* Item Details */
    
    .items-table th:nth-child(3),
    .items-table td:nth-child(3) { width: 200px; } /* Specifications */
    
    .items-table th:nth-child(4),
    .items-table td:nth-child(4) { width: 120px; } /* Quantity */
    
    .items-table th:nth-child(5),
    .items-table td:nth-child(5) { width: 120px; } /* Rate */
    
    .items-table th:nth-child(6),
    .items-table td:nth-child(6) { width: 120px; } /* Discount */
    
    .items-table th:nth-child(7),
    .items-table td:nth-child(7) { width: 120px; } /* Amount */
    
    .items-table th:nth-child(8),
    .items-table td:nth-child(8) { width: 80px; } /* Action */
    
    /* Input Styles */
    .items-table input,
    .items-table select {
     
        min-width: 0;
        box-sizing: border-box;
    }
    
    /* Custom Dropdown Styles */
    .product-dropdown-container {
        position: relative;
        width: 100%;
    }
    
    .product-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 300px;
        overflow-y: auto;
        background: white;
        z-index: 999999;
        margin-top: 4px;
        min-width: 280px;
    }
    
    /* Specifications Dropdown Styles */
    .specifications-dropdown-container {
        position: relative;
        width: 100%;
    }
    
    .specifications-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 200px;
        overflow-y: auto;
        background: white;
        z-index: 999999;
        margin-top: 4px;
        min-width: 180px;
    }
    
    /* Specifications Checkbox Styles */
    .specifications-dropdown-list .spec-checkbox,
    .specifications-dropdown-list .select-all-checkbox {
        accent-color: #6366f1;
    }
    
    .specifications-dropdown-list .spec-checkbox:checked,
    .specifications-dropdown-list .select-all-checkbox:checked {
        background-color: #6366f1;
        border-color: #6366f1;
    }
    
    .specifications-dropdown-list .spec-checkbox:focus,
    .specifications-dropdown-list .select-all-checkbox:focus {
        ring: 2px;
        ring-color: #6366f1;
        ring-offset: 2px;
    }
    
    /* Select All section styling */
    .specifications-dropdown-list .bg-indigo-50 {
        background-color: #eef2ff;
    }
    
    .specifications-dropdown-list .bg-indigo-50:hover {
        background-color: #e0e7ff;
    }
    
    /* Ensure table cells don't interfere with dropdown positioning */
    .items-table td {
        position: relative;
        z-index: 1;
    }
    
    .items-table .product-dropdown-container {
        z-index: 10;
    }
    
    .items-table .product-dropdown-list {
        z-index: 999999;
    }
    
    .items-table .specifications-dropdown-container {
        z-index: 10;
    }
    
    .items-table .specifications-dropdown-list {
        z-index: 999999;
    }
    
    /* Force dropdown to appear above everything */
    .product-dropdown-list:not(.hidden) {
        position: absolute !important;
        z-index: 999999 !important;
    }
    
    .specifications-dropdown-list:not(.hidden) {
        position: absolute !important;
        z-index: 999999 !important;
    }
    
    .dropdown-item {
        transition: background-color 0.15s ease-in-out;
        cursor: pointer;
        padding: 8px 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .dropdown-item:hover {
        background-color: #f8fafc;
    }
    
    .dropdown-item.bg-indigo-50 {
        background-color: #eef2ff;
    }
    
    .product-search-input {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
    
    .hidden {
        display: none;
    }
    
    /* Table Container Styles */
    .overflow-x-auto {
        overflow-x: auto;
        overflow-y: visible;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background: white;
    }
    
    /* Additional fixes for table overflow */
    .overflow-x-auto {
        overflow: visible;
    }
    
    /* Ensure the table container doesn't clip the dropdown */
    .items-table {
        overflow: visible;
    }
    
    .items-table tbody {
        overflow: visible;
    }
    
    .items-table tr {
        overflow: visible;
    }
    
    /* Scrollbar Styles */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Amount Display */
    .amount-display {
        font-weight: 600;
        color: #374151;
    }
    
    /* Action Button */
    .items-table .action-button {
        padding: 4px;
        border-radius: 50%;
        transition: all 0.2s ease-in-out;
    }
    
    .items-table .action-button:hover {
        background-color: #fef2f2;
        transform: scale(1.1);
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
            <div class="relative product-dropdown-container">
                <input type="text" 
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 product-search-input" 
                       placeholder="Search products..." 
                       autocomplete="off">
                <input type="hidden" name="items[${itemCounter}][product_id]" class="product-id-input">
                <input type="hidden" name="items[${itemCounter}][item_details]" class="item-details-hidden">
                
                <!-- Dropdown List -->
                <div class="product-dropdown-list hidden">
                    <div class="p-2 text-sm text-gray-500 dropdown-loading hidden">Searching...</div>
                    <div class="dropdown-items">
                        @foreach($products as $product)
                            <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" 
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-description="{{ $product->description }}"
                                 data-price="{{ $product->price_aed ?? $product->price }}"
                                 data-price-aed="{{ $product->price_aed ?? $product->price }}"
                                 data-price-usd="{{ $product->price ?? 0 }}"
                                 data-procurement-price-aed="{{ $product->procurement_price_aed ?? $product->price_aed ?? $product->price }}"
                                 data-procurement-price-usd="{{ $product->procurement_price_usd ?? $product->price ?? 0 }}"
                                 data-specifications="{{ $product->specifications ? json_encode($product->specifications->map(function($spec) { return $spec->display_name . ': ' . $spec->formatted_value; })->toArray()) : '[]' }}"
                                 data-has-size-options="{{ $product->has_size_options ? 'true' : 'false' }}"
                                 data-size-options="{{ is_array($product->size_options) ? json_encode($product->size_options) : ($product->size_options ?: '[]') }}"
                                 data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                @if($product->description)
                                    <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                @endif
                                @if($product->price_aed ?? $product->price)
                                    <div class="text-indigo-600 text-sm font-medium mt-1">
                                        <span class="price-display-aed">AED {{ number_format($product->price_aed ?? $product->price, 2) }}</span>
                                        <span class="price-display-usd" style="display: none;">USD {{ number_format($product->price ?? 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3 text-sm text-gray-500 text-center dropdown-no-results hidden">No products found</div>
                </div>
            </div>
        </td>
        <td class="px-3 py-4">
            <div class="relative specifications-dropdown-container">
                <input type="text" 
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 specifications-search-input" 
                       placeholder="Select specifications..." 
                       autocomplete="off"
                       readonly>
                <input type="hidden" name="items[${itemCounter}][specifications]" class="specifications-hidden">
                
                <!-- Size Options Dropdown -->
                <div class="mt-2">
                    <select name="items[${itemCounter}][size]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 size-options-select">
                        <option value="">Select Size (if applicable)</option>
                    </select>
                </div>
                
                <!-- Specifications Dropdown List -->
                <div class="specifications-dropdown-list hidden">
                    <div class="p-2 text-sm text-gray-500">No specifications available</div>
                </div>
            </div>
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
            <button type="button" onclick="removeItem(this)" class="inline-flex items-center p-1 border border-transparent rounded-full text-red-600 hover:bg-red-50 action-button">
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
    const productSearchInput = row.querySelector('.product-search-input');
    const productIdInput = row.querySelector('.product-id-input');
    const itemDetailsHidden = row.querySelector('.item-details-hidden');
    const dropdownList = row.querySelector('.product-dropdown-list');
    const dropdownItems = row.querySelector('.dropdown-items');
    const dropdownNoResults = row.querySelector('.dropdown-no-results');
    const specificationsInput = row.querySelector('.specifications-search-input');
    const specificationsHidden = row.querySelector('.specifications-hidden');
    const specificationsDropdown = row.querySelector('.specifications-dropdown-list');
    
    [quantityInput, rateInput, discountInput].forEach(input => {
        input.addEventListener('input', calculateRowAmount);
    });
    
    // Initialize custom dropdown functionality
    initializeCustomDropdown(productSearchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput, specificationsInput, specificationsHidden, specificationsDropdown);
    
    // Add specifications dropdown functionality
    specificationsInput.addEventListener('click', function() {
        if (specificationsHidden.value && specificationsHidden.value !== '[]') {
            specificationsDropdown.classList.toggle('hidden');
        }
    });
    
    // Hide specifications dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!specificationsInput.contains(e.target) && !specificationsDropdown.contains(e.target)) {
            specificationsDropdown.classList.add('hidden');
        }
    });
    
    // Prevent dropdown from closing when clicking on checkboxes
    specificationsDropdown.addEventListener('click', function(e) {
        if (e.target.type === 'checkbox' || e.target.tagName === 'LABEL') {
            e.stopPropagation();
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
    let subTotal = 0;
    let totalProcurementCost = 0;
    
    amounts.forEach(amount => {
        subTotal += parseFloat(amount.textContent) || 0;
    });
    
    // Calculate total procurement cost for customs calculation
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input')?.value) || 0;
        const procurementPriceElement = row.querySelector('[data-procurement-price]');
        const procurementPrice = parseFloat(procurementPriceElement?.getAttribute('data-procurement-price')) || 0;
        totalProcurementCost += quantity * procurementPrice;
    });
    
    const shippingRate = parseFloat(document.getElementById('shipping_rate').value) || 0;
    let customsFee = parseFloat(document.getElementById('customs_clearance_fee').value) || 0;
    let vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;
    
    // Auto-calculate customs as 10% of procurement cost if not manually set
    if (!document.getElementById('customs_clearance_fee').getAttribute('data-manual-override')) {
        customsFee = totalProcurementCost * 0.10;
        document.getElementById('customs_clearance_fee').value = customsFee.toFixed(2);
    }
    
    // Auto-set VAT rate to 5% if not manually set
    if (!document.getElementById('vat_rate').getAttribute('data-manual-override')) {
        vatRate = 5;
        document.getElementById('vat_rate').value = vatRate.toFixed(1);
    }
    
    // Calculate VAT on subtotal + shipping + customs
    const vatAmount = ((subTotal + shippingRate + customsFee) * (vatRate / 100));
    const total = subTotal + shippingRate + customsFee + vatAmount;
    
    // Update displays
    document.getElementById('subTotal').textContent = subTotal.toFixed(2);
    document.getElementById('shippingAmount').textContent = shippingRate.toFixed(2);
    document.getElementById('customsAmount').textContent = customsFee.toFixed(2);
    document.getElementById('vatAmount').textContent = vatAmount.toFixed(2);
    document.getElementById('vatRateDisplay').textContent = vatRate.toFixed(1);
    document.getElementById('totalAmount').textContent = total.toFixed(2);
    
    // Show/hide rows based on values
    document.getElementById('customsRow').style.display = customsFee > 0 ? 'flex' : 'none';
    document.getElementById('vatRow').style.display = vatRate > 0 ? 'flex' : 'none';
}

function updateShippingAmount() {
    calculateTotals();
}

function updateTotals() {
    calculateTotals();
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
        const productIdInput = row.querySelector('input[name*="[product_id]"]');
        const quantity = row.querySelector('input[name*="[quantity]"]');
        const rate = row.querySelector('input[name*="[rate]"]');
        
        if (!productIdInput || !productIdInput.value) {
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

// Initialize custom dropdown functionality
function initializeCustomDropdown(searchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput, specificationsInput, specificationsHidden, specificationsDropdown) {
    const allDropdownItems = dropdownItems.querySelectorAll('.dropdown-item');
    let selectedIndex = -1;
    
    // Show dropdown when input is focused
    searchInput.addEventListener('focus', function() {
        dropdownList.classList.remove('hidden');
        filterDropdownItems('');
    });
    
    // Filter items as user types
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterDropdownItems(searchTerm);
        selectedIndex = -1;
        dropdownList.classList.remove('hidden');
    });
    
    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const visibleItems = dropdownItems.querySelectorAll('.dropdown-item:not(.hidden)');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, visibleItems.length - 1);
            updateSelection(visibleItems);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(visibleItems);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
                selectProduct(visibleItems[selectedIndex]);
            }
        } else if (e.key === 'Escape') {
            dropdownList.classList.add('hidden');
            selectedIndex = -1;
        }
    });
    
    // Handle item clicks
    allDropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            selectProduct(this);
        });
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
            dropdownList.classList.add('hidden');
            selectedIndex = -1;
        }
    });
    
    function filterDropdownItems(searchTerm) {
        let visibleCount = 0;
        
        allDropdownItems.forEach(item => {
            const searchText = item.dataset.searchText || '';
            
            if (searchTerm === '' || searchText.includes(searchTerm)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            dropdownNoResults.classList.remove('hidden');
        } else {
            dropdownNoResults.classList.add('hidden');
        }
    }
    
    function updateSelection(visibleItems) {
        // Remove previous selection
        visibleItems.forEach(item => item.classList.remove('bg-indigo-50'));
        
        // Add selection to current item
        if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
            visibleItems[selectedIndex].classList.add('bg-indigo-50');
        }
    }
    
    function selectProduct(item) {
        const productId = item.dataset.id;
        const productName = item.dataset.name;
        const currency = document.getElementById('currency').value;
        const productPrice = currency === 'USD' ? item.dataset.priceUsd : item.dataset.priceAed;
        const procurementPrice = currency === 'USD' ? item.dataset.procurementPriceUsd : item.dataset.procurementPriceAed;
        const specifications = item.dataset.specifications;
        
        // Set values
        searchInput.value = productName;
        productIdInput.value = productId;
        itemDetailsHidden.value = productName;
        rateInput.value = productPrice || 0;
        
        // Store procurement price for customs calculation and populate size options
        const row = searchInput.closest('tr');
        rateInput.setAttribute('data-procurement-price', procurementPrice || 0);
        const sizeSelect = row.querySelector('.size-options-select');
        if (sizeSelect) {
            const hasSizeOptions = item.dataset.hasSizeOptions === 'true';
            const sizeOptions = item.dataset.sizeOptions ? JSON.parse(item.dataset.sizeOptions) : [];
            populateSizeOptionsFromData(sizeSelect, hasSizeOptions, sizeOptions);
            
            // Add event listener for size changes
            sizeSelect.addEventListener('change', updateSelectedSpecifications);
        }
        
        // Handle specifications
        if (specifications && specifications !== '[]') {
            try {
                const specsArray = JSON.parse(specifications);
                if (specsArray.length > 0) {
                    specificationsInput.value = 'Click to select specifications...';
                    specificationsHidden.value = JSON.stringify(specsArray);
                    
                    // Update specifications dropdown content with checkboxes
                    specificationsDropdown.innerHTML = '';
                    specsArray.forEach((spec, index) => {
                        const specDiv = document.createElement('div');
                        specDiv.className = 'p-3 text-sm text-gray-700 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 cursor-pointer flex items-center';
                        specDiv.innerHTML = `
                            <input type="checkbox" id="spec_${itemCounter}_${index}" class="mr-2 h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded spec-checkbox" data-spec="${spec}">
                            <label for="spec_${itemCounter}_${index}" class="flex-1 cursor-pointer">${spec}</label>
                        `;
                        specificationsDropdown.appendChild(specDiv);
                    });
                    
                    // Add "Select All" option
                    const selectAllDiv = document.createElement('div');
                    selectAllDiv.className = 'p-3 text-sm font-medium text-indigo-600 border-b border-gray-200 bg-indigo-50 hover:bg-indigo-100 cursor-pointer';
                    selectAllDiv.innerHTML = `
                        <input type="checkbox" id="select_all_${itemCounter}" class="mr-2 h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded select-all-checkbox">
                        <label for="select_all_${itemCounter}" class="cursor-pointer">Select All</label>
                    `;
                    specificationsDropdown.insertBefore(selectAllDiv, specificationsDropdown.firstChild);
                    
                    // Add event listeners for checkboxes
                    const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox');
                    const selectAllCheckbox = specificationsDropdown.querySelector('.select-all-checkbox');
                    
                    // Select All functionality
                    selectAllCheckbox.addEventListener('change', function() {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateSelectedSpecifications();
                    });
                    
                    // Individual checkbox functionality
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            updateSelectedSpecifications();
                            // Update select all checkbox
                            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
                            selectAllCheckbox.checked = allChecked;
                            selectAllCheckbox.indeterminate = someChecked && !allChecked;
                        });
                    });
                } else {
                    specificationsInput.value = '';
                    specificationsHidden.value = '';
                    specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
                }
            } catch (e) {
                specificationsInput.value = '';
                specificationsHidden.value = '';
                specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
            }
        } else {
            specificationsInput.value = '';
            specificationsHidden.value = '';
            specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
        }
        
        // Hide dropdown
        dropdownList.classList.add('hidden');
        selectedIndex = -1;
        
        // Trigger calculation
        calculateRowAmount({ target: rateInput });
        
        // Recalculate totals to update customs clearance
        calculateTotals();
    }
    
    function updateSelectedSpecifications() {
        const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox:checked');
        const selectedSpecs = Array.from(checkboxes).map(cb => cb.dataset.spec);
        
        let allSpecs = [...selectedSpecs];
        
        if (allSpecs.length > 0) {
            specificationsInput.value = allSpecs.join(', ');
            specificationsHidden.value = JSON.stringify(allSpecs);
        } else {
            specificationsInput.value = 'Click to select specifications...';
            specificationsHidden.value = '';
        }
    }
    
    // Clear selection function
    searchInput.addEventListener('blur', function() {
        // Small delay to allow click events to process
        setTimeout(() => {
            if (!productIdInput.value && searchInput.value) {
                // If no product was selected but there's text, clear it
                searchInput.value = '';
                itemDetailsHidden.value = '';
                rateInput.value = 0;
                specificationsInput.value = '';
                specificationsHidden.value = '';
                specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
                calculateRowAmount({ target: rateInput });
            }
        }, 200);
    });
}

// Add product data to window for access
const products = <?php echo json_encode($products->map(function($product) {
    return [
        'id' => $product->id,
        'name' => $product->name,
        'has_size_options' => $product->has_size_options,
        'size_options' => $product->size_options
    ];
})); ?>;
window.products = products;

// Function to populate size options from data attributes
function populateSizeOptionsFromData(sizeSelect, hasSizeOptions, sizeOptions) {
    if (!hasSizeOptions || !sizeOptions || sizeOptions.length === 0) {
        sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
        return;
    }

    let options = '<option value="">Select Size (if applicable)</option>';
    sizeOptions.forEach(size => {
        options += `<option value="${size}">${size}</option>`;
    });
    sizeSelect.innerHTML = options;
}

// Function to populate size options (kept for compatibility)
function populateSizeOptions(productId, sizeSelect) {
    if (!productId) {
        sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
        return;
    }

    const product = products.find(p => p.id == productId);
    if (!product || !product.has_size_options || !product.size_options) {
        sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
        return;
    }

    let options = '<option value="">Select Size (if applicable)</option>';
    product.size_options.forEach(size => {
        options += `<option value="${size}">${size}</option>`;
    });
    sizeSelect.innerHTML = options;
}

// Initialize with one item
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addItem').addEventListener('click', addItem);
    
    // Currency change handler
    document.getElementById('currency').addEventListener('change', function() {
        const selectedCurrency = this.value;
        
        // Update total currency display
        document.getElementById('totalCurrency').textContent = selectedCurrency;
        
        // Update all product price displays in dropdown
        const priceDisplaysAed = document.querySelectorAll('.price-display-aed');
        const priceDisplaysUsd = document.querySelectorAll('.price-display-usd');
        
        if (selectedCurrency === 'USD') {
            priceDisplaysAed.forEach(el => el.style.display = 'none');
            priceDisplaysUsd.forEach(el => el.style.display = 'inline');
        } else {
            priceDisplaysAed.forEach(el => el.style.display = 'inline');
            priceDisplaysUsd.forEach(el => el.style.display = 'none');
        }
        
        // Update existing item rates based on selected currency
        const itemRows = document.querySelectorAll('#itemsTable tr');
        itemRows.forEach(row => {
            const productIdInput = row.querySelector('input[name*="[product_id]"]');
            const rateInput = row.querySelector('input[name*="[rate]"]');
            
            if (productIdInput && productIdInput.value && rateInput) {
                // Find the product in dropdown to get price data
                const productItem = document.querySelector(`[data-id="${productIdInput.value}"]`);
                if (productItem) {
                    const newPrice = selectedCurrency === 'USD' ? 
                        productItem.dataset.priceUsd : 
                        productItem.dataset.priceAed;
                    rateInput.value = newPrice || 0;
                    
                    // Trigger recalculation
                    calculateRowAmount({ target: rateInput });
                }
            }
        });
    });
    
    // Initialize customer dropdown
    (function initializeCustomerDropdown(){
        const searchInput = document.getElementById('customer_search');
        const hiddenId = document.getElementById('customer_id');
        const dropdownList = document.getElementById('customer_dropdown_list');
        if (!searchInput || !hiddenId || !dropdownList) return;
        const dropdownItemsContainer = dropdownList.querySelector('.dropdown-items');
        const allItems = dropdownItemsContainer.querySelectorAll('.dropdown-item');
        const noResults = dropdownList.querySelector('.dropdown-no-results');
        let selectedIndex = -1;
        function filter(term){
            let visible=0; const t=(term||'').toLowerCase();
            allItems.forEach(item=>{ const st=item.dataset.searchText||''; if(t===''||st.includes(t)){ item.classList.remove('hidden'); visible++; } else { item.classList.add('hidden'); } });
            if (noResults) noResults.classList.toggle('hidden', visible!==0);
        }
        function updateHighlight(visibleItems){ visibleItems.forEach(i=>i.classList.remove('bg-indigo-50')); if(selectedIndex>=0&&visibleItems[selectedIndex]){ visibleItems[selectedIndex].classList.add('bg-indigo-50'); } }
        function selectCustomer(item){ const id=item.dataset.id; const name=item.dataset.name; const company=item.dataset.company; hiddenId.value=id; searchInput.value=company? name+' - '+company : name; dropdownList.classList.add('hidden'); selectedIndex=-1; }
        searchInput.addEventListener('focus', ()=>{ dropdownList.classList.remove('hidden'); filter(searchInput.value); });
        searchInput.addEventListener('input', (e)=>{ filter(e.target.value); selectedIndex=-1; dropdownList.classList.remove('hidden'); });
        searchInput.addEventListener('keydown', (e)=>{ const visibleItems=Array.from(dropdownItemsContainer.querySelectorAll('.dropdown-item:not(.hidden)')); if(e.key==='ArrowDown'){ e.preventDefault(); selectedIndex=Math.min(selectedIndex+1, visibleItems.length-1); updateHighlight(visibleItems);} else if(e.key==='ArrowUp'){ e.preventDefault(); selectedIndex=Math.max(selectedIndex-1, -1); updateHighlight(visibleItems);} else if(e.key==='Enter'){ e.preventDefault(); if(selectedIndex>=0&&visibleItems[selectedIndex]) selectCustomer(visibleItems[selectedIndex]); } else if(e.key==='Escape'){ dropdownList.classList.add('hidden'); selectedIndex=-1; }});
        allItems.forEach(item=> item.addEventListener('click', ()=> selectCustomer(item)));
        document.addEventListener('click', (e)=>{ if(!searchInput.contains(e.target) && !dropdownList.contains(e.target)){ dropdownList.classList.add('hidden'); selectedIndex=-1; } });
        searchInput.addEventListener('blur', ()=>{ setTimeout(()=>{ if(!hiddenId.value && searchInput.value){ searchInput.value=''; } }, 200); });
    })();

    // Add initial default item
    addItem();
    
    // Add event listeners for size changes on existing items
    document.querySelectorAll('.size-options-select').forEach(sizeSelect => {
        sizeSelect.addEventListener('change', updateSelectedSpecifications);
    });
    
    // Enhanced file upload handling
    const fileInput = document.getElementById('attachments');
    const fileList = document.getElementById('fileList');
    const uploadArea = document.getElementById('uploadArea');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    let uploadedFiles = [];
    
    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-indigo-400', 'bg-indigo-50');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-indigo-400', 'bg-indigo-50');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-indigo-400', 'bg-indigo-50');
        const files = e.dataTransfer.files;
        handleFiles(files);
    });
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        const maxFiles = 5;
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/tiff',
            'text/plain',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        // Clear existing files if we're adding new ones
        if (uploadedFiles.length === 0) {
        fileList.innerHTML = '';
        }
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validate file
            if (!allowedTypes.includes(file.type)) {
                showFileError(`File "${file.name}" is not a supported file type.`);
                continue;
            }
            
            if (file.size > maxSize) {
                showFileError(`File "${file.name}" is too large. Maximum size is 10MB.`);
                continue;
            }
            
            if (uploadedFiles.length >= maxFiles) {
                showFileError(`Maximum ${maxFiles} files allowed.`);
                break;
            }
            
            // Add file to list
            uploadedFiles.push(file);
            displayFile(file, uploadedFiles.length - 1);
        }
        
        // Update file input
        updateFileInput();
    }
    
    function displayFile(file, index) {
            const fileDiv = document.createElement('div');
        fileDiv.className = 'flex items-center justify-between bg-white border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors';
            fileDiv.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    ${getFileIcon(file.type)}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Ready
                </span>
                <button type="button" onclick="removeFile(${index})" class="text-red-600 hover:text-red-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            `;
            fileList.appendChild(fileDiv);
        }
    
    function getFileIcon(fileType) {
        if (fileType.includes('pdf')) {
            return '<div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center"><svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg></div>';
        } else if (fileType.includes('word') || fileType.includes('document')) {
            return '<div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg></div>';
        } else if (fileType.includes('image')) {
            return '<div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"><svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg></div>';
        } else if (fileType.includes('excel') || fileType.includes('spreadsheet')) {
            return '<div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"><svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg></div>';
        } else {
            return '<div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center"><svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg></div>';
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function removeFile(index) {
        uploadedFiles.splice(index, 1);
        updateFileDisplay();
        updateFileInput();
    }
    
    function updateFileDisplay() {
        fileList.innerHTML = '';
        uploadedFiles.forEach((file, index) => {
            displayFile(file, index);
        });
    }
    
    function updateFileInput() {
        // Create a new FileList-like object
        const dt = new DataTransfer();
        uploadedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }
    
    function showFileError(message) {
        // Create a temporary error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-50 border border-red-200 rounded-lg p-3 mb-3';
        errorDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm text-red-800">${message}</p>
            </div>
        `;
        fileList.appendChild(errorDiv);
        
        // Remove error after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 5000);
    }
    
    // Make removeFile function global
    window.removeFile = removeFile;
});
</script>
@endpush 