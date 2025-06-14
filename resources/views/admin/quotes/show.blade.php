@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Quote {{ $quote->quote_number }}</h2>
            <p class="text-muted mb-0">View and manage quote details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.quotes.edit', $quote) }}" class="btn btn-success">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.quotes.pdf', $quote) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </a>
            <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Quotes
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Quote Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Quote Information
                        </h6>
                        <div>
                            @if($quote->status === 'draft')
                                <span class="badge bg-soft-secondary text-secondary">Draft</span>
                            @elseif($quote->status === 'sent')
                                <span class="badge bg-soft-info text-info">Sent</span>
                            @elseif($quote->status === 'invoiced')
                                <span class="badge bg-soft-success text-success">Invoiced</span>
                            @else
                                <span class="badge bg-soft-warning text-warning">{{ ucfirst($quote->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Customer Name</label>
                            <p class="mb-0 fw-medium">{{ $quote->customer_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Quote Number</label>
                            <p class="mb-0 fw-medium font-monospace">{{ $quote->quote_number }}</p>
                        </div>
                        @if($quote->reference_number)
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Reference Number</label>
                            <p class="mb-0">{{ $quote->reference_number }}</p>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Quote Date</label>
                            <p class="mb-0">{{ $quote->quote_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Expiry Date</label>
                            <p class="mb-0">{{ $quote->expiry_date->format('d M Y') }}</p>
                        </div>
                        @if($quote->salesperson)
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Salesperson</label>
                            <p class="mb-0">{{ $quote->salesperson }}</p>
                        </div>
                        @endif
                        @if($quote->creator)
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Created By</label>
                            <p class="mb-0">{{ $quote->creator->name }}</p>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Created Date</label>
                            <p class="mb-0">{{ $quote->created_at->format('d M Y H:i') }}</p>
                        </div>
                        @if($quote->subject)
                        <div class="col-12">
                            <label class="form-label text-muted small">Subject</label>
                            <p class="mb-0">{{ $quote->subject }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>Items
                    </h6>
                </div>
                <div class="card-body">
                    @if($quote->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Details</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Rate (AED)</th>
                                        <th class="text-center">Discount</th>
                                        <th class="text-end">Amount (AED)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quote->items as $item)
                                        <tr>
                                            <td>{{ $item->item_details }}</td>
                                            <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                                            <td class="text-center">{{ number_format($item->discount, 2) }}%</td>
                                            <td class="text-end fw-medium">{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row justify-content-end mt-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between py-2">
                                    <span class="fw-medium">Sub Total:</span>
                                    <span class="fw-bold">{{ number_format($quote->sub_total, 2) }} AED</span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-top">
                                    <span class="fw-medium fs-5">Total:</span>
                                    <span class="fw-bold fs-5 text-primary">{{ number_format($quote->total_amount, 2) }} AED</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="icon-shape icon-lg bg-soft-secondary text-secondary rounded-3 mx-auto mb-3">
                                <i class="fas fa-list fa-2x"></i>
                            </div>
                            <h6>No Items Added</h6>
                            <p class="text-muted mb-0">This quote doesn't have any items yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Customer Notes -->
            @if($quote->customer_notes)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>Customer Notes
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted">{{ $quote->customer_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Terms & Conditions -->
            @if($quote->terms_conditions)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-file-contract me-2"></i>Terms & Conditions
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted small">{{ $quote->terms_conditions }}</p>
                </div>
            </div>
            @endif

            <!-- Attachments -->
            @if($quote->attachments && count($quote->attachments) > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-paperclip me-2"></i>Attachments
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($quote->attachments as $index => $attachment)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 border rounded">
                                    <div class="icon-shape icon-sm bg-soft-primary text-primary rounded me-3">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $attachment['name'] }}</h6>
                                        <small class="text-muted">Attachment {{ $index + 1 }}</small>
                                    </div>
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Update -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Update Status
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.quotes.status.update', $quote) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select name="status" class="form-select" required>
                                <option value="draft" {{ $quote->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ $quote->status == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="invoiced" {{ $quote->status == 'invoiced' ? 'selected' : '' }}>Invoiced</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Convert to Invoice -->
            @if($quote->status === 'sent' && !$quote->invoices()->exists())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Convert to Invoice
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Convert this quote to a proforma invoice when customer confirms the order.</p>
                    <form action="{{ route('admin.quotes.convert-to-invoice', $quote) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to convert this quote to a proforma invoice?')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-exchange-alt me-2"></i>Convert to Proforma Invoice
                        </button>
                    </form>
                </div>
            </div>
            @elseif($quote->invoices()->exists())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Related Invoices
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($quote->invoices as $invoice)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $invoice->invoice_number }}</strong>
                                <br>
                                <small class="text-muted">{{ ucfirst($invoice->type) }} - {{ $invoice->status }}</small>
                            </div>
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quote Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Quote Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-12">
                            <div class="border-bottom pb-2 mb-2">
                                <h4 class="mb-0 text-primary">{{ number_format($quote->total_amount, 2) }}</h4>
                                <small class="text-muted">Total Amount (AED)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-0">{{ $quote->items->count() }}</h6>
                            <small class="text-muted">Items</small>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-0">{{ $quote->created_at->diffForHumans() }}</h6>
                            <small class="text-muted">Created</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-shape.icon-sm {
        width: 2rem;
        height: 2rem;
    }
    
    .icon-shape.icon-lg {
        width: 4rem;
        height: 4rem;
    }
    
    .bg-soft-primary { background-color: rgba(79, 70, 229, 0.1) !important; }
    .bg-soft-success { background-color: rgba(16, 185, 129, 0.1) !important; }
    .bg-soft-info { background-color: rgba(59, 130, 246, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
</style>
@endsection 