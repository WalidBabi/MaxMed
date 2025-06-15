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
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-content-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 17.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Invoice Items
                        </h3>
                        <button type="button" onclick="addItem()" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
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
                                <tbody id="invoice-items" class="bg-white divide-y divide-gray-200">
                                    @if($quote && $quote->items->count() > 0)
                                        @foreach($quote->items as $index => $item)
                                            <tr class="item-row bg-white hover:bg-gray-50" draggable="true">
                                                <td class="px-3 py-4 text-center">
                                                    <svg class="w-4 h-4 text-gray-400 cursor-pointer drag-handle mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                    </svg>
                                                </td>
                                                <td class="px-3 py-4">
                                                    <select name="items[{{ $index }}][product_id]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 product-select" required>
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
                                                    <input type="hidden" name="items[{{ $index }}][item_description]" class="item-description" value="{{ $item->item_details }}">
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
                                                    <span class="line-total font-medium text-gray-900">{{ number_format($item->amount, 2) }}</span>
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
                        
                        <!-- Add Item Button -->
                        <div class="mt-4">
                            <button type="button" onclick="addItem()" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                + Add another line
                            </button>
                        </div>
                        
                        <!-- Invoice Totals -->
                        <div class="mt-8 flex justify-end">
                            <div class="w-full max-w-sm">
                                <div class="rounded-lg bg-gray-50 p-6">
                                    <div class="flex justify-between mb-3">
                                        <span class="text-sm font-medium text-gray-700">Sub Total:</span>
                                        <span id="sub-total" class="text-sm font-semibold text-gray-900">0.00 AED</span>
                                    </div>
                                    <div class="flex justify-between mb-3">
                                        <span class="text-sm font-medium text-gray-700">Tax:</span>
                                        <span id="tax-amount" class="text-sm font-semibold text-gray-900">0.00 AED</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-3">
                                        <div class="flex justify-between">
                                            <span class="text-base font-semibold text-gray-900">Total:</span>
                                            <span id="total-amount" class="text-base font-bold text-gray-900">0.00 AED</span>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 8px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
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

/* Select2 result styling */
.select2-result-product {
    padding: 12px;
    border-bottom: 1px solid #f3f4f6;
}
.select2-result-product__title {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9rem;
}
.select2-result-product__description {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 4px;
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

/* Enhanced Select2 Styling */
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: #fff;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.select2-container--default .select2-selection--single:focus-within {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
    padding-right: 20px;
    color: #374151;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 8px;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #9ca3af;
}

.select2-dropdown {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.select2-search--dropdown {
    padding: 8px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #d1d5db;
}

.select2-search__field {
    width: 100% !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.375rem !important;
    padding: 8px 12px !important;
    font-size: 0.875rem !important;
}

.select2-results__options {
    max-height: 300px;
}

.select2-results__option {
    padding: 10px 12px;
    line-height: 1.5;
    color: #374151;
    cursor: pointer;
}

.select2-results__option:hover,
.select2-results__option--highlighted {
    background-color: #f3f4f6 !important;
    color: #374151 !important;
}

.select2-results__option--selected {
    background-color: #6366f1 !important;
    color: #fff !important;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let itemIndex = 1;

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

// Product selection handling
function handleProductSelect(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const itemRow = selectElement.closest('tr');
    
    if (selectedOption.value) {
        const productName = selectedOption.getAttribute('data-name');
        const productDescription = selectedOption.getAttribute('data-description');
        const productPrice = selectedOption.getAttribute('data-price');
        
        // Update item description and price
        const descriptionField = itemRow.querySelector('.item-description');
        const priceField = itemRow.querySelector('.rate-input');
        
        if (descriptionField && productName) {
            descriptionField.value = productName + (productDescription ? '\n' + productDescription : '');
        }
        
        if (priceField && productPrice) {
            priceField.value = parseFloat(productPrice).toFixed(2);
        }
        
        // Recalculate totals
        calculateLineTotal(itemRow);
        calculateTotals();
    }
}

// Payment terms handling
document.getElementById('payment_terms').addEventListener('change', function() {
    const customDiv = document.getElementById('custom-percentage');
    if (this.value === 'custom') {
        customDiv.style.display = 'block';
    } else {
        customDiv.style.display = 'none';
    }
});

// Add new item
function addItem() {
    const tbody = document.getElementById('invoice-items');
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
            <select name="items[${itemIndex}][product_id]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 product-select" required>
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
            <input type="hidden" name="items[${itemIndex}][item_description]" class="item-description" value="">
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemIndex}][quantity]" value="1.00" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 quantity-input">
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" value="0.00" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rate-input">
        </td>
        <td class="px-3 py-4">
            <div class="flex">
                <input type="number" step="0.01" name="items[${itemIndex}][discount_percentage]" value="0" min="0" max="100"
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 discount-input">
                <span class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">%</span>
            </div>
        </td>
        <td class="px-3 py-4 text-right">
            <span class="line-total font-medium text-gray-900">0.00</span>
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
    itemIndex++;
    
    // Add event listeners to new inputs
    addCalculationListeners(row);
    
    // Initialize Select2 for new product select
    const productSelect = row.querySelector('.product-select');
    if (productSelect) {
        // Populate product options dynamically
        const existingSelect = document.querySelector('.product-select');
        if (existingSelect && existingSelect !== productSelect) {
            const options = existingSelect.innerHTML;
            productSelect.innerHTML = options;
        }
        
        // Initialize Select2 with enhanced search
        $(productSelect).select2({
            placeholder: 'Search and select a product...',
            allowClear: true,
            width: '100%',
            minimumInputLength: 0,
            templateResult: formatProductResult,
            templateSelection: formatProductSelection,
            matcher: function(params, data) {
                // If there are no search terms, return all data
                if ($.trim(params.term) === '') {
                    return data;
                }
                
                // Skip if no text property
                if (typeof data.text === 'undefined') {
                    return null;
                }
                
                // Search in multiple fields
                const searchTerm = params.term.toLowerCase();
                const searchText = data.text.toLowerCase();
                const productName = $(data.element).data('name') ? $(data.element).data('name').toString().toLowerCase() : '';
                const productDescription = $(data.element).data('description') ? $(data.element).data('description').toString().toLowerCase() : '';
                
                const combinedText = searchText + ' ' + productName + ' ' + productDescription;
                
                if (combinedText.indexOf(searchTerm) > -1) {
                    return data;
                }
                
                return null;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        
        // Handle Select2 change event
        $(productSelect).on('select2:select', function(e) {
            handleProductSelect(this);
        });
        
        $(productSelect).on('select2:clear', function(e) {
            const itemRow = this.closest('tr');
            itemRow.querySelector('.item-description').value = '';
            itemRow.querySelector('.rate-input').value = 0;
            calculateLineTotal(itemRow);
            calculateTotals();
        });
    }
}

// Remove item
function removeItem(button) {
    const tbody = document.getElementById('invoice-items');
    if (tbody.children.length > 1) {
        button.closest('tr').remove();
        calculateTotals();
    } else {
        alert('You must have at least one item.');
    }
}

// Add calculation listeners to inputs
function addCalculationListeners(container = document) {
    const inputs = container.querySelectorAll('.quantity-input, .price-input, .discount-input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            calculateLineTotal(this.closest('.item-row'));
            calculateTotals();
        });
    });
}

// Calculate line total for a specific item
function calculateLineTotal(itemRow) {
    const quantity = parseFloat(itemRow.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(itemRow.querySelector('.rate-input').value) || 0;
    const discount = parseFloat(itemRow.querySelector('.discount-input').value) || 0;
    
    const subtotal = quantity * price;
    const discountAmount = subtotal * (discount / 100);
    const total = subtotal - discountAmount;
    
    itemRow.querySelector('.line-total').textContent = total.toFixed(2);
}

// Calculate invoice totals
function calculateTotals() {
    let subTotal = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const lineTotal = parseFloat(row.querySelector('.line-total').textContent) || 0;
        subTotal += lineTotal;
    });
    
    const taxAmount = 0; // Add tax calculation if needed
    const totalAmount = subTotal + taxAmount;
    
    document.getElementById('sub-total').textContent = subTotal.toFixed(2) + ' AED';
    document.getElementById('tax-amount').textContent = taxAmount.toFixed(2) + ' AED';
    document.getElementById('total-amount').textContent = totalAmount.toFixed(2) + ' AED';
}

// Format function for product results in dropdown
function formatProductResult(product) {
    if (product.loading) {
        return product.text;
    }
    
    var $container = $(
        "<div class='select2-result-product clearfix'>" +
            "<div class='select2-result-product__meta'>" +
                "<div class='select2-result-product__title'></div>" +
                "<div class='select2-result-product__description'></div>" +
            "</div>" +
        "</div>"
    );
    
    var element = product.element;
    if (element) {
        $container.find(".select2-result-product__title").text(product.text);
        var price = $(element).data('price');
        if (price) {
            $container.find(".select2-result-product__description").text('Price: ' + parseFloat(price).toFixed(2) + ' AED');
        }
    } else {
        $container.find(".select2-result-product__title").text(product.text);
    }
    
    return $container;
}

// Format function for selected product
function formatProductSelection(product) {
    return product.text || product.element.textContent;
}

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for customer dropdown
    $('#customer_id').select2({
        placeholder: 'Search and select a customer...',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        escapeMarkup: function (markup) {
            return markup;
        }
    });
    
    // Initialize Select2 for product dropdowns with enhanced search
    $('.product-select').select2({
        placeholder: 'Search products by name, brand, or category...',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        templateResult: formatProductResult,
        templateSelection: formatProductSelection,
        escapeMarkup: function (markup) {
            return markup;
        }
    });
    
    // Format function for product results in dropdown
    function formatProductResult(product) {
        if (product.loading) {
            return product.text;
        }
        
        var $container = $(
            "<div class='select2-result-product clearfix'>" +
                "<div class='select2-result-product__meta'>" +
                    "<div class='select2-result-product__title'></div>" +
                    "<div class='select2-result-product__description'></div>" +
                "</div>" +
            "</div>"
        );
        
        var element = product.element;
        if (element) {
            $container.find(".select2-result-product__title").text(product.text);
            var price = $(element).data('price');
            if (price) {
                $container.find(".select2-result-product__description").text('Price: ' + parseFloat(price).toFixed(2) + ' AED');
            }
        } else {
            $container.find(".select2-result-product__title").text(product.text);
        }
        
        return $container;
    }
    
    // Format function for selected product
    function formatProductSelection(product) {
        return product.text || product.element.textContent;
    }
    
    addCalculationListeners();
    calculateTotals();
    
    // Add event listeners for existing product selects
    document.querySelectorAll('.product-select').forEach(select => {
        select.addEventListener('change', function() {
            handleProductSelect(this);
        });
    });
    
    // Trigger payment terms check
    document.getElementById('payment_terms').dispatchEvent(new Event('change'));
});
</script>
@endpush 