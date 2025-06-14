@extends('admin.layouts.app')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">New Quote</h2>
            <p class="text-muted mb-0">Create a new quote for customers</p>
        </div>
        <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Quotes
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading">Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="quoteForm" action="{{ route('admin.quotes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Quote Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                <select id="customer_id" name="customer_id" required 
                                        class="form-select @error('customer_id') is-invalid @enderror">
                                    <option value="">Select a customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}{{ $customer->company_name ? ' - ' . $customer->company_name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="quote_number" class="form-label">Quote Number</label>
                                <input type="text" id="quote_number" 
                                       value="{{ \App\Models\Quote::generateQuoteNumber() }}" readonly
                                       class="form-control bg-light">
                                <small class="form-text text-muted">Auto-generated</small>
                            </div>

                            <div class="col-md-6">
                                <label for="reference_number" class="form-label">Reference Number</label>
                                <input type="text" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number') }}"
                                       class="form-control @error('reference_number') is-invalid @enderror">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="quote_date" class="form-label">Quote Date <span class="text-danger">*</span></label>
                                <input type="date" id="quote_date" name="quote_date" 
                                       value="{{ old('quote_date', date('Y-m-d')) }}" required
                                       class="form-control @error('quote_date') is-invalid @enderror">
                                @error('quote_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" id="expiry_date" name="expiry_date" 
                                       value="{{ old('expiry_date', date('Y-m-d', strtotime('+30 days'))) }}" required
                                       class="form-control @error('expiry_date') is-invalid @enderror">
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="salesperson" class="form-label">Salesperson</label>
                                <input type="text" id="salesperson" name="salesperson" 
                                       value="{{ old('salesperson') }}"
                                       class="form-control @error('salesperson') is-invalid @enderror">
                                @error('salesperson')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" id="subject" name="subject" 
                                       value="{{ old('subject') }}"
                                       placeholder="Let your customer know what this Quote is for"
                                       class="form-control @error('subject') is-invalid @enderror">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Table -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Item Table
                        </h6>
                        <button type="button" id="addItem" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Item
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
                                <tbody id="itemsTable">
                                    <!-- Items will be added dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <div class="row justify-content-end mt-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between py-2">
                                    <span class="fw-medium">Sub Total:</span>
                                    <span id="subTotal" class="fw-bold">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-top">
                                    <span class="fw-medium fs-5">Total (AED):</span>
                                    <span id="totalAmount" class="fw-bold fs-5 text-primary">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Notes -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Customer Notes
                        </h6>
                    </div>
                    <div class="card-body">
                        <textarea id="customer_notes" name="customer_notes" rows="3"
                                  placeholder="Looking forward for your business."
                                  class="form-control @error('customer_notes') is-invalid @enderror">{{ old('customer_notes', 'Looking forward for your business.') }}</textarea>
                        @error('customer_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-file-contract me-2"></i>Terms & Conditions
                        </h6>
                    </div>
                    <div class="card-body">
                        <textarea id="terms_conditions" name="terms_conditions" rows="4"
                                  placeholder="Enter the terms and conditions of your business to be displayed in your transaction"
                                  class="form-control @error('terms_conditions') is-invalid @enderror">{{ old('terms_conditions') }}</textarea>
                        @error('terms_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Attachments -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-upload me-2"></i>Attach File(s) to Quote
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="upload-area border-2 border-dashed rounded p-4 text-center">
                            <input type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="d-none">
                            <label for="attachments" class="cursor-pointer d-block">
                                <i class="fas fa-cloud-upload-alt fs-1 text-muted mb-3 d-block"></i>
                                <p class="mb-1">Click to upload files or drag and drop</p>
                                <small class="text-muted">You can upload a maximum of 5 files, 10MB each</small>
                            </label>
                        </div>
                        <div id="fileList" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status and Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-cog me-2"></i>Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="status" value="draft">
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" onclick="return validateForm()">
                                <i class="fas fa-save me-2"></i>Save Quote
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>Quick Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small text-muted">
                            <p class="mb-2"><strong>Customer:</strong> Select from existing customers list</p>
                            <p class="mb-2"><strong>Quote Date:</strong> Defaults to today's date</p>
                            <p class="mb-2"><strong>Expiry Date:</strong> Defaults to 30 days from quote date</p>
                            <p class="mb-0"><strong>Items:</strong> Add at least one item to create the quote</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .form-control:focus,
    .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .upload-area {
        border-color: #dee2e6 !important;
        transition: all 0.2s ease;
    }
    
    .upload-area:hover {
        border-color: #4f46e5 !important;
        background-color: rgba(79, 70, 229, 0.02);
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .item-row {
        cursor: grab;
    }
    
    .item-row:active {
        cursor: grabbing;
    }
    
    .drag-handle {
        cursor: grab;
    }
    
    /* Select2 Custom Styles */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(1.5em + 0.5rem + 2px);
        font-size: 0.875rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .select2-container--bootstrap-5 .select2-selection:focus-within {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    
    .select2-container--bootstrap-5.select2-container--small .select2-selection {
        min-height: calc(1.5em + 0.25rem + 2px);
        font-size: 0.875rem;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 12px;
        padding-right: 20px;
        color: #495057;
        line-height: 1.5;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }
    
    /* Hide Select2's custom arrow and keep Bootstrap's arrow */
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        display: none !important;
    }
    
    /* Ensure Bootstrap form-select arrow shows through */
    .select2-container--bootstrap-5 .select2-selection--single {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
        padding-right: 2.5rem !important;
    }
    
    .select2-dropdown {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        background-color: #fff;
        z-index: 1056;
    }
    
    .select2-search--dropdown {
        padding: 8px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .select2-search__field {
        width: 100% !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        padding: 8px 12px !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        color: #495057 !important;
        background-color: #fff !important;
        background-image: none !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
    }
    
    .select2-search__field:focus {
        border-color: #4f46e5 !important;
        outline: 0 !important;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25) !important;
    }
    
    .select2-search__field::placeholder {
        color: #6c757d !important;
        opacity: 1 !important;
    }
    
    .select2-results__options {
        max-height: 300px;
    }
    
    .select2-results__option {
        padding: 10px 12px;
        line-height: 1.5;
        color: #495057;
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    
    .select2-results__option:hover,
    .select2-results__option--highlighted {
        background-color: #f8f9fa !important;
        color: #495057 !important;
    }
    
    .select2-results__option--selected {
        background-color: #4f46e5 !important;
        color: #fff !important;
    }
    
    .select2-results__option[aria-selected="true"]:not(.select2-results__option--highlighted) {
        background-color: #e7e7ff;
        color: #4f46e5;
    }
    
    .select2-result-item {
        padding: 2px 0;
    }
    
    .select2-result-item .fw-medium {
        font-weight: 500;
        color: #212529;
        margin-bottom: 2px;
    }
    
    .select2-result-item .text-muted {
        font-size: 0.8rem;
        color: #6c757d !important;
        margin-bottom: 2px;
    }
    
    .select2-result-item .text-success {
        font-size: 0.8rem;
        font-weight: 500;
        color: #198754 !important;
    }
    
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    

    
    /* Loading state */
    .select2-results__option.loading-results {
        text-align: center;
        padding: 20px;
        color: #6c757d;
    }
    
    /* No results */
    .select2-results__option--no-results {
        text-align: center;
        padding: 20px;
        color: #6c757d;
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
    
    .items-table .form-control,
    .items-table .form-select {
        min-width: 100px;
    }
</style>

<script>
let itemCounter = 0;

function addItem() {
    const tbody = document.getElementById('itemsTable');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.draggable = true;
    row.innerHTML = `
        <td class="text-center">
            <i class="fas fa-grip-vertical text-muted cursor-pointer drag-handle"></i>
        </td>
        <td>
            <select name="items[${itemCounter}][product_id]" class="form-select form-select-sm product-select" required>
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
        <td>
            <input type="number" step="0.01" name="items[${itemCounter}][quantity]" value="1.00" required
                   class="form-control form-control-sm quantity-input">
        </td>
        <td>
            <input type="number" step="0.01" name="items[${itemCounter}][rate]" value="0.00" required
                   class="form-control form-control-sm rate-input">
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="0.01" name="items[${itemCounter}][discount]" value="0" min="0" max="100"
                       class="form-control discount-input">
                <span class="input-group-text">%</span>
            </div>
        </td>
        <td class="text-end">
            <span class="amount-display fw-medium">0.00</span>
        </td>
        <td class="text-center">
            <button type="button" onclick="removeItem(this)" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-times"></i>
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
            const productDescription = selectedOption.getAttribute('data-description');
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
    
    // Initialize Select2 on the new product select with proper event handling
    setTimeout(function() {
        if (typeof window.initializeProductSelect === 'function') {
            window.initializeProductSelect(productSelect);
            
            // Use jQuery events for Select2
            if (typeof jQuery !== 'undefined') {
                jQuery(productSelect).on('select2:select', function(e) {
                    const selectedData = e.params.data;
                    const selectedElement = selectedData.element;
                    
                    if (selectedElement) {
                        const productName = jQuery(selectedElement).data('name') || '';
                        const productDescription = jQuery(selectedElement).data('description') || '';
                        const productPrice = jQuery(selectedElement).data('price') || 0;
                        
                        // Set the item details (name only)
                        itemDetailsHidden.value = productName;
                        
                        // Set the rate
                        rateInput.value = productPrice;
                        
                        // Trigger calculation
                        calculateRowAmount({ target: rateInput });
                    }
                });
                
                jQuery(productSelect).on('select2:clear', function(e) {
                    itemDetailsHidden.value = '';
                    rateInput.value = 0;
                    calculateRowAmount({ target: rateInput });
                });
            }
        }
    }, 100); // Small delay to ensure Select2 is loaded
    
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
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
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
            fileDiv.className = 'd-flex justify-content-between align-items-center bg-light p-2 rounded mt-2';
            fileDiv.innerHTML = `
                <span class="small">${file.name}</span>
                <span class="badge bg-secondary">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
            `;
            fileList.appendChild(fileDiv);
        }
    });
    

});
</script>

@push('scripts')
<script>
// Avoid conflicts with existing scripts
(function() {
    'use strict';
    
    // Load jQuery and Select2 dynamically to avoid conflicts
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;
        script.onload = callback;
        document.head.appendChild(script);
    }
    
    function loadCSS(href) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
    }
    
    // Load dependencies
    if (typeof jQuery === 'undefined') {
        loadScript('https://code.jquery.com/jquery-3.6.0.min.js', initializeSelect2System);
    } else {
        initializeSelect2System();
    }
    
    function initializeSelect2System() {
        // Load Select2 CSS files
        loadCSS('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        loadCSS('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css');
        
        // Load Select2 JS
        loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', function() {
            setupSelect2Functions();
        });
    }
    
    function setupSelect2Functions() {
        // Global function to initialize Select2 on product selects
        window.initializeProductSelect = function(element) {
            if (!element || !jQuery(element).length) return;
            
            const $element = jQuery(element);
            
            // Destroy existing Select2 if present
            if ($element.hasClass('select2-hidden-accessible')) {
                $element.select2('destroy');
            }
            
            $element.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Search and select a product...',
                allowClear: true,
                dropdownAutoWidth: true,
                escapeMarkup: function(markup) {
                    return markup; // Allow HTML in results
                },
                matcher: function(params, data) {
                    // If there are no search terms, return all data
                    if (jQuery.trim(params.term) === '') {
                        return data;
                    }
                    
                    // Skip if no text property
                    if (typeof data.text === 'undefined') {
                        return null;
                    }
                    
                    // Search in multiple fields
                    const searchTerm = params.term.toLowerCase();
                    const searchText = data.text.toLowerCase();
                    const productName = jQuery(data.element).data('name') ? jQuery(data.element).data('name').toString().toLowerCase() : '';
                    const productDescription = jQuery(data.element).data('description') ? jQuery(data.element).data('description').toString().toLowerCase() : '';
                    
                    const combinedText = searchText + ' ' + productName + ' ' + productDescription;
                    
                    if (combinedText.indexOf(searchTerm) > -1) {
                        return data;
                    }
                    
                    return null;
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }
                    
                    // Show detailed product info in dropdown
                    if (data.element) {
                        const $element = jQuery(data.element);
                        const productName = $element.data('name') || '';
                        const productDescription = $element.data('description') || '';
                        const productPrice = $element.data('price') || '';
                        
                        if (productName) {
                            let html = '<div class="select2-result-item">';
                            html += '<div class="fw-medium">' + productName + '</div>';
                            if (productDescription) {
                                html += '<div class="text-muted small">' + productDescription + '</div>';
                            }
                            if (productPrice) {
                                html += '<div class="text-success small">AED ' + parseFloat(productPrice).toFixed(2) + '</div>';
                            }
                            html += '</div>';
                            
                            return jQuery(html);
                        }
                    }
                    
                    return data.text;
                },
                templateSelection: function(data) {
                    if (data.element) {
                        const productName = jQuery(data.element).data('name');
                        if (productName) {
                            return productName;
                        }
                    }
                    return data.text;
                }
            });
        };
        
        // Initialize existing selects when DOM is ready
        jQuery(document).ready(function() {
            jQuery('.product-select').each(function() {
                window.initializeProductSelect(this);
            });
        });
    }
})();
</script>
@endpush

@endsection 