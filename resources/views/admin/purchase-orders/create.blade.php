@extends('admin.layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
    <!-- Full Width Container -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8">
        <!-- Header -->
        <div class="mb-8 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Purchase Order</h1>
                    <p class="text-gray-600 mt-2">Create a new purchase order to send to suppliers</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.purchase-orders.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                        Back to Purchase Orders
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

        <!-- Form -->
        <form action="{{ route('admin.purchase-orders.store') }}" method="POST" id="purchaseOrderForm">
            @csrf
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="space-y-8">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M8.25 9h2.25m-2.25 4.5h2.25m-2.25 4.5h2.25M10.5 21h4.5" />
                                </svg>
                                Purchase Order Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">Customer Order (Optional)</label>
                                    <select name="order_id" id="order_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select a customer order (optional)</option>
                                        @foreach($availableOrders as $order)
                                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                                {{ $order->order_number }} - {{ $order->getCustomerName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">Link to an existing customer order or create a standalone purchase order</p>
                                </div>
                                <div>
                                    <label for="po_date" class="block text-sm font-medium text-gray-700 mb-2">PO Date</label>
                                    <input type="date" id="po_date" name="po_date" 
                                           value="{{ old('po_date', now()->format('Y-m-d')) }}" required
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="delivery_date_requested" class="block text-sm font-medium text-gray-700 mb-2">Requested Delivery Date</label>
                                    <input type="date" id="delivery_date_requested" name="delivery_date_requested" 
                                           value="{{ old('delivery_date_requested', now()->addDays(14)->format('Y-m-d')) }}" required
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                    <select name="payment_terms" id="payment_terms" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select payment terms</option>
                                        <option value="Net 30" {{ old('payment_terms') == 'Net 30' ? 'selected' : '' }}>Net 30</option>
                                        <option value="Net 60" {{ old('payment_terms') == 'Net 60' ? 'selected' : '' }}>Net 60</option>
                                        <option value="Net 90" {{ old('payment_terms') == 'Net 90' ? 'selected' : '' }}>Net 90</option>
                                        <option value="Due on Receipt" {{ old('payment_terms') == 'Due on Receipt' ? 'selected' : '' }}>Due on Receipt</option>
                                        <option value="50% Advance, 50% on Delivery" {{ old('payment_terms') == '50% Advance, 50% on Delivery' ? 'selected' : '' }}>50% Advance, 50% on Delivery</option>
                                        <option value="Advance Payment" {{ old('payment_terms') == 'Advance Payment' ? 'selected' : '' }}>Advance Payment</option>
                                        <option value="Custom" {{ old('payment_terms') == 'Custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                    <select name="currency" id="currency" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        <option value="AED" {{ old('currency', 'AED') == 'AED' ? 'selected' : '' }}>AED (UAE Dirham)</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                        <option value="CYN" {{ old('currency') == 'CYN' ? 'selected' : '' }}>CYN (Chinese Yuan)</option>
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">This determines which procurement prices to use for products</p>
                                </div>
                                <div>
                                    <label for="shipping_method" class="block text-sm font-medium text-gray-700 mb-2">Shipping Method</label>
                                    <select name="shipping_method" id="shipping_method" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select shipping method</option>
                                        <option value="Standard Shipping" {{ old('shipping_method') == 'Standard Shipping' ? 'selected' : '' }}>Standard Shipping</option>
                                        <option value="Express Shipping" {{ old('shipping_method') == 'Express Shipping' ? 'selected' : '' }}>Express Shipping</option>
                                        <option value="Overnight Shipping" {{ old('shipping_method') == 'Overnight Shipping' ? 'selected' : '' }}>Overnight Shipping</option>
                                        <option value="Local Pickup" {{ old('shipping_method') == 'Local Pickup' ? 'selected' : '' }}>Local Pickup</option>
                                        <option value="Supplier Delivery" {{ old('shipping_method') == 'Supplier Delivery' ? 'selected' : '' }}>Supplier Delivery</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                                Supplier Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- Supplier Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Selection</label>
                                    <div class="flex items-center space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="supplier_type" value="existing" id="existing_supplier" 
                                                   class="mr-2 text-indigo-600 focus:ring-indigo-500 border-gray-300" 
                                                   {{ old('supplier_type', 'existing') == 'existing' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Select Existing Supplier</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="supplier_type" value="new" id="new_supplier" 
                                                   class="mr-2 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                                   {{ old('supplier_type') == 'new' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Enter New Supplier</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Existing Supplier Selection -->
                                <div id="existing_supplier_section">
                                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Select Supplier</label>
                                    <select id="supplier_id" name="supplier_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Choose a registered supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" 
                                                    data-company="{{ $supplier->supplierInformation->company_name ?? $supplier->name }}"
                                                    data-email="{{ $supplier->email }}"
                                                    data-phone="{{ $supplier->supplierInformation->phone_primary ?? '' }}"
                                                    data-address="{{ $supplier->supplierInformation->business_address ?? '' }}"
                                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->supplierInformation->company_name ?? $supplier->name }} 
                                                @if($supplier->supplierInformation)
                                                    ({{ $supplier->supplierInformation->city ?? '' }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Manual Supplier Entry -->
                                <div id="manual_supplier_section" style="display: none;">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-2">Supplier Name *</label>
                                            <input type="text" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="supplier_email" class="block text-sm font-medium text-gray-700 mb-2">Supplier Email</label>
                                            <input type="email" id="supplier_email" name="supplier_email" value="{{ old('supplier_email') }}"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="supplier_phone" class="block text-sm font-medium text-gray-700 mb-2">Supplier Phone</label>
                                            <input type="text" id="supplier_phone" name="supplier_phone" value="{{ old('supplier_phone') }}"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="supplier_contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                                            <input type="text" id="supplier_contact_person" name="supplier_contact_person" value="{{ old('supplier_contact_person') }}"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="supplier_address" class="block text-sm font-medium text-gray-700 mb-2">Supplier Address</label>
                                        <textarea id="supplier_address" name="supplier_address" rows="3"
                                                  placeholder="Enter supplier address"
                                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('supplier_address') }}</textarea>
                                    </div>
                                </div>
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
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 17.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Purchase Order Items
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
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 150px;">Rate (<span id="currency-label">AED</span>)</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Discount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Amount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Items will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Purchase Order Totals -->
                        <div class="mt-6 flex justify-end">
                            <div class="w-full max-w-sm">
                                <div class="rounded-lg bg-gray-50 p-4">
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">Sub Total:</span>
                                        <span id="subTotal" class="text-sm font-semibold text-gray-900">0.00 <span class="currency-display">AED</span></span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">Tax:</span>
                                        <span id="tax-amount" class="text-sm font-semibold text-gray-900">0.00 <span class="currency-display">AED</span></span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">Shipping:</span>
                                        <span id="shipping-amount" class="text-sm font-semibold text-gray-900">0.00 <span class="currency-display">AED</span></span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2">
                                        <div class="flex justify-between">
                                            <span class="text-base font-semibold text-gray-900">Total:</span>
                                            <span id="totalAmount" class="text-base font-bold text-gray-900">0.00 <span class="currency-display">AED</span></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hidden inputs for form submission -->
                                <input type="hidden" id="sub_total_hidden" name="sub_total" value="0">
                                <input type="hidden" id="total_amount_hidden" name="total_amount" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Sections - Back to Grid Layout -->
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="space-y-8">
                    <!-- Additional Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M8.25 9h2.25m-2.25 4.5h2.25m-2.25 4.5h2.25M10.5 21h4.5" />
                                </svg>
                                Additional Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea id="description" name="description" rows="3"
                                              placeholder="Enter purchase order description..."
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Brief description of the purchase order</p>
                                </div>
                                <div>
                                    <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                                    <textarea id="terms_conditions" name="terms_conditions" rows="6"
                                              placeholder="Enter terms and conditions for this purchase order..."
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('terms_conditions') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Terms and conditions for this purchase order</p>
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Internal Notes</label>
                                    <textarea id="notes" name="notes" rows="3"
                                              placeholder="Enter internal notes (not visible to supplier)..."
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Internal notes for your reference only</p>
                                </div>
                                <div>
                                    <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                                    <textarea id="special_instructions" name="special_instructions" rows="3"
                                              placeholder="Enter any special instructions for the supplier..."
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('special_instructions') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Special handling, packaging, or delivery instructions</p>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Create Button Section -->
            <div class="px-4 sm:px-6 lg:px-8 mt-8 mb-8">
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="saveAsDraft()" class="inline-flex items-center rounded-md bg-gray-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75h9z" />
                        </svg>
                        Save as Draft
                    </button>
                    <button type="submit" name="action" value="create" class="inline-flex items-center rounded-md bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        Create Purchase Order
                    </button>
                </div>
            </div>
        </form>
    </div>

@push('styles')
<style>
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
    .items-table td:nth-child(5) { width: 150px; } /* Rate */
    
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

// Currency change handling
function updateCurrencyDisplay() {
    const currency = document.getElementById('currency').value;
    const currencyLabels = document.querySelectorAll('#currency-label, .currency-display');
    
    currencyLabels.forEach(label => {
        label.textContent = currency;
    });
    
    // Update product price displays and selected rates
    updateAllProductPrices(currency);
    calculateTotals();
}

function updateAllProductPrices(currency) {
    const allRows = document.querySelectorAll('.item-row');
    
    allRows.forEach(row => {
        // Update dropdown prices
        const priceDisplaysAed = row.querySelectorAll('.price-display-aed');
        const priceDisplaysUsd = row.querySelectorAll('.price-display-usd');
        
        if (currency === 'USD') {
            priceDisplaysAed.forEach(display => display.classList.add('hidden'));
            priceDisplaysUsd.forEach(display => display.classList.remove('hidden'));
        } else if (currency === 'AED') {
            priceDisplaysAed.forEach(display => display.classList.remove('hidden'));
            priceDisplaysUsd.forEach(display => display.classList.add('hidden'));
        } else {
            priceDisplaysAed.forEach(display => display.classList.add('hidden'));
            priceDisplaysUsd.forEach(display => display.classList.add('hidden'));
        }
        
        // Update rate input if product is selected
        const productIdInput = row.querySelector('.product-id-input');
        const rateInput = row.querySelector('.rate-input');
        
        if (productIdInput && productIdInput.value && rateInput) {
            const dropdownItem = row.querySelector(`[data-id="${productIdInput.value}"]`);
            if (dropdownItem) {
                let price = '0';
                if (currency === 'USD') {
                    price = dropdownItem.getAttribute('data-procurement-price-usd') || dropdownItem.getAttribute('data-price-usd') || '0';
                } else if (currency === 'AED') {
                    price = dropdownItem.getAttribute('data-procurement-price-aed') || dropdownItem.getAttribute('data-price-aed') || '0';
                } else {
                    // For CYN or other currencies, do not auto-populate
                    price = '0';
                }
                rateInput.value = parseFloat(price).toFixed(2);
                calculateRowAmount({ target: rateInput });
            }
        }
    });
}

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
                <input type="hidden" name="items[${itemCounter}][item_description]" class="item-details-hidden">
                
                <!-- Dropdown List -->
                <div class="product-dropdown-list hidden">
                    <div class="p-2 text-sm text-gray-500 dropdown-loading hidden">Searching...</div>
                    <div class="dropdown-items">
                        @foreach(($products ?? \Illuminate\Support\Collection::make([])) as $product)
                            <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" 
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-description="{{ $product->description }}"
                                 data-price-aed="{{ $product->price_aed ?? $product->price }}"
                                 data-price-usd="{{ $product->price }}"
                                 data-procurement-price-aed="{{ $product->procurement_price_aed ?? $product->price_aed ?? $product->price }}"
                                 data-procurement-price-usd="{{ $product->procurement_price_usd ?? $product->price }}"
                                 data-specifications="{{ $product->specifications ? json_encode($product->specifications->map(function($spec) { return $spec->display_name . ': ' . $spec->formatted_value; })->toArray()) : '[]' }}"
                                 data-has-size-options="{{ $product->has_size_options ? 'true' : 'false' }}"
                                 data-size-options="{{ is_array($product->size_options) ? json_encode($product->size_options) : ($product->size_options ?: '[]') }}"
                                 data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                @if($product->description)
                                    <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                @endif
                                @if($product->procurement_price_aed ?? $product->price_aed ?? $product->price)
                                    <div class="price-display-aed text-indigo-600 text-sm font-medium mt-1">AED {{ number_format($product->procurement_price_aed ?? $product->price_aed ?? $product->price, 2) }}</div>
                                    <div class="price-display-usd text-indigo-600 text-sm font-medium mt-1 hidden">USD {{ number_format($product->procurement_price_usd ?? $product->price, 2) }}</div>
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
            <input type="number" step="0.01" name="items[${itemCounter}][unit_price]" value="0.00" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rate-input">
        </td>
        <td class="px-3 py-4">
            <div class="flex">
                <input type="number" step="0.01" name="items[${itemCounter}][discount_percentage]" value="0" min="0" max="100"
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
    let total = 0;
    
    amounts.forEach(amount => {
        total += parseFloat(amount.textContent) || 0;
    });
    
    const currency = document.getElementById('currency').value;
    document.getElementById('subTotal').innerHTML = `${total.toFixed(2)} <span class="currency-display">${currency}</span>`;
    document.getElementById('totalAmount').innerHTML = `${total.toFixed(2)} <span class="currency-display">${currency}</span>`;
    
    // Update hidden inputs for form submission
    document.getElementById('sub_total_hidden').value = total.toFixed(2);
    document.getElementById('total_amount_hidden').value = total.toFixed(2);
}

function validateForm() {
    // Validate that we have at least one item
    const itemRows = document.querySelectorAll('#itemsTable tr');
    if (itemRows.length === 0) {
        alert('Please add at least one item before saving the purchase order.');
        return false;
    }
    
    // Validate that all item rows have required data
    let hasEmptyItems = false;
    itemRows.forEach(row => {
        const productIdInput = row.querySelector('input[name*="[product_id]"]');
        const quantity = row.querySelector('input[name*="[quantity]"]');
        const rate = row.querySelector('input[name*="[unit_price]"]');
        
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
        const specifications = item.dataset.specifications;
        
        // Set values
        searchInput.value = productName;
        productIdInput.value = productId;
        itemDetailsHidden.value = productName;
        
        // Set rate based on selected currency and procurement price
        const currency = document.getElementById('currency').value;
        let price = '0';
        if (currency === 'USD') {
            price = item.dataset.procurementPriceUsd || item.dataset.priceUsd || '0';
        } else if (currency === 'AED') {
            price = item.dataset.procurementPriceAed || item.dataset.priceAed || '0';
        } else {
            // For CYN or other currencies, keep zero and let user input manually
            price = '0';
        }
        rateInput.value = parseFloat(price).toFixed(2);
        
        // Handle specifications
        if (specifications && specifications !== '[]') {
            try {
                const specsArray = JSON.parse(specifications);
                if (specsArray.length > 0) {
                    specificationsInput.value = 'Click to select specifications...';
                    specificationsHidden.value = JSON.stringify(specsArray);
                    
                    // Create checkboxes for specifications
                    let checkboxesHtml = '';
                    specsArray.forEach(spec => {
                        checkboxesHtml += `
                            <label class="flex items-center p-2 hover:bg-gray-50">
                                <input type="checkbox" class="spec-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" data-spec="${spec}">
                                <span class="ml-2 text-sm text-gray-700">${spec}</span>
                            </label>
                        `;
                    });
                    specificationsDropdown.innerHTML = checkboxesHtml;
                    
                    // Add event listeners to checkboxes
                    const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', updateSelectedSpecifications);
                    });
                }
            } catch (e) {
                console.error('Error parsing specifications:', e);
                specificationsInput.value = '';
                specificationsHidden.value = '';
                specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
            }
        } else {
            specificationsInput.value = '';
            specificationsHidden.value = '';
            specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
        }
        
        // Populate size options
        const itemRow = searchInput.closest('tr');
        const sizeSelect = itemRow.querySelector('.size-options-select');
        if (sizeSelect) {
            const hasSizeOptions = item.dataset.hasSizeOptions === 'true';
            const sizeOptions = item.dataset.sizeOptions ? JSON.parse(item.dataset.sizeOptions) : [];
            populateSizeOptionsFromData(sizeSelect, hasSizeOptions, sizeOptions);
            
            // Add event listener for size changes
            sizeSelect.addEventListener('change', updateSelectedSpecifications);
        }
        
        // Hide dropdown
        dropdownList.classList.add('hidden');
        selectedIndex = -1;
        
        // Trigger calculation
        calculateRowAmount({ target: rateInput });
    }
    
    function updateSelectedSpecifications() {
        const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox:checked');
        const selectedSpecs = Array.from(checkboxes).map(cb => cb.dataset.spec);
        
        // Get selected size
        const row = specificationsDropdown.closest('tr');
        const sizeSelect = row.querySelector('.size-options-select');
        const selectedSize = sizeSelect ? sizeSelect.value : '';
        
        // Combine specifications and size
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

// Function to populate size options from data attributes (global scope)
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

    const product = window.products ? window.products.find(p => p.id == productId) : null;
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

// Initialize supplier selection functionality
function initializeSupplierSelection() {
    const existingSupplierRadio = document.getElementById('existing_supplier');
    const newSupplierRadio = document.getElementById('new_supplier');
    const existingSupplierSection = document.getElementById('existing_supplier_section');
    const manualSupplierSection = document.getElementById('manual_supplier_section');
    const supplierIdSelect = document.getElementById('supplier_id');
    
    // Function to toggle supplier sections
    function toggleSupplierSections() {
        if (existingSupplierRadio.checked) {
            existingSupplierSection.style.display = 'block';
            manualSupplierSection.style.display = 'none';
            
            // Clear manual supplier inputs
            document.getElementById('supplier_name').value = '';
            document.getElementById('supplier_email').value = '';
            document.getElementById('supplier_phone').value = '';
            document.getElementById('supplier_address').value = '';
        } else {
            existingSupplierSection.style.display = 'none';
            manualSupplierSection.style.display = 'block';
            
            // Clear existing supplier selection
            supplierIdSelect.value = '';
        }
    }
    
    // Add event listeners to radio buttons
    existingSupplierRadio.addEventListener('change', toggleSupplierSections);
    newSupplierRadio.addEventListener('change', toggleSupplierSections);
    
    // Initialize based on current selection
    toggleSupplierSections();
    
    // Auto-populate manual fields when existing supplier is selected
    supplierIdSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const supplierName = selectedOption.getAttribute('data-company');
            const supplierEmail = selectedOption.getAttribute('data-email');
            const supplierPhone = selectedOption.getAttribute('data-phone');
            const supplierAddress = selectedOption.getAttribute('data-address');
            
            // Populate manual fields (these will be used for the PO)
            document.getElementById('supplier_name').value = supplierName || '';
            document.getElementById('supplier_email').value = supplierEmail || '';
            document.getElementById('supplier_phone').value = supplierPhone || '';
            document.getElementById('supplier_address').value = supplierAddress || '';
        }
    });
}

// Initialize with one item
document.addEventListener('DOMContentLoaded', function() {
    // Initialize supplier selection functionality
    initializeSupplierSelection();
    
    // Add currency change event listener
    document.getElementById('currency').addEventListener('change', updateCurrencyDisplay);
    
    document.getElementById('addItem').addEventListener('click', addItem);
    
    // Initialize existing items if any
    const existingItems = document.querySelectorAll('.item-row');
    existingItems.forEach(row => {
        const quantityInput = row.querySelector('.quantity-input');
        const rateInput = row.querySelector('.rate-input');
        const discountInput = row.querySelector('.discount-input');
        const priceTypeSelect = row.querySelector('.price-type-select');
        const productSearchInput = row.querySelector('.product-search-input');
        const productIdInput = row.querySelector('.product-id-input');
        const itemDetailsHidden = row.querySelector('.item-details-hidden');
        const dropdownList = row.querySelector('.product-dropdown-list');
        const dropdownItems = row.querySelector('.dropdown-items');
        const dropdownNoResults = row.querySelector('.dropdown-no-results');
        const specificationsInput = row.querySelector('.specifications-search-input');
        const specificationsHidden = row.querySelector('.specifications-hidden');
        const specificationsDropdown = row.querySelector('.specifications-dropdown-list');
        const sizeSelect = row.querySelector('.size-options-select');
        
        [quantityInput, rateInput, discountInput].forEach(input => {
            input.addEventListener('input', calculateRowAmount);
        });
        
        // Add event listener for price type change
        if (priceTypeSelect) {
            priceTypeSelect.addEventListener('change', function() {
                updateRateFromPriceType(this);
            });
        }
        
        // Initialize custom dropdown functionality
        initializeCustomDropdown(productSearchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput, specificationsInput, specificationsHidden, specificationsDropdown);
        
        // Initialize size options if product is selected
        if (productIdInput.value) {
            populateSizeOptions(productIdInput.value, sizeSelect);
        }
        
        // Add event listener for size changes
        if (sizeSelect) {
            sizeSelect.addEventListener('change', updateSelectedSpecifications);
        }
    });
    
    // Add initial item if none exist
    if (existingItems.length === 0) {
        addItem();
    }
    
    calculateTotals();

    // Update size options when product is selected
    document.addEventListener('change', function(e) {
        if (e.target.matches('.product-select')) {
            const row = e.target.closest('tr');
            const sizeSelect = row.querySelector('.size-options-select');
            populateSizeOptions(e.target.value, sizeSelect);
        }
    });

    // Add product data to window for access
    const products = <?php echo json_encode(($products ?? \Illuminate\Support\Collection::make([]))->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'has_size_options' => $product->has_size_options,
            'size_options' => $product->size_options,
            'price' => $product->price_aed ?? $product->price
        ];
    })); ?>;
    window.products = products;
});
</script>
@endpush

@endsection 