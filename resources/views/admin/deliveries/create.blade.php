@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create Delivery</h1>
        <a href="{{ route('admin.deliveries.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.deliveries.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="order_id" class="form-label">Order *</label>
                        <select class="form-select @error('order_id') is-invalid @enderror" 
                                id="order_id" 
                                name="order_id" 
                                required>
                            <option value="">Select an order</option>
                            @foreach($orders as $id => $label)
                                <option value="{{ $id }}" {{ old('order_id') == $id ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            @foreach(\App\Models\Delivery::$statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', 'pending') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="carrier" class="form-label">Carrier *</label>
                        <input type="text" 
                               class="form-control @error('carrier') is-invalid @enderror" 
                               id="carrier" 
                               name="carrier" 
                               value="{{ old('carrier') }}"
                               required>
                        @error('carrier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="tracking_number" class="form-label">Tracking Number</label>
                        <input type="text" 
                               class="form-control @error('tracking_number') is-invalid @enderror" 
                               id="tracking_number" 
                               name="tracking_number" 
                               value="{{ old('tracking_number') }}">
                        <div class="form-text">Leave blank to generate automatically</div>
                        @error('tracking_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="shipping_cost" class="form-label">Shipping Cost *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   class="form-control @error('shipping_cost') is-invalid @enderror" 
                                   id="shipping_cost" 
                                   name="shipping_cost" 
                                   value="{{ old('shipping_cost', '0.00') }}" 
                                   required>
                        </div>
                        @error('shipping_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="total_weight" class="form-label">Total Weight (kg)</label>
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               class="form-control @error('total_weight') is-invalid @enderror" 
                               id="total_weight" 
                               name="total_weight" 
                               value="{{ old('total_weight') }}">
                        @error('total_weight')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-select order if passed in URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('order_id');
        if (orderId) {
            document.getElementById('order_id').value = orderId;
        }
    });
</script>
@endpush
@endsection
