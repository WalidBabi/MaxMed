@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Delivery #{{ $delivery->id }}</h1>
        <div>
            <a href="{{ route('admin.deliveries.show', $delivery) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Delivery
            </a>
            <a href="{{ route('admin.deliveries.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> All Deliveries
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.deliveries.update', $delivery) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Order</label>
                        <input type="text" class="form-control" value="Order #{{ $delivery->order_id }}" disabled>
                        <div class="form-text">Order cannot be changed after creation.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            @foreach(\App\Models\Delivery::$statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $delivery->status) == $value ? 'selected' : '' }}>
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
                               value="{{ old('carrier', $delivery->carrier) }}"
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
                               value="{{ old('tracking_number', $delivery->tracking_number) }}">
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
                                   value="{{ old('shipping_cost', number_format($delivery->shipping_cost, 2)) }}" 
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
                               value="{{ old('total_weight', $delivery->total_weight) }}">
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
                              rows="3">{{ old('notes', $delivery->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(!$delivery->isDelivered())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.deliveries.update-status', $delivery) }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <label for="update_status" class="form-label">Update Status To</label>
                            <select class="form-select" id="update_status" name="status" required>
                                @foreach(\App\Models\Delivery::$statuses as $value => $label)
                                    @if($value !== $delivery->status && 
                                        (($delivery->status === 'pending' && in_array($value, ['processing', 'in_transit', 'delivered', 'cancelled'])) ||
                                         ($delivery->status === 'processing' && in_array($value, ['in_transit', 'delivered', 'cancelled'])) ||
                                         ($delivery->status === 'in_transit' && in_array($value, ['delivered', 'cancelled'])) ||
                                         ($delivery->status === 'cancelled' && in_array($value, ['processing', 'in_transit', 'delivered']))))
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4" id="trackingNumberField" style="display: none;">
                            <label for="tracking_number_update" class="form-label">Tracking Number *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="tracking_number_update" 
                                   name="tracking_number"
                                   value="{{ $delivery->tracking_number }}">
                        </div>
                        
                        <div class="col-md-4" id="carrierField" style="display: none;">
                            <label for="carrier_update" class="form-label">Carrier *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="carrier_update" 
                                   name="carrier"
                                   value="{{ $delivery->carrier }}">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-1"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('update_status');
        const trackingField = document.getElementById('trackingNumberField');
        const carrierField = document.getElementById('carrierField');
        
        if (statusSelect) {
            // Initial check
            toggleFields(statusSelect.value);
            
            // Add event listener for changes
            statusSelect.addEventListener('change', function() {
                toggleFields(this.value);
            });
            
            function toggleFields(status) {
                if (status === 'in_transit') {
                    trackingField.style.display = 'block';
                    carrierField.style.display = 'block';
                    document.getElementById('tracking_number_update').required = true;
                    document.getElementById('carrier_update').required = true;
                } else {
                    trackingField.style.display = 'none';
                    carrierField.style.display = 'none';
                    document.getElementById('tracking_number_update').required = false;
                    document.getElementById('carrier_update').required = false;
                }
            }
        }
    });
</script>
@endpush
@endsection
