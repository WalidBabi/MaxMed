@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Invoice {{ $invoice->invoice_number }}</h2>
            <p class="text-muted mb-0">
                {{ ucfirst($invoice->type) }} Invoice - {{ $invoice->status_options[$invoice->status] ?? ucfirst($invoice->status) }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-outline-info" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sendEmailModal">
                <i class="fas fa-envelope me-2"></i>Send Email
            </button>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Invoice Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">{{ $invoice->invoice_number }}</h4>
                            <div class="mb-2">
                                <span class="badge {{ $invoice->type === 'proforma' ? 'bg-info' : 'bg-success' }} fs-6">
                                    {{ ucfirst($invoice->type) }} Invoice
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="mb-1">{{ $invoice->customer_name }}</h5>
                            <div class="text-muted small">
                                {{ $invoice->billing_address }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Invoice Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Invoice Date</label>
                            <div class="fw-medium">{{ $invoice->invoice_date->format('F j, Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Due Date</label>
                            <div class="fw-medium">{{ $invoice->due_date->format('F j, Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Payment Terms</label>
                            <div class="fw-medium">{{ $invoice::PAYMENT_TERMS[$invoice->payment_terms] ?? ucfirst($invoice->payment_terms) }}</div>
                        </div>
                        @if($invoice->reference_number)
                        <div class="col-md-4">
                            <label class="text-muted small">Reference Number</label>
                            <div class="fw-medium">{{ $invoice->reference_number }}</div>
                        </div>
                        @endif
                        @if($invoice->po_number)
                        <div class="col-md-4">
                            <label class="text-muted small">PO Number</label>
                            <div class="fw-medium">{{ $invoice->po_number }}</div>
                        </div>
                        @endif
                        @if($invoice->quote)
                        <div class="col-md-4">
                            <label class="text-muted small">Related Quote</label>
                            <div class="fw-medium">
                                <a href="{{ route('admin.quotes.show', $invoice->quote) }}" class="text-decoration-none">
                                    {{ $invoice->quote->quote_number }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>Invoice Items
                    </h6>
                </div>
                <div class="card-body">
                    @if($invoice->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Description</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-center">Discount</th>
                                        <th class="text-end">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->item_description }}
                                                @if($item->specifications)
                                                    <br><small class="text-muted">{{ $item->specifications }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($item->quantity, 2) }}
                                                @if($item->unit_of_measure)
                                                    {{ $item->unit_of_measure }}
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $item->formatted_unit_price }} {{ $invoice->currency }}</td>
                                            <td class="text-center">{{ number_format($item->discount_percentage, 2) }}%</td>
                                            <td class="text-end fw-medium">{{ $item->formatted_line_total }} {{ $invoice->currency }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row justify-content-end mt-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between py-2">
                                    <span class="fw-medium">Sub Total:</span>
                                    <span class="fw-bold">{{ $invoice->formatted_total }} {{ $invoice->currency }}</span>
                                </div>
                                @if($invoice->tax_amount > 0)
                                <div class="d-flex justify-content-between py-2">
                                    <span class="fw-medium">Tax:</span>
                                    <span class="fw-bold">{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</span>
                                </div>
                                @endif
                                @if($invoice->discount_amount > 0)
                                <div class="d-flex justify-content-between py-2">
                                    <span class="fw-medium">Discount:</span>
                                    <span class="fw-bold">-{{ number_format($invoice->discount_amount, 2) }} {{ $invoice->currency }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between py-2 border-top">
                                    <span class="fw-medium fs-5">Total:</span>
                                    <span class="fw-bold fs-5 text-primary">{{ $invoice->formatted_total }} {{ $invoice->currency }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="icon-shape icon-lg bg-soft-secondary text-secondary rounded-3 mx-auto mb-3">
                                <i class="fas fa-list fa-2x"></i>
                            </div>
                            <h6>No Items Added</h6>
                            <p class="text-muted mb-0">This invoice doesn't have any items yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            @if($invoice->payments->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Payment History
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Payment #</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td class="fw-medium">{{ $payment->payment_number }}</td>
                                        <td>{{ $payment->payment_date->format('M j, Y') }}</td>
                                        <td>{{ $payment::PAYMENT_METHODS[$payment->payment_method] ?? ucfirst($payment->payment_method) }}</td>
                                        <td>{{ $payment->formatted_amount }} {{ $payment->currency }}</td>
                                        <td><span class="{{ $payment->status_badge_class }}">{{ ucfirst($payment->status) }}</span></td>
                                        <td>{{ $payment->transaction_reference ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Additional Information -->
            @if($invoice->description || $invoice->terms_conditions || $invoice->notes)
            <div class="row">
                @if($invoice->description)
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-align-left me-2"></i>Description
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-muted">{{ $invoice->description }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($invoice->terms_conditions)
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-file-contract me-2"></i>Terms & Conditions
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-muted small">{{ $invoice->terms_conditions }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($invoice->notes)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-sticky-note me-2"></i>Notes
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-muted">{{ $invoice->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Invoice Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Invoice Status
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.invoices.status.update', $invoice) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select name="status" class="form-select" required>
                                @foreach($invoice::STATUS_OPTIONS as $value => $label)
                                    <option value="{{ $value }}" {{ $invoice->status == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Payment Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-12">
                            <div class="border-bottom pb-2 mb-2">
                                <h4 class="mb-0 text-primary">{{ $invoice->formatted_total }}</h4>
                                <small class="text-muted">Total Amount ({{ $invoice->currency }})</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-0 text-success">{{ $invoice->formatted_paid_amount }}</h6>
                            <small class="text-muted">Paid</small>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-0 text-warning">{{ $invoice->formatted_remaining_amount }}</h6>
                            <small class="text-muted">Remaining</small>
                        </div>
                        <div class="col-12">
                            <span class="{{ $invoice->payment_status_badge_class }}">
                                {{ $invoice::PAYMENT_STATUS[$invoice->payment_status] ?? ucfirst($invoice->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Record Payment -->
            @if($invoice->payment_status !== 'paid')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Record Payment
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.invoices.record-payment', $invoice) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" max="{{ $invoice->getRemainingAmount() }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                @foreach(\App\Models\Payment::PAYMENT_METHODS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_reference" class="form-label">Transaction Reference</label>
                            <input type="text" class="form-control" id="transaction_reference" name="transaction_reference">
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-credit-card me-2"></i>Record Payment
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Convert Actions -->
            @if($invoice->type === 'proforma' && $invoice->canConvertToFinalInvoice())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>Convert to Final
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Convert this proforma invoice to a final invoice when ready to deliver.</p>
                    <form action="{{ route('admin.invoices.convert-to-final', $invoice) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to convert this to a final invoice?')">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-exchange-alt me-2"></i>Convert to Final Invoice
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Related Invoices -->
            @if($invoice->parentInvoice || $invoice->childInvoices->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-link me-2"></i>Related Invoices
                    </h6>
                </div>
                <div class="card-body">
                    @if($invoice->parentInvoice)
                        <div class="mb-2">
                            <strong>Parent:</strong>
                            <a href="{{ route('admin.invoices.show', $invoice->parentInvoice) }}" class="text-decoration-none">
                                {{ $invoice->parentInvoice->invoice_number }}
                            </a>
                        </div>
                    @endif
                    @foreach($invoice->childInvoices as $childInvoice)
                        <div class="mb-2">
                            <strong>Child:</strong>
                            <a href="{{ route('admin.invoices.show', $childInvoice) }}" class="text-decoration-none">
                                {{ $childInvoice->invoice_number }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Actions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Invoice
                        </a>
                        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-outline-info">
                            <i class="fas fa-file-pdf me-2"></i>Download PDF
                        </a>
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#sendEmailModal">
                            <i class="fas fa-envelope me-2"></i>Send Email
                        </button>

                        @if($invoice->type === 'proforma' && $invoice->status === 'confirmed')
                            <!-- Create Order & Delivery -->
                            @if(!$invoice->order_id)
                                <div class="border-top pt-3 mt-3">
                                    <h6 class="text-muted mb-2">Order & Delivery</h6>
                                    <form action="{{ route('admin.invoices.create-order', $invoice) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100" 
                                                onclick="return confirm('Create an order from this proforma invoice?')">
                                            <i class="fas fa-shopping-cart me-2"></i>Create Order
                                        </button>
                                    </form>
                                    <small class="text-muted">Create an order to enable delivery tracking</small>
                                </div>
                            @else
                                <!-- Order exists, check for delivery -->
                                @if(!$invoice->order->hasDelivery())
                                    <div class="border-top pt-3 mt-3">
                                        <h6 class="text-muted mb-2">Delivery</h6>
                                        <a href="{{ route('admin.deliveries.create', ['order' => $invoice->order_id]) }}" 
                                           class="btn btn-info w-100">
                                            <i class="fas fa-truck me-2"></i>Create Delivery
                                        </a>
                                        <small class="text-muted">Create delivery record for order #{{ $invoice->order_id }}</small>
                                    </div>
                                @else
                                    <!-- Delivery exists -->
                                    <div class="border-top pt-3 mt-3">
                                        <h6 class="text-muted mb-2">Delivery</h6>
                                        <a href="{{ route('admin.deliveries.show', $invoice->order->delivery) }}" 
                                           class="btn btn-success w-100">
                                            <i class="fas fa-eye me-2"></i>View Delivery
                                        </a>
                                        <small class="text-muted">Delivery #{{ $invoice->order->delivery->id }}</small>
                                    </div>
                                @endif
                            @endif
                        @endif

                        @if($invoice->type === 'final' && $invoice->delivery_id)
                            <div class="border-top pt-3 mt-3">
                                <h6 class="text-muted mb-2">Delivery</h6>
                                <a href="{{ route('admin.deliveries.show', $invoice->delivery_id) }}" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-truck me-2"></i>View Delivery
                                </a>
                                <small class="text-muted">Linked to delivery #{{ $invoice->delivery_id }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Link to Existing Delivery -->
            @if($invoice->type === 'proforma' && !$invoice->delivery_id)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-link me-2"></i>Link to Delivery
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.invoices.link-delivery', $invoice) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="delivery_id" class="form-label">Select Delivery</label>
                            <select name="delivery_id" id="delivery_id" class="form-select" required>
                                <option value="">Choose a delivery...</option>
                                @foreach(\App\Models\Delivery::with('order')->whereNull('id')->orWhereNotIn('id', \App\Models\Invoice::whereNotNull('delivery_id')->pluck('delivery_id'))->get() as $delivery)
                                    <option value="{{ $delivery->id }}">
                                        Delivery #{{ $delivery->id }} - Order #{{ $delivery->order_id }} 
                                        ({{ $delivery->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-link me-2"></i>Link to Delivery
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Automation Workflow Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Automation Workflow
                    </h6>
                </div>
                <div class="card-body">
                    <div class="workflow-steps">
                        <!-- Step 1: Proforma Invoice -->
                        <div class="step {{ $invoice->type === 'proforma' ? 'active' : 'completed' }}">
                            <div class="step-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="step-content">
                                <h6>Proforma Invoice</h6>
                                <small class="text-muted">{{ $invoice->invoice_number }}</small>
                                @if($invoice->type === 'proforma')
                                    <div class="mt-1">
                                        <span class="badge bg-{{ $invoice->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($invoice->payment_status) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Step 2: Order Creation -->
                        <div class="step {{ $invoice->order_id ? 'completed' : ($invoice->payment_status === 'paid' ? 'pending' : 'disabled') }}">
                            <div class="step-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="step-content">
                                <h6>Order Creation</h6>
                                @if($invoice->order_id)
                                    <small class="text-success">
                                        <a href="{{ route('admin.orders.show', $invoice->order_id) }}">Order #{{ $invoice->order_id }}</a>
                                    </small>
                                    <div class="mt-1">
                                        <span class="badge bg-info">{{ ucfirst($invoice->order->status ?? 'N/A') }}</span>
                                    </div>
                                @else
                                    <small class="text-muted">
                                        {{ $invoice->payment_status === 'paid' ? 'Will auto-create when payment confirmed' : 'Waiting for payment' }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        <!-- Step 3: Delivery -->
                        @php
                            $hasDelivery = $invoice->order_id && $invoice->order->hasDelivery();
                            $delivery = $hasDelivery ? $invoice->order->delivery : null;
                        @endphp
                        <div class="step {{ $hasDelivery ? 'completed' : ($invoice->order_id && $invoice->order->status === 'shipped' ? 'pending' : 'disabled') }}">
                            <div class="step-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="step-content">
                                <h6>Delivery</h6>
                                @if($hasDelivery)
                                    <small class="text-success">
                                        <a href="{{ route('admin.deliveries.show', $delivery) }}">Delivery #{{ $delivery->id }}</a>
                                    </small>
                                    <div class="mt-1">
                                        <span class="badge bg-{{ $delivery->status === 'delivered' ? 'success' : 'primary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                        </span>
                                    </div>
                                @else
                                    <small class="text-muted">
                                        {{ $invoice->order_id ? 'Will auto-create when order shipped' : 'Waiting for order' }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        <!-- Step 4: Final Invoice -->
                        @php
                            $finalInvoice = $invoice->childInvoices()->where('type', 'final')->first();
                        @endphp
                        <div class="step {{ $finalInvoice ? 'completed' : ($hasDelivery && $delivery->status === 'delivered' ? 'pending' : 'disabled') }}">
                            <div class="step-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="step-content">
                                <h6>Final Invoice</h6>
                                @if($finalInvoice)
                                    <small class="text-success">
                                        <a href="{{ route('admin.invoices.show', $finalInvoice) }}">{{ $finalInvoice->invoice_number }}</a>
                                    </small>
                                    <div class="mt-1">
                                        <span class="badge bg-success">Completed</span>
                                    </div>
                                @else
                                    <small class="text-muted">
                                        {{ $hasDelivery ? 'Will auto-create when delivered' : 'Waiting for delivery' }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Automation Status -->
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-cogs me-1"></i>Automation Status
                        </h6>
                        @if($invoice->type === 'proforma' && $invoice->payment_status !== 'paid')
                            <div class="text-warning">
                                <i class="fas fa-clock me-1"></i>
                                Waiting for payment to trigger order creation
                            </div>
                        @elseif($invoice->order_id && !$hasDelivery)
                            <div class="text-info">
                                <i class="fas fa-shipping-fast me-1"></i>
                                Order created. Delivery will auto-create when shipped
                            </div>
                        @elseif($hasDelivery && !$finalInvoice)
                            <div class="text-primary">
                                <i class="fas fa-truck me-1"></i>
                                Delivery in progress. Final invoice will auto-create when delivered
                            </div>
                        @elseif($finalInvoice)
                            <div class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Workflow completed! Final invoice generated automatically
                            </div>
                        @else
                            <div class="text-secondary">
                                <i class="fas fa-play me-1"></i>
                                Automation ready to begin
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendEmailModalLabel">Send Invoice Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.invoices.send-email', $invoice) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="to_email" class="form-label">To Email Address</label>
                        <input type="email" class="form-control" id="to_email" name="to_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="cc_emails" class="form-label">CC Email Addresses (comma separated)</label>
                        <input type="text" class="form-control" id="cc_emails" name="cc_emails" 
                               placeholder="email1@example.com, email2@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               value="{{ $invoice->type === 'proforma' ? 'Proforma Invoice' : 'Invoice' }} {{ $invoice->invoice_number }}">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" 
                                  placeholder="Enter additional message (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-shape.icon-lg {
        width: 4rem;
        height: 4rem;
    }
    
    .bg-soft-secondary {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }

    .workflow-steps {
        position: relative;
    }

    .step {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .step:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 20px;
        top: 40px;
        width: 2px;
        height: calc(100% + 0.5rem);
        background-color: #dee2e6;
        z-index: 1;
    }

    .step.completed:not(:last-child)::after {
        background-color: #28a745;
    }

    .step.pending:not(:last-child)::after {
        background-color: #ffc107;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        position: relative;
        z-index: 2;
        font-size: 16px;
    }

    .step.active .step-icon {
        background-color: #007bff;
        color: white;
        box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
    }

    .step.completed .step-icon {
        background-color: #28a745;
        color: white;
    }

    .step.pending .step-icon {
        background-color: #ffc107;
        color: #212529;
    }

    .step.disabled .step-icon {
        background-color: #e9ecef;
        color: #6c757d;
    }

    .step-content {
        flex: 1;
        padding-top: 0.25rem;
    }

    .step-content h6 {
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .step.disabled .step-content {
        opacity: 0.6;
    }

    .step.disabled .step-content h6 {
        color: #6c757d;
    }

    /* Animation for active steps */
    .step.active .step-icon {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
        }
    }

    .step.pending .step-icon {
        animation: pulse-warning 2s infinite;
    }

    @keyframes pulse-warning {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
        }
    }
</style>
@endsection 