@extends('admin.layouts.app')

@section('title', 'Create Invoice')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Invoice</h1>
                <p class="text-gray-600 mt-2">Create a new proforma or final invoice</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Invoices
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <div class="grid grid-cols-1 gap-8">
            <!-- Main Form -->
            <div class="space-y-8">
                <!-- Basic Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Invoice Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Invoice Type</label>
                                <select name="type" id="type" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="proforma" {{ old('type', 'proforma') == 'proforma' ? 'selected' : '' }}>Proforma Invoice</option>
                                    <option value="final" {{ old('type') == 'final' ? 'selected' : '' }}>Final Invoice</option>
                                </select>
                            </div>
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                                <select name="customer_id" id="customer_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select a customer...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}{{ $customer->company_name ? ' - ' . $customer->company_name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="customer_name" id="customer_name_hidden" value="{{ old('customer_name', $quote->customer_name ?? '') }}">
                            </div>
                            <div>
                                <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">Invoice Date</label>
                                <input type="date" id="invoice_date" name="invoice_date" 
                                       value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                <input type="date" id="due_date" name="due_date" 
                                       value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                                <input type="text" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number', $quote->reference_number ?? '') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="po_number" class="block text-sm font-medium text-gray-700 mb-2">PO Number</label>
                                <input type="text" id="po_number" name="po_number" 
                                       value="{{ old('po_number') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Customer Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">Billing Address</label>
                                <textarea id="billing_address" name="billing_address" rows="3" required
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('billing_address') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">This will be automatically filled when you select a customer</p>
                            </div>
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Shipping Address</label>
                                <textarea id="shipping_address" name="shipping_address" rows="3"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('shipping_address') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">This will be automatically filled when you select a customer</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 17.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Invoice Items
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
                                    @if($quote && $quote->items->count() > 0)
                                        @foreach($quote->items as $index => $item)
                                        <tr class="item-row bg-white hover:bg-gray-50" draggable="true">
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
                                                           value="{{ $item->item_details }}"
                                                           autocomplete="off">
                                                    <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id-input" value="{{ $item->product_id ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][item_description]" class="item-details-hidden" value="{{ $item->item_details }}">
                                                    
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
                                                                     data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                                                    <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                                                    @if($product->description)
                                                                        <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                                                    @endif
                                                                    @if($product->price_aed ?? $product->price)
                                                                        <div class="text-indigo-600 text-sm font-medium mt-1">AED {{ number_format($product->price_aed ?? $product->price, 2) }}</div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="p-3 text-sm text-gray-500 text-center dropdown-no-results hidden">No products found</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4">
                                                <input type="number" step="0.01" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" required
                                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 quantity-input">
                                            </td>
                                            <td class="px-3 py-4">
                                                <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" value="{{ $item->rate }}" required
                                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rate-input">
                                            </td>
                                            <td class="px-3 py-4">
                                                <div class="flex">
                                                    <input type="number" step="0.01" name="items[{{ $index }}][discount_percentage]" value="{{ $item->discount }}" min="0" max="100"
                                                           class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 discount-input">
                                                    <span class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">%</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 text-right">
                                                <span class="amount-display font-medium text-gray-900">{{ number_format($item->amount, 2) }}</span>
                                            </td>
                                            <td class="px-3 py-4 text-center">
                                                <button type="button" onclick="removeItem(this)" class="inline-flex items-center p-1 border border-transparent rounded-full text-red-600 hover:bg-red-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                   
                        
                        <!-- Invoice Totals -->
                        <div class="mt-8 flex justify-end">
                            <div class="w-full max-w-sm">
                                <div class="rounded-lg bg-gray-50 p-6">
                                    <div class="flex justify-between mb-3">
                                        <span class="text-sm font-medium text-gray-700">Sub Total:</span>
                                        <span id="subTotal" class="text-sm font-semibold text-gray-900">0.00 AED</span>
                                    </div>
                                    <div class="flex justify-between mb-3">
                                        <span class="text-sm font-medium text-gray-700">Tax:</span>
                                        <span id="tax-amount" class="text-sm font-semibold text-gray-900">0.00 AED</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-3">
                                        <div class="flex justify-between">
                                            <span class="text-base font-semibold text-gray-900">Total:</span>
                                            <span id="totalAmount" class="text-base font-bold text-gray-900">0.00 AED</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
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
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $quote->subject ?? '') }}</textarea>
                            </div>
                            <div>
                                <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                                <textarea id="terms_conditions" name="terms_conditions" rows="3"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('terms_conditions', $quote->terms_conditions ?? '') }}</textarea>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea id="notes" name="notes" rows="3"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $quote->customer_notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Payment Terms and Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Payment Terms -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            Payment Terms
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                <select name="payment_terms" id="payment_terms" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="advance_50" {{ old('payment_terms', 'advance_50') == 'advance_50' ? 'selected' : '' }}>50% Advance Payment</option>
                                    <option value="advance_100" {{ old('payment_terms') == 'advance_100' ? 'selected' : '' }}>100% Advance Payment</option>
                                    <option value="on_delivery" {{ old('payment_terms') == 'on_delivery' ? 'selected' : '' }}>Payment on Delivery</option>
                                    <option value="net_30" {{ old('payment_terms') == 'net_30' ? 'selected' : '' }}>Net 30 Days</option>
                                    <option value="custom" {{ old('payment_terms') == 'custom' ? 'selected' : '' }}>Custom Terms</option>
                                </select>
                            </div>
                            <div id="custom-percentage" style="display: none;">
                                <label for="advance_percentage" class="block text-sm font-medium text-gray-700 mb-2">Advance Percentage</label>
                                <input type="number" id="advance_percentage" name="advance_percentage" min="0" max="100" step="0.01" 
                                       value="{{ old('advance_percentage') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" name="status" value="draft" class="w-full inline-flex justify-center items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                </svg>
                                Save as Draft
                            </button>
                            <button type="submit" name="status" value="sent" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                                Create & Send
                            </button>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
<style>
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

/* Item row styling */
.item-row {
    cursor: grab;
}

.item-row:active {
    cursor: grabbing;
}

.drag-handle {
    cursor: grab;
}

/* Custom Dropdown Styles */
.product-dropdown-container {
    position: relative;
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
    z-index: 9999;
    margin-top: 4px;
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
    z-index: 9999;
}

.dropdown-item {
    transition: background-color 0.15s ease-in-out;
    cursor: pointer;
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
</style>
@endpush

@push('scripts')
<script>
let itemCounter = 0;

// Customer selection handling
document.getElementById('customer_id').addEventListener('change', function() {
    const customerId = this.value;
    
    if (customerId) {
        // Fetch customer details via API
        fetch(`/admin/customers/${customerId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Customer not found');
                    return;
                }
                
                // Update hidden customer name field and address fields
                document.getElementById('customer_name_hidden').value = data.name || '';
                document.getElementById('billing_address').value = data.billing_address || '';
                document.getElementById('shipping_address').value = data.shipping_address || data.billing_address || '';
            })
            .catch(error => {
                console.error('Error fetching customer details:', error);
            });
    } else {
        // Clear fields if no customer selected
        document.getElementById('customer_name_hidden').value = '';
        document.getElementById('billing_address').value = '';
        document.getElementById('shipping_address').value = '';
    }
});

// Payment terms handling
document.getElementById('payment_terms').addEventListener('change', function() {
    const customDiv = document.getElementById('custom-percentage');
    if (this.value === 'custom') {
        customDiv.style.display = 'block';
    } else {
        customDiv.style.display = 'none';
    }
});

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
                        @foreach($products as $product)
                            <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" 
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-description="{{ $product->description }}"
                                 data-price="{{ $product->price_aed ?? $product->price }}"
                                 data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                @if($product->description)
                                    <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                @endif
                                @if($product->price_aed ?? $product->price)
                                    <div class="text-indigo-600 text-sm font-medium mt-1">AED {{ number_format($product->price_aed ?? $product->price, 2) }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3 text-sm text-gray-500 text-center dropdown-no-results hidden">No products found</div>
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
    const productSearchInput = row.querySelector('.product-search-input');
    const productIdInput = row.querySelector('.product-id-input');
    const itemDetailsHidden = row.querySelector('.item-details-hidden');
    const dropdownList = row.querySelector('.product-dropdown-list');
    const dropdownItems = row.querySelector('.dropdown-items');
    const dropdownNoResults = row.querySelector('.dropdown-no-results');
    
    [quantityInput, rateInput, discountInput].forEach(input => {
        input.addEventListener('input', calculateRowAmount);
    });
    
    // Initialize custom dropdown functionality
    initializeCustomDropdown(productSearchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput);
    
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
        alert('Please add at least one item before saving the invoice.');
        return false;
    }
    
    // Check if required fields are filled
    const customerSelect = document.getElementById('customer_id');
    if (!customerSelect.value) {
        alert('Please select a customer before saving the invoice.');
        customerSelect.focus();
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
function initializeCustomDropdown(searchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput) {
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
        const productPrice = item.dataset.price;
        
        // Set values
        searchInput.value = productName;
        productIdInput.value = productId;
        itemDetailsHidden.value = productName;
        rateInput.value = productPrice || 0;
        
        // Hide dropdown
        dropdownList.classList.add('hidden');
        selectedIndex = -1;
        
        // Trigger calculation
        calculateRowAmount({ target: rateInput });
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
                calculateRowAmount({ target: rateInput });
            }
        }, 200);
    });
}

// Initialize with one item
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addItem').addEventListener('click', addItem);
    
    // Initialize existing items if any
    const existingItems = document.querySelectorAll('.item-row');
    existingItems.forEach(row => {
        const quantityInput = row.querySelector('.quantity-input');
        const rateInput = row.querySelector('.rate-input');
        const discountInput = row.querySelector('.discount-input');
        const productSearchInput = row.querySelector('.product-search-input');
        const productIdInput = row.querySelector('.product-id-input');
        const itemDetailsHidden = row.querySelector('.item-details-hidden');
        const dropdownList = row.querySelector('.product-dropdown-list');
        const dropdownItems = row.querySelector('.dropdown-items');
        const dropdownNoResults = row.querySelector('.dropdown-no-results');
        
        [quantityInput, rateInput, discountInput].forEach(input => {
            input.addEventListener('input', calculateRowAmount);
        });
        
        // Initialize custom dropdown functionality
        if (productSearchInput) {
            initializeCustomDropdown(productSearchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput);
        }
    });
    
    // Add initial default item if none exist
    if (existingItems.length === 0) {
        addItem();
    }
    
    calculateTotals();
    
    // Trigger payment terms check
    document.getElementById('payment_terms').dispatchEvent(new Event('change'));
});
</script>
@endpush 