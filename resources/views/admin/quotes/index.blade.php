@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Quote Management</h2>
            <p class="text-muted mb-0">Create and manage customer quotes</p>
        </div>
        <a href="{{ route('admin.quotes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Quote
        </a>
    </div>

    <!-- Quotes Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($quotes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Quote Number</th>
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th>Amount (AED)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes as $quote)
                                <tr>
                                    <td class="text-nowrap">
                                        {{ $quote->created_at->format('d M Y') }}
                                    </td>
                                    <td class="fw-medium">
                                        {{ $quote->quote_number }}
                                    </td>
                                    <td>
                                        {{ $quote->customer_name }}
                                    </td>
                                    <td>
                                        @if($quote->status === 'draft')
                                            <span class="badge bg-soft-secondary text-secondary">Draft</span>
                                        @elseif($quote->status === 'sent')
                                            <span class="badge bg-soft-info text-info">Sent</span>
                                        @elseif($quote->status === 'invoiced')
                                            <span class="badge bg-soft-success text-success">Invoiced</span>
                                        @else
                                            <span class="badge bg-soft-warning text-warning">{{ ucfirst($quote->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap fw-medium">
                                        {{ number_format($quote->total_amount, 2) }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.quotes.show', $quote) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="View Quote">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.quotes.edit', $quote) }}" 
                                               class="btn btn-sm btn-outline-success" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit Quote">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.quotes.pdf', $quote) }}" 
                                               class="btn btn-sm btn-outline-danger" 
                                               data-bs-toggle="tooltip" 
                                               title="Download PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            @if($quote->status !== 'invoiced')
                                                <a href="{{ route('admin.quotes.convert-to-proforma', $quote) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Convert to Proforma Invoice"
                                                   onclick="return confirm('Are you sure you want to convert this quote to a proforma invoice?')">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            @endif
                                            <button class="btn btn-sm btn-outline-info send-email-btn" 
                                                    data-bs-toggle="tooltip" 
                                                    data-quote-id="{{ $quote->id }}"
                                                    data-customer-name="{{ $quote->customer_name }}"
                                                    data-quote-number="{{ $quote->quote_number }}"
                                                    title="Send Email">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-quote-btn" 
                                                    data-quote-id="{{ $quote->id }}"
                                                    data-bs-toggle="tooltip" 
                                                    title="Delete Quote">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3 mx-auto mb-3">
                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                    </div>
                    <h5>No Quotes Found</h5>
                    <p class="text-muted mb-4">Get started by creating your first quote</p>
                    <a href="{{ route('admin.quotes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Quote
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if($quotes->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="pagination-info">
                Showing {{ $quotes->firstItem() ?? 0 }} to {{ $quotes->lastItem() ?? 0 }} of {{ $quotes->total() }} results
            </div>
            <div class="pagination-wrapper">
                {{ $quotes->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Quote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this quote? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendEmailModalLabel">Send Quote Email</h5>
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
                        The quote PDF will be automatically attached to the email.
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
    
    .bg-soft-primary { background-color: rgba(79, 70, 229, 0.1) !important; }
    .bg-soft-success { background-color: rgba(16, 185, 129, 0.1) !important; }
    .bg-soft-info { background-color: rgba(59, 130, 246, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .pagination-wrapper .pagination {
        gap: 0.25rem;
    }
    
    .pagination-wrapper .page-link {
        border: 1px solid #e5e7eb;
        color: #6b7280;
        padding: 0.5rem 0.75rem;
        margin: 0;
        border-radius: 0.375rem !important;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .pagination-wrapper .page-item:not(.active) .page-link:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: #374151;
        transform: translateY(-1px);
    }
    
    .pagination-wrapper .page-item.active .page-link {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: white;
        font-weight: 600;
    }
    
    .pagination-wrapper .page-item.disabled .page-link {
        color: #d1d5db;
        background-color: #f9fafb;
        border-color: #e5e7eb;
        cursor: not-allowed;
    }
    
    .pagination-info {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }
</style>

<script>
function deleteQuote(quoteId) {
    document.getElementById('deleteForm').action = '/admin/quotes/' + quoteId;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Event delegation for send email buttons and delete buttons
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.send-email-btn')) {
            const button = e.target.closest('.send-email-btn');
            const quoteId = button.getAttribute('data-quote-id');
            const customerName = button.getAttribute('data-customer-name');
            const quoteNumber = button.getAttribute('data-quote-number');
            
            openEmailModal(quoteId, customerName, quoteNumber);
        }
        
        if (e.target.closest('.delete-quote-btn')) {
            const button = e.target.closest('.delete-quote-btn');
            const quoteId = button.getAttribute('data-quote-id');
            deleteQuote(quoteId);
        }
    });
});

function openEmailModal(quoteId, customerName, quoteNumber) {
    // Set modal title
    document.getElementById('sendEmailModalLabel').textContent = `Send Email - Quote ${quoteNumber}`;
    
    // Set form action
    document.getElementById('sendEmailForm').action = `/admin/quotes/${quoteId}/send-email`;
    
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
        alert('Customer email is required to send the quote.');
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
@endsection 