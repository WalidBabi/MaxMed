@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Add New Customer</h1>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Customers
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">Basic Information</h5>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Link to User Account (Optional)</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                <option value="">Select a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Company Information</h5>
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tax_id" class="form-label">Tax ID</label>
                            <input type="text" class="form-control @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id" value="{{ old('tax_id') }}">
                            @error('tax_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">Billing Address</h5>
                        @include('admin.customers.partials.address-fields', ['prefix' => 'billing_'])
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Shipping Address <small class="text-muted">(Same as billing)</small></h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="same_as_billing" onchange="copyBillingToShipping()">
                            <label class="form-check-label" for="same_as_billing">Same as billing address</label>
                        </div>
                        @include('admin.customers.partials.address-fields', ['prefix' => 'shipping_'])
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyBillingToShipping() {
        const sameAsBilling = document.getElementById('same_as_billing').checked;
        if (sameAsBilling) {
            document.getElementById('shipping_street').value = document.getElementById('billing_street').value;
            document.getElementById('shipping_city').value = document.getElementById('billing_city').value;
            document.getElementById('shipping_state').value = document.getElementById('billing_state').value;
            document.getElementById('shipping_zip').value = document.getElementById('billing_zip').value;
            document.getElementById('shipping_country').value = document.getElementById('billing_country').value;
        }
    }
</script>
@endpush
@endsection
