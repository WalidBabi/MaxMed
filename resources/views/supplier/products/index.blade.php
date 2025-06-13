@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">My Products</h1>
                    <p class="text-muted mb-0">Manage your product catalog</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('supplier.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
                    <a href="{{ route('supplier.products.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i> Add New Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <form action="{{ route('supplier.products.index') }}" method="GET" id="filter-form" class="filter-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label small text-muted">Search Products</label>
                                <div class="input-group">
                              
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Search by product name or description..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="category_id" class="form-label small text-muted">Category</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        @forelse($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card border-0 shadow-sm h-100">
                    <div class="position-relative">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="card-img-top product-image">
                        @else
                            <div class="card-img-top product-image-placeholder d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-image text-muted fa-2x"></i>
                            </div>
                        @endif
                        
                     
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <h6 class="card-title mb-1 fw-semibold">{{ Str::limit($product->name, 50) }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-tags me-1"></i>{{ $product->category->name ?? 'No category' }}
                            </small>
                        </div>
                        
                     
                        
                        <div class="product-actions mt-auto">
                            <div class="d-flex gap-2">
                                <a href="{{ route('supplier.products.edit', $product) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state text-center py-5">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-box-open fa-4x text-muted"></i>
                    </div>
                    <h3 class="text-muted mb-2">No products found</h3>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'category_id']))
                            Try adjusting your filters or add a new product
                        @else
                            Start building your product catalog by adding your first product
                        @endif
                    </p>
                    <a href="{{ route('supplier.products.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i> Add Your First Product
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the product "<span id="productName"></span>"?</p>
                <p class="text-muted small">This action cannot be undone and will remove all associated images and data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteProduct(productId, productName) {
    document.getElementById('productName').textContent = productName;
    document.getElementById('deleteForm').action = `/supplier/products/${productId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Auto-submit form when search input changes (with delay)
let searchTimeout;
document.getElementById('search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filter-form').submit();
    }, 500);
});

// Auto-submit form when category changes
document.getElementById('category_id').addEventListener('change', function() {
    document.getElementById('filter-form').submit();
});
</script>

<style>
.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.product-image {
    height: 200px;
    object-fit: cover;
    width: 100%;
}

.product-image-placeholder {
    height: 200px;
}

.product-pricing {
    font-size: 0.9rem;
}

.price {
    font-size: 1rem;
    margin-right: 0.5rem;
}

.price-aed {
    font-size: 0.85rem;
}

.empty-state-icon {
    opacity: 0.6;
}

.filter-form .form-control, .filter-form .form-select {
    border-radius: 0.375rem;
}

.filter-form .input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.badge {
    font-size: 0.7rem;
}

@media (max-width: 768px) {
    .product-actions .d-flex {
        flex-direction: column;
    }
    
    .product-actions .btn {
        margin-bottom: 0.25rem;
    }
}
</style>
@endsection 