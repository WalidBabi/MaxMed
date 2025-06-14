@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Create Invoice</h2>
            <p class="text-muted mb-0">Create a new proforma or final invoice</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Invoices
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Invoice Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Invoice Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="proforma" {{ old('type', 'proforma') == 'proforma' ? 'selected' : '' }}>Proforma Invoice</option>
                                    <option value="final" {{ old('type') == 'final' ? 'selected' : '' }}>Final Invoice</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                       value="{{ old('customer_name', $quote->customer_name ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" id="invoice_date" name="invoice_date" 
                                       value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                       value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="reference_number" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number', $quote->reference_number ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="po_number" class="form-label">PO Number</label>
                                <input type="text" class="form-control" id="po_number" name="po_number" 
                                       value="{{ old('po_number') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-user me-2"></i>Customer Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="billing_address" class="form-label">Billing Address</label>
                                <textarea class="form-control" id="billing_address" name="billing_address" 
                                          rows="3" required>{{ old('billing_address') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="shipping_address" class="form-label">Shipping Address</label>
                                <textarea class="form-control" id="shipping_address" name="shipping_address" 
                                          rows="3">{{ old('shipping_address') }}</textarea>
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
                        <div id="invoice-items">
                            @if($quote && $quote->items->count() > 0)
                                @foreach($quote->items as $index => $item)
                                    <div class="item-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Item Description</label>
                                                <textarea name="items[{{ $index }}][item_description]" class="form-control" 
                                                          rows="2" required>{{ $item->item_details }}</textarea>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" name="items[{{ $index }}][quantity]" 
                                                       class="form-control quantity-input" step="0.01" 
                                                       value="{{ $item->quantity }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Unit Price</label>
                                                <input type="number" name="items[{{ $index }}][unit_price]" 
                                                       class="form-control price-input" step="0.01" 
                                                       value="{{ $item->rate }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Discount %</label>
                                                <input type="number" name="items[{{ $index }}][discount_percentage]" 
                                                       class="form-control discount-input" step="0.01" min="0" max="100"
                                                       value="{{ $item->discount }}">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="item-total">
                                                <strong>Line Total: <span class="line-total">{{ number_format($item->amount, 2) }}</span> AED</strong>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="item-row border rounded p-3 mb-3" data-index="0">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Item Description</label>
                                            <textarea name="items[0][item_description]" class="form-control" 
                                                      rows="2" required placeholder="Enter item description"></textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" name="items[0][quantity]" 
                                                   class="form-control quantity-input" step="0.01" 
                                                   value="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit Price</label>
                                            <input type="number" name="items[0][unit_price]" 
                                                   class="form-control price-input" step="0.01" 
                                                   value="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Discount %</label>
                                            <input type="number" name="items[0][discount_percentage]" 
                                                   class="form-control discount-input" step="0.01" min="0" max="100"
                                                   value="0">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="item-total">
                                            <strong>Line Total: <span class="line-total">0.00</span> AED</strong>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Invoice Totals -->
                        <div class="row justify-content-end mt-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Sub Total:</span>
                                            <span id="sub-total">0.00 AED</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tax:</span>
                                            <span id="tax-amount">0.00 AED</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total:</strong>
                                            <strong id="total-amount">0.00 AED</strong>
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
                                          rows="3">{{ old('description', $quote->subject ?? '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                                <textarea class="form-control" id="terms_conditions" name="terms_conditions" 
                                          rows="3">{{ old('terms_conditions', $quote->terms_conditions ?? '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" 
                                          rows="3">{{ old('notes', $quote->customer_notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Payment Terms -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Terms
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="payment_terms" class="form-label">Payment Terms</label>
                            <select name="payment_terms" id="payment_terms" class="form-select" required>
                                <option value="advance_50" {{ old('payment_terms', 'advance_50') == 'advance_50' ? 'selected' : '' }}>50% Advance Payment</option>
                                <option value="advance_100" {{ old('payment_terms') == 'advance_100' ? 'selected' : '' }}>100% Advance Payment</option>
                                <option value="on_delivery" {{ old('payment_terms') == 'on_delivery' ? 'selected' : '' }}>Payment on Delivery</option>
                                <option value="net_30" {{ old('payment_terms') == 'net_30' ? 'selected' : '' }}>Net 30 Days</option>
                                <option value="custom" {{ old('payment_terms') == 'custom' ? 'selected' : '' }}>Custom Terms</option>
                            </select>
                        </div>
                        <div class="mb-3" id="custom-percentage" style="display: none;">
                            <label for="advance_percentage" class="form-label">Advance Percentage</label>
                            <input type="number" class="form-control" id="advance_percentage" 
                                   name="advance_percentage" min="0" max="100" step="0.01" 
                                   value="{{ old('advance_percentage') }}">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" name="status" value="draft" class="btn btn-secondary">
                                <i class="fas fa-save me-2"></i>Save as Draft
                            </button>
                            <button type="submit" name="status" value="sent" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Create & Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = {{ $quote && $quote->items->count() > 0 ? $quote->items->count() : 1 }};

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
    const container = document.getElementById('invoice-items');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'item-row border rounded p-3 mb-3';
    itemDiv.setAttribute('data-index', itemIndex);
    
    itemDiv.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Item Description</label>
                <textarea name="items[${itemIndex}][item_description]" class="form-control" 
                          rows="2" required placeholder="Enter item description"></textarea>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantity</label>
                <input type="number" name="items[${itemIndex}][quantity]" 
                       class="form-control quantity-input" step="0.01" 
                       value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit Price</label>
                <input type="number" name="items[${itemIndex}][unit_price]" 
                       class="form-control price-input" step="0.01" 
                       value="0" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Discount %</label>
                <input type="number" name="items[${itemIndex}][discount_percentage]" 
                       class="form-control discount-input" step="0.01" min="0" max="100"
                       value="0">
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="item-total">
                <strong>Line Total: <span class="line-total">0.00</span> AED</strong>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(itemDiv);
    itemIndex++;
    
    // Add event listeners to new inputs
    addCalculationListeners(itemDiv);
}

// Remove item
function removeItem(button) {
    const container = document.getElementById('invoice-items');
    if (container.children.length > 1) {
        button.closest('.item-row').remove();
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
    const price = parseFloat(itemRow.querySelector('.price-input').value) || 0;
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
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .item-row:hover {
        background-color: #e9ecef;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endsection 