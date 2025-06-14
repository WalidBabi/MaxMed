@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Invoice Management</h2>
            <p class="text-muted mb-0">Manage proforma invoices, final invoices, and payments</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Invoice
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.invoices.index') }}" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search invoices...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($filterOptions['types'] as $value => $label)
                            <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($filterOptions['statuses'] as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_status" class="form-select">
                        <option value="">Payment Status</option>
                        @foreach($filterOptions['payment_statuses'] as $value => $label)
                            <option value="{{ $value }}" {{ request('payment_status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Invoice #</th>
                                <th>Type</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Payment Condition</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td class="text-nowrap">
                                        {{ $invoice->invoice_date->format('d M Y') }}
                                    </td>
                                    <td class="fw-medium">
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-decoration-none">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                        @if($invoice->quote_id)
                                            <br><small class="text-muted">from {{ $invoice->quote->quote_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $invoice->type === 'proforma' ? 'bg-info' : 'bg-success' }}">
                                            {{ ucfirst($invoice->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $invoice->customer_name }}
                                    </td>
                                    <td>
                                        <span class="{{ $invoice->status_badge_class }}">
                                            {{ $invoice->status_options[$invoice->status] ?? ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $invoice->payment_status_badge_class }}">
                                            {{ $invoice->payment_status_options[$invoice->payment_status] ?? ucfirst($invoice->payment_status) }}
                                        </span>
                                        @if($invoice->paid_amount > 0)
                                            <br><small class="text-success fw-medium">
                                                <i class="fas fa-coins me-1"></i>{{ $invoice->formatted_paid_amount }} {{ $invoice->currency }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $invoice::PAYMENT_TERMS[$invoice->payment_terms] ?? ucfirst($invoice->payment_terms) }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap fw-medium">
                                        {{ $invoice->formatted_total }} {{ $invoice->currency }}
                                        @if($invoice->paid_amount > 0)
                                            <br><small class="text-success">Paid: {{ $invoice->formatted_paid_amount }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="View Invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.invoices.edit', $invoice) }}" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit Invoice">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.invoices.pdf', $invoice) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               data-bs-toggle="tooltip" 
                                               title="Download PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-success send-email-btn" 
                                                    data-bs-toggle="tooltip" 
                                                    data-invoice-id="{{ $invoice->id }}"
                                                    data-customer-name="{{ $invoice->customer_name }}"
                                                    data-invoice-number="{{ $invoice->invoice_number }}"
                                                    title="Send Email">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-invoice-btn" 
                                                    data-invoice-id="{{ $invoice->id }}"
                                                    data-bs-toggle="tooltip" 
                                                    title="Delete Invoice">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} results
                    </div>
                    {{ $invoices->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3 mx-auto mb-3">
                        <i class="fas fa-file-invoice fa-2x"></i>
                    </div>
                    <h5>No Invoices Found</h5>
                    <p class="text-muted mb-4">Start by creating your first invoice or converting a quote to proforma invoice</p>
                    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Invoice
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendEmailModalLabel">Send Invoice Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendEmailForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Customer Email</label>
                        <input type="email" id="customer_email" name="customer_email" 
                               class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cc_emails" class="form-label">CC Email Addresses</label>
                        <input type="text" id="cc_emails" name="cc_emails" 
                               value="sales@maxmedme.com"
                               class="form-control">
                        <small class="form-text text-muted">Separate multiple email addresses with commas</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        The invoice PDF will be automatically attached to the email.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event delegation for send email buttons and delete buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.send-email-btn')) {
            const button = e.target.closest('.send-email-btn');
            const invoiceId = button.getAttribute('data-invoice-id');
            const customerName = button.getAttribute('data-customer-name');
            const invoiceNumber = button.getAttribute('data-invoice-number');
            
            openEmailModal(invoiceId, customerName, invoiceNumber);
        }
        
        if (e.target.closest('.delete-invoice-btn')) {
            const button = e.target.closest('.delete-invoice-btn');
            const invoiceId = button.getAttribute('data-invoice-id');
            deleteInvoice(invoiceId);
        }
    });

    // Delete Invoice
    window.deleteInvoice = function(invoiceId) {
        if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/invoices/${invoiceId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    };

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});

function openEmailModal(invoiceId, customerName, invoiceNumber) {
    // Set modal title
    document.getElementById('sendEmailModalLabel').textContent = `Send Email - Invoice ${invoiceNumber}`;
    
    // Set form action
    document.getElementById('sendEmailForm').action = `/admin/invoices/${invoiceId}/send-email`;
    
    // Fetch customer email
    fetch(`/admin/customers/by-name/${encodeURIComponent(customerName)}`)
        .then(response => response.json())
        .then(data => {
            if (data.email) {
                document.getElementById('customer_email').value = data.email;
            } else {
                document.getElementById('customer_email').value = '';
                document.getElementById('customer_email').placeholder = 'No email found for this customer';
            }
        })
        .catch(error => {
            console.error('Error fetching customer email:', error);
            document.getElementById('customer_email').value = '';
            document.getElementById('customer_email').placeholder = 'Error loading customer email';
        });
    
    // Set CC emails to default
    document.getElementById('cc_emails').value = 'sales@maxmedme.com';
    
    // Show modal
    const emailModal = new bootstrap.Modal(document.getElementById('sendEmailModal'));
    emailModal.show();
}

// Handle email form submission
document.getElementById('sendEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const customerEmail = document.getElementById('customer_email').value;
    if (!customerEmail) {
        alert('Customer email is required to send the invoice.');
        return;
    }
    
    const ccEmails = document.getElementById('cc_emails').value.trim();
    if (ccEmails) {
        const emailArray = ccEmails.split(',').map(email => email.trim());
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        for (let email of emailArray) {
            if (!emailRegex.test(email)) {
                alert(`Invalid email format: ${email}`);
                return;
            }
        }
    }
    
    // Show loading state
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    
    // Submit form
    this.submit();
});
</script>

<style>
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
    }
    
    .bg-soft-primary {
        background-color: rgba(79, 70, 229, 0.1) !important;
    }
    
    .text-primary {
        color: #4f46e5 !important;
    }
    
    .btn-group .btn {
        border-radius: 0;
    }
    
    .btn-group .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    
    .btn-group .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
</style>
@endsection 