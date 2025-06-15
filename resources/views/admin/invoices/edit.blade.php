@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Invoice</h1>
                <p class="text-gray-600 mt-2">Edit invoice {{ $invoice->invoice_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.invoices.show', $invoice) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    View Invoice
                </a>
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
    <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Invoice Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Invoice Type</label>
                                <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500" value="{{ ucfirst($invoice->type) }} Invoice" disabled>
                                <p class="mt-1 text-sm text-gray-500">Invoice type cannot be changed after creation.</p>
                            </div>
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                                <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="customer_name" name="customer_name" 
                                       value="{{ old('customer_name', $invoice->customer_name) }}" required>
                            </div>
                            <div>
                                <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">Invoice Date</label>
                                <input type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="invoice_date" name="invoice_date" 
                                       value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                            </div>
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                <input type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="due_date" name="due_date" 
                                       value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                            </div>
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                                <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number', $invoice->reference_number) }}">
                            </div>
                            <div>
                                <label for="po_number" class="block text-sm font-medium text-gray-700 mb-2">PO Number</label>
                                <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="po_number" name="po_number" 
                                       value="{{ old('po_number', $invoice->po_number) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">Billing Address</label>
                                <textarea class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="billing_address" name="billing_address" 
                                          rows="3" required>{{ old('billing_address', $invoice->billing_address) }}</textarea>
                            </div>
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Shipping Address</label>
                                <textarea class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="shipping_address" name="shipping_address" 
                                          rows="3">{{ old('shipping_address', $invoice->shipping_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Invoice Items
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                            <i class="fas fa-plus me-1"></i>Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">Drag</th>
                                        <th style="width: auto;">Item Details</th>
                                        <th style="width: 120px;">Quantity</th>
                                        <th style="width: 130px;">Rate</th>
                                        <th style="width: 130px;">Discount</th>
                                        <th style="width: 130px;">Amount</th>
                                        <th style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-items">
                                    @if($invoice->items->count() > 0)
                                        @foreach($invoice->items as $index => $item)
                                            <tr class="item-row" draggable="true">
                                                <td class="text-center">
                                                    <i class="fas fa-grip-vertical text-muted cursor-pointer drag-handle"></i>
                                                </td>
                                                <td>
                                                    <select name="items[{{ $index }}][product_id]" class="form-select form-select-sm product-select">
                                                        <option value="">Select a product</option>
                                                        @if(isset($products))
                                                            @foreach($products as $product)
                                                                <option value="{{ $product->id }}" 
                                                                        data-name="{{ $product->name }}"
                                                                        data-description="{{ $product->description }}"
                                                                        data-price="{{ $product->price_aed ?? $product->price }}">
                                                                    {{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <input type="hidden" name="items[{{ $index }}][item_description]" class="item-description" value="{{ $item->item_description }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" required
                                                           class="form-control form-control-sm quantity-input">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" required
                                                           class="form-control form-control-sm rate-input">
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01" name="items[{{ $index }}][discount_percentage]" value="{{ $item->discount_percentage }}" min="0" max="100"
                                                               class="form-control discount-input">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <span class="line-total fw-medium">{{ number_format($item->line_total, 2) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" onclick="removeItem(this)" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Invoice Totals -->
                        <div class="row justify-content-end mt-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Sub Total:</span>
                                            <span id="sub-total">{{ number_format($invoice->sub_total, 2) }} AED</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tax:</span>
                                            <span id="tax-amount">{{ number_format($invoice->tax_amount, 2) }} AED</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total:</strong>
                                            <strong id="total-amount">{{ number_format($invoice->total_amount, 2) }} AED</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Additional Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3">{{ old('description', $invoice->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                                <textarea class="form-control" id="terms_conditions" name="terms_conditions" 
                                          rows="3">{{ old('terms_conditions', $invoice->terms_conditions) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" 
                                          rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Payment Terms -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Payment Terms</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                            <select name="payment_terms" id="payment_terms" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="advance_50" {{ old('payment_terms', $invoice->payment_terms) == 'advance_50' ? 'selected' : '' }}>50% Advance Payment</option>
                                <option value="advance_100" {{ old('payment_terms', $invoice->payment_terms) == 'advance_100' ? 'selected' : '' }}>100% Advance Payment</option>
                                <option value="on_delivery" {{ old('payment_terms', $invoice->payment_terms) == 'on_delivery' ? 'selected' : '' }}>Payment on Delivery</option>
                                <option value="net_30" {{ old('payment_terms', $invoice->payment_terms) == 'net_30' ? 'selected' : '' }}>Net 30 Days</option>
                                <option value="custom" {{ old('payment_terms', $invoice->payment_terms) == 'custom' ? 'selected' : '' }}>Custom Terms</option>
                            </select>
                        </div>
                        <div class="mb-4 {{ old('payment_terms', $invoice->payment_terms) == 'custom' ? '' : 'hidden' }}" id="custom-percentage">
                            <label for="advance_percentage" class="block text-sm font-medium text-gray-700 mb-2">Advance Percentage</label>
                            <input type="number" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="advance_percentage" 
                                   name="advance_percentage" min="0" max="100" step="0.01" 
                                   value="{{ old('advance_percentage', $invoice->advance_percentage) }}">
                        </div>
                    </div>
                </div>

                <!-- Invoice Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Invoice Status</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">Current Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">Created:</span>
                                <span>{{ $invoice->created_at->format('d M Y') }}</span>
                            </div>
                            @if($invoice->payments()->exists())
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">Paid Amount:</span>
                                <span>{{ number_format($invoice->paid_amount, 2) }} AED</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                </svg>
                                Update Invoice
                            </button>
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Item count data -->
<script type="application/json" id="itemCountData">
    @json($invoice->items->count() > 0 ? $invoice->items->count() : 1)
</script>

<script>
let itemIndex = 1;
try {
    const dataScript = document.getElementById('itemCountData');
    if (dataScript) {
        itemIndex = JSON.parse(dataScript.textContent);
    }
} catch (e) {
    console.error('Error parsing item count data:', e);
    itemIndex = 1;
}

// Payment terms handling
document.addEventListener('DOMContentLoaded', function() {
    const paymentTermsSelect = document.getElementById('payment_terms');
    const customDiv = document.getElementById('custom-percentage');
    
    function toggleCustomPercentage() {
        if (paymentTermsSelect.value === 'custom') {
            customDiv.classList.remove('hidden');
        } else {
            customDiv.classList.add('hidden');
        }
    }
    
    // Initial state
    toggleCustomPercentage();
    
    // Handle changes
    paymentTermsSelect.addEventListener('change', toggleCustomPercentage);
});

// Add new item
function addItem() {
    const tbody = document.getElementById('invoice-items');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.draggable = true;
    
    row.innerHTML = `
        <td class="text-center">
            <i class="fas fa-grip-vertical text-muted cursor-pointer drag-handle"></i>
        </td>
        <td>
            <select name="items[${itemIndex}][product_id]" class="form-select form-select-sm product-select">
                <option value="">Select a product</option>
            </select>
            <input type="hidden" name="items[${itemIndex}][item_description]" class="item-description" value="">
        </td>
        <td>
            <input type="number" step="0.01" name="items[${itemIndex}][quantity]" value="1.00" required
                   class="form-control form-control-sm quantity-input">
        </td>
        <td>
            <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" value="0.00" required
                   class="form-control form-control-sm rate-input">
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="0.01" name="items[${itemIndex}][discount_percentage]" value="0" min="0" max="100"
                       class="form-control discount-input">
                <span class="input-group-text">%</span>
            </div>
        </td>
        <td class="text-end">
            <span class="line-total fw-medium">0.00</span>
        </td>
        <td class="text-center">
            <button type="button" onclick="removeItem(this)" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    itemIndex++;
    
    // Add event listeners to new inputs
    addCalculationListeners(row);
    
    // Initialize Select2 for new product select
    const productSelect = row.querySelector('.product-select');
    if (productSelect && typeof window.initializeProductSelect === 'function') {
        // Populate product options from existing select
        const existingSelect = document.querySelector('.product-select');
        if (existingSelect && existingSelect !== productSelect) {
            const options = existingSelect.innerHTML;
            productSelect.innerHTML = options;
        }
        
        // Initialize Select2
        setTimeout(() => {
            window.initializeProductSelect(productSelect);
            
            // Handle product selection
            if (typeof jQuery !== 'undefined') {
                jQuery(productSelect).on('select2:select', function(e) {
                    const selectedData = e.params.data;
                    const selectedElement = selectedData.element;
                    const itemRow = this.closest('tr');
                    
                    if (selectedElement) {
                        const productName = jQuery(selectedElement).data('name') || '';
                        const productPrice = jQuery(selectedElement).data('price') || 0;
                        
                        // Set the item details
                        const descriptionField = itemRow.querySelector('.item-description');
                        const priceField = itemRow.querySelector('.rate-input');
                        
                        if (descriptionField) {
                            descriptionField.value = productName;
                        }
                        
                        if (priceField) {
                            priceField.value = productPrice;
                        }
                        
                        // Recalculate totals
                        calculateLineTotal(itemRow);
                        calculateTotals();
                    }
                });
                
                jQuery(productSelect).on('select2:clear', function(e) {
                    const itemRow = this.closest('tr');
                    itemRow.querySelector('.item-description').value = '';
                    itemRow.querySelector('.rate-input').value = 0;
                    calculateLineTotal(itemRow);
                    calculateTotals();
                });
            }
        }, 100);
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
    const inputs = container.querySelectorAll('.quantity-input, .rate-input, .discount-input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            calculateLineTotal(this.closest('tr'));
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

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    addCalculationListeners();
    calculateTotals();
    
    // Trigger payment terms check
    document.getElementById('payment_terms').dispatchEvent(new Event('change'));
});
</script>

<style>
    .item-row {
        transition: all 0.3s ease;
    }
    
    .item-row:hover {
        background-color: #f9fafb;
    }
    
    .hidden {
        display: none;
    }
</style>
@endsection