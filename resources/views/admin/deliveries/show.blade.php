@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Delivery #{{ $delivery->id }}</h1>
        <div>
            <a href="{{ route('admin.deliveries.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ 
                                $delivery->status === 'delivered' ? 'success' : 
                                ($delivery->status === 'in_transit' ? 'primary' : 
                                ($delivery->status === 'processing' ? 'info' : 'secondary'))
                            }}">
                                {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Order:</div>
                        <div class="col-md-8">
                            <a href="{{ route('admin.orders.show', $delivery->order) }}">
                                Order #{{ $delivery->order_id }}
                            </a>
                        </div>
                    </div>
                    @if($delivery->tracking_number)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Tracking Number:</div>
                            <div class="col-md-8">{{ $delivery->tracking_number }}</div>
                        </div>
                    @endif
                    @if($delivery->carrier)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Carrier:</div>
                            <div class="col-md-8">{{ $delivery->carrier }}</div>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Shipping Cost:</div>
                        <div class="col-md-8">${{ number_format($delivery->shipping_cost, 2) }}</div>
                    </div>
                    @if($delivery->total_weight)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Total Weight:</div>
                            <div class="col-md-8">{{ $delivery->total_weight }} kg</div>
                        </div>
                    @endif
                    @if($delivery->shipped_at)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Shipped At:</div>
                            <div class="col-md-8">{{ $delivery->shipped_at->format('M d, Y H:i') }}</div>
                        </div>
                    @endif
                    @if($delivery->delivered_at)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Delivered At:</div>
                            <div class="col-md-8">{{ $delivery->delivered_at->format('M d, Y H:i') }}</div>
                        </div>
                        @if($delivery->signed_at)
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Signed At:</div>
                                <div class="col-md-8">{{ $delivery->signed_at->format('M d, Y H:i') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Signed IP:</div>
                                <div class="col-md-8">{{ $delivery->signature_ip_address ?? 'N/A' }}</div>
                            </div>
                        @endif
                    @endif
                    @if($delivery->notes)
                        <div class="row">
                            <div class="col-md-4 fw-bold">Notes:</div>
                            <div class="col-md-8">{{ $delivery->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($delivery->shipping_address)) !!}
                </div>
            </div>

            @if($delivery->customer_signature)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Signature</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($delivery->customer_signature_url)
                            <img src="{{ $delivery->customer_signature_url }}" alt="Customer Signature" class="img-fluid mb-3" style="max-height: 150px;">
                            <div class="signature-line"></div>
                            <p class="text-muted mt-2 mb-0">Customer's Signature</p>
                        @else
                            <p class="text-muted">No signature available</p>
                        @endif
                    </div>
                </div>

                @if($delivery->delivery_conditions)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Delivery Conditions</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                @foreach($delivery->delivery_conditions as $condition)
                                    @php
                                        $conditionText = [
                                            'received_undamaged' => 'I confirm that I have received the items in good condition',
                                            'agree_terms' => 'I agree to the terms and conditions of delivery',
                                            'no_damage' => 'I confirm there is no visible damage to the packaging or contents'
                                        ][$condition] ?? $condition;
                                    @endphp
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> {{ $conditionText }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            @elseif($delivery->isInTransit() && !$delivery->customer_signature)
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0 text-white">Signature Pending</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted">Customer has not yet signed for this delivery.</p>
                        <a href="{{ route('deliveries.sign', $delivery) }}" class="btn btn-primary">
                            <i class="fas fa-signature me-2"></i> Capture Signature
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    @if(!$delivery->isInTransit() && !$delivery->isDelivered())
                        <form action="{{ route('admin.deliveries.mark-as-shipped', $delivery) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label for="tracking_number" class="form-label">Tracking Number</label>
                                <input type="text" class="form-control" id="tracking_number" name="tracking_number" required>
                            </div>
                            <div class="mb-3">
                                <label for="carrier" class="form-label">Carrier</label>
                                <input type="text" class="form-control" id="carrier" name="carrier" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-truck"></i> Mark as Shipped
                            </button>
                        </form>
                    @endif

                    @if($delivery->isInTransit() && !$delivery->isDelivered())
                        <form action="{{ route('admin.deliveries.mark-as-delivered', $delivery) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Mark as Delivered
                            </button>
                        </form>
                    @endif

                    <div class="list-group">
                        <a href="{{ route('admin.orders.show', $delivery->order) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-cart me-2"></i> View Order
                        </a>
                        <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit me-2"></i> Edit Delivery
                        </a>
                        <form action="{{ route('admin.deliveries.destroy', $delivery) }}" method="POST" class="list-group-item p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger w-100 text-start" 
                                    onclick="return confirm('Are you sure you want to delete this delivery?')">
                                <i class="fas fa-trash me-2"></i> Delete Delivery
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    @foreach($delivery->order->items as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                {{ $item->quantity }}x {{ $item->product->name }}
                                @if($item->variation)
                                    <div class="text-muted small">{{ $item->variation }}</div>
                                @endif
                            </div>
                            <div>${{ number_format($item->price * $item->quantity, 2) }}</div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <div>Subtotal:</div>
                        <div>${{ number_format($delivery->order->total_amount - $delivery->shipping_cost, 2) }}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>Shipping:</div>
                        <div>${{ number_format($delivery->shipping_cost, 2) }}</div>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mt-2">
                        <div>Total:</div>
                        <div>${{ number_format($delivery->order->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
