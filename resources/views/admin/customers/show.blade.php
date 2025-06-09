@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Customer: {{ $customer->name }}</h1>
        <div>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Customers
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Customer Details</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mb-2" style="width: 80px; height: 80px; background-color: #{{ substr(md5($customer->name), 0, 6) }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <h4 class="mb-1">{{ $customer->name }}</h4>
                        <span class="badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Contact Information</h6>
                        <p class="mb-1">
                            <i class="fas fa-envelope me-2 text-muted"></i>
                            {{ $customer->email ?? 'N/A' }}
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-phone me-2 text-muted"></i>
                            {{ $customer->phone ?? 'N/A' }}
                        </p>
                        @if($customer->user)
                            <p class="mb-0">
                                <i class="fas fa-user me-2 text-muted"></i>
                                Linked to user: {{ $customer->user->name }} ({{ $customer->user->email }})
                            </p>
                        @endif
                    </div>

                    @if($customer->company_name || $customer->tax_id)
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">Company Information</h6>
                            @if($customer->company_name)
                                <p class="mb-1">
                                    <i class="fas fa-building me-2 text-muted"></i>
                                    {{ $customer->company_name }}
                                </p>
                            @endif
                            @if($customer->tax_id)
                                <p class="mb-0">
                                    <i class="fas fa-file-invoice me-2 text-muted"></i>
                                    Tax ID: {{ $customer->tax_id }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Customer Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h4 class="mb-0">0</h4>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Spent</h6>
                            <h4 class="mb-0">$0.00</h4>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Member Since</h6>
                            <p class="mb-0">{{ $customer->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Last Updated</h6>
                            <p class="mb-0">{{ $customer->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <ul class="nav nav-tabs" id="customerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses" type="button" role="tab" aria-controls="addresses" aria-selected="true">
                            <i class="fas fa-address-book me-2"></i>Addresses
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">
                            <i class="fas fa-shopping-cart me-2"></i>Orders (0)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="false">
                            <i class="fas fa-sticky-note me-2"></i>Notes
                        </button>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="customerTabsContent">
                        <!-- Addresses Tab -->
                        <div class="tab-pane fade show active" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="border p-3 rounded mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Billing Address</h6>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                        @if($customer->billing_street)
                                            <address class="mb-0">
                                                {{ $customer->billing_street }}<br>
                                                {{ $customer->billing_city }}, {{ $customer->billing_state }} {{ $customer->billing_zip }}<br>
                                                {{ $customer->billing_country }}
                                            </address>
                                        @else
                                            <p class="text-muted mb-0">No billing address provided.</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Shipping Address</h6>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                        @if($customer->shipping_street)
                                            <address class="mb-0">
                                                {{ $customer->shipping_street }}<br>
                                                {{ $customer->shipping_city }}, {{ $customer->shipping_state }} {{ $customer->shipping_zip }}<br>
                                                {{ $customer->shipping_country }}
                                            </address>
                                        @else
                                            <p class="text-muted mb-0">No shipping address provided.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Tab -->
                        <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5>No orders found</h5>
                                <p class="text-muted">This customer hasn't placed any orders yet.</p>
                            </div>
                        </div>

                        <!-- Notes Tab -->
                        <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                            @if($customer->notes)
                                <div class="mb-3">
                                    <h6>Customer Notes</h6>
                                    <div class="border p-3 rounded bg-light">
                                        {!! nl2br(e($customer->notes)) !!}
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                                    <h5>No notes available</h5>
                                    <p class="text-muted">There are no notes for this customer.</p>
                                </div>
                            @endif
                            
                            <form action="#" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="newNote" class="form-label">Add Note</label>
                                    <textarea class="form-control" id="newNote" name="note" rows="3" placeholder="Add a note about this customer..."></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Add Note
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }
    .avatar-circle {
        width: 80px;
        height: 80px;
        background-color: #0d6efd;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto;
    }
</style>
@endsection
