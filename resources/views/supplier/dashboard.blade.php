@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="welcome-card bg-white rounded-3 p-4 shadow-sm mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div>
                <h2 class="h4 mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-muted mb-0">Manage your product catalog and track your submissions.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-light text-dark">
                    <i class="far fa-calendar-alt me-2"></i>
                    {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-12 col-lg-12">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                   
                    <h3 class="mb-1">{{ $totalProducts }}</h3>
                    <p class="text-muted small mb-0">Total Products</p>
                </div>
            </div>
        </div>

       


    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
              
                <div class="card-body p-0">
                    @if($recentProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($product->image_url)
                                                        <img src="{{ $product->image_url }}" 
                                                             alt="{{ $product->name }}" 
                                                             class="rounded me-3" 
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium">{{ Str::limit($product->name, 30) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $product->category->name ?? 'No category' }}</span>
                                            </td>
                                          
                                            <td>
                                                <span class="text-muted">{{ $product->created_at->format('M d, Y') }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('supplier.products.edit', $product) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit">Edit</i>
                                                </a>
                                            <form action="{{ route('supplier.products.destroy', $product) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <i class="fas fa-trash">Delete</i>
                                                </button>
                                            </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No products yet</h5>
                            <p class="text-muted mb-3">Start building your product catalog</p>
                            <a href="{{ route('supplier.products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Add Your First Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('supplier.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Add New Product
                        </a>
                        <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i> Manage Products
                        </a>
                        <a href="{{ route('supplier.feedback.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-comment-dots me-2"></i> System Feedback
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>Supplier Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted">
                        <p><strong>Brand:</strong> All supplier products are automatically assigned to the "Yooning" brand.</p>
                        <p><strong>Updates:</strong> You can edit your product information anytime.</p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-comment-dots text-info me-2"></i>Help Us Improve
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-3">
                        <p>Your feedback helps us make the system better for all suppliers. Share your suggestions, report issues, or request new features.</p>
                    </div>
                    <a href="{{ route('supplier.feedback.create') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-paper-plane me-2"></i> Submit Feedback
                    </a>
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
        width: 4rem;
        height: 4rem;
    }
    
    .bg-soft-primary { background-color: rgba(79, 70, 229, 0.1) !important; }
    .bg-soft-success { background-color: rgba(16, 185, 129, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1) !important; }
    .bg-soft-danger { background-color: rgba(239, 68, 68, 0.1) !important; }
    
    .welcome-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #f1f3ff 100%);
        border: 1px solid rgba(79, 70, 229, 0.1);
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
    }

    .table td {
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f9fafb;
    }
</style>
@endsection 