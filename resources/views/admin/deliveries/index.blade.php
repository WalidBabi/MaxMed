@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Deliveries</h1>
        <a href="{{ route('admin.deliveries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Delivery
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order #</th>
                            <th>Status</th>
                            <th>Invoice Status</th>
                            <th>Tracking #</th>
                            <th>Carrier</th>
                            <th>Shipped At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $delivery)
                            @php
                                $proformaInvoice = $delivery->getProformaInvoice();
                                $canConvert = $delivery->isReadyForFinalInvoice();
                                $hasFinalInvoice = $delivery->finalInvoice()->exists();
                            @endphp
                            <tr>
                                <td>{{ $delivery->id }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $delivery->order) }}">
                                        #{{ $delivery->order_id }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $delivery->status === 'delivered' ? 'success' : 
                                        ($delivery->status === 'in_transit' ? 'primary' : 
                                        ($delivery->status === 'processing' ? 'info' : 'secondary'))
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($hasFinalInvoice)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Final Invoice
                                        </span>
                                    @elseif($canConvert)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Ready to Convert
                                        </span>
                                    @elseif($proformaInvoice)
                                        <span class="badge bg-info">
                                            <i class="fas fa-file-invoice me-1"></i>Proforma Only
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-minus me-1"></i>No Invoice
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $delivery->tracking_number ?? 'N/A' }}</td>
                                <td>{{ $delivery->carrier ?? 'N/A' }}</td>
                                <td>{{ $delivery->shipped_at?->format('M d, Y H:i') ?? 'Not shipped' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.deliveries.show', $delivery) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.deliveries.edit', $delivery) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if($canConvert)
                                            <form action="{{ route('admin.deliveries.convert-to-final-invoice', $delivery) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Convert proforma invoice to final invoice?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Convert to Final Invoice">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.deliveries.destroy', $delivery) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this delivery?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No deliveries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $deliveries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
