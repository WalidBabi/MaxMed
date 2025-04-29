@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-1">Products Management</h1>
                <p class="page-description text-muted">Manage your product inventory, pricing, and categories</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ App\Models\Product::count() }}</h3>
                    <p>Total Products</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ App\Models\Product::whereHas('inventory', function($query) {
                        $query->where('quantity', '>', 0);
                    })->count() }}</h3>
                    <p>In Stock</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ App\Models\Product::whereHas('inventory', function($query) {
                        $query->where('quantity', '<=', 0);
                    })->count() }}</h3>
                    <p>Out of Stock</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ App\Models\Category::count() }}</h3>
                    <p>Categories</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Filter Products</h5>
            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-filter me-1"></i> Toggle Filters
                @if(request()->hasAny(['search', 'category_id', 'brand_id', 'stock_status', 'min_price', 'max_price']))
                    <span class="badge bg-primary ms-1">{{ count(array_filter(request()->only(['search', 'category_id', 'brand_id', 'stock_status', 'min_price', 'max_price']))) }}</span>
                @endif
            </button>
        </div>
        
        <div class="collapse {{ request()->hasAny(['search', 'category_id', 'brand_id', 'stock_status', 'min_price', 'max_price', 'sort_by', 'sort_order']) ? 'show' : '' }}" id="filterCollapse">
            <form action="{{ route('admin.products.index') }}" method="GET" id="filter-form" class="filter-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by Name">
                            <label for="search">Search by Name</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="category_id">Category</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="brand_id" name="brand_id">
                                <option value="">All Brands</option>
                                @foreach(App\Models\Brand::orderBy('name')->get() as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="brand_id">Brand</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="stock_status" name="stock_status">
                                <option value="">All</option>
                                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                            <label for="stock_status">Stock Status</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}" min="0" step="0.01" placeholder="Min Price">
                            <label for="min_price">Min Price</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}" min="0" step="0.01" placeholder="Max Price">
                            <label for="max_price">Max Price</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                            </select>
                            <label for="sort_by">Sort By</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="sort_order" name="sort_order">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                            </select>
                            <label for="sort_order">Sort Order</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="products-grid">
        @foreach($products as $product)
            <div class="product-card">
                <div class="product-image">
                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}">
                    <div class="product-badges">
                        <span class="stock-badge {{ $product->inventory->quantity > 0 ? 'in-stock' : 'out-of-stock' }}">
                            {{ $product->inventory->quantity }} in stock
                        </span>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">{{ $product->name }}</h3>
                    <div class="product-pricing">
                        <span class="price">${{ number_format($product->price, 2) }}</span>
                        <span class="price-aed">AED {{ number_format($product->price_aed, 2) }}</span>
                    </div>
                    <div class="product-actions">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($products->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-box-open"></i>
            </div>
            <h3>No products found</h3>
            <p>Try adjusting your filters or add a new product</p>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add New Product
            </a>
        </div>
    @else
        <div class="pagination-container">
            <div class="pagination-info">
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
            </div>
            <div class="pagination-links">
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
        </div>
    @endif
</div>

<style>
    /* Modern Card Design */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .stat-info h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .stat-info p {
        margin: 0;
        color: #6c757d;
        font-size: 0.875rem;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .filter-form .form-floating {
        margin-bottom: 0;
    }

    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .product-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-badges {
        position: absolute;
        top: 12px;
        right: 12px;
    }

    .stock-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }

    .stock-badge.in-stock {
        background: rgba(40, 167, 69, 0.9);
    }

    .stock-badge.out-of-stock {
        background: rgba(220, 53, 69, 0.9);
    }

    .product-info {
        padding: 16px;
    }

    .product-title {
        font-size: 1.1rem;
        margin-bottom: 8px;
        color: #212529;
    }

    .product-pricing {
        margin-bottom: 16px;
    }

    .price {
        font-size: 1.25rem;
        font-weight: 600;
        color: #212529;
    }

    .price-aed {
        font-size: 0.875rem;
        color: #6c757d;
        margin-left: 8px;
    }

    .product-actions {
        display: flex;
        gap: 8px;
    }

    .btn-edit, .btn-delete {
        flex: 1;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background: #e9ecef;
        color: #495057;
        border: none;
    }

    .btn-edit:hover {
        background: #dee2e6;
        color: #212529;
    }

    .btn-delete {
        background: #f8d7da;
        color: #721c24;
        border: none;
    }

    .btn-delete:hover {
        background: #f5c6cb;
        color: #721c24;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        margin-bottom: 8px;
        color: #212529;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 24px;
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .pagination-info {
        color: #6c757d;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        }

        .stat-card {
            margin-bottom: 16px;
        }

        .pagination-container {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate cards on page load
        const cards = document.querySelectorAll('.product-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });

        // Save filter values to localStorage
        const filterForm = document.getElementById('filter-form');
        const filterInputs = filterForm.querySelectorAll('input, select');
        
        filterInputs.forEach(input => {
            input.addEventListener('change', () => {
                localStorage.setItem(`product_filter_${input.name}`, input.value);
            });
        });

        // Restore saved filters
        const restoreFilters = () => {
            filterInputs.forEach(input => {
                const savedValue = localStorage.getItem(`product_filter_${input.name}`);
                if (savedValue !== null) {
                    input.value = savedValue;
                }
            });
        };

        // Clear filters
        const resetButton = filterForm.querySelector('a.btn-outline-secondary');
        resetButton.addEventListener('click', (e) => {
            e.preventDefault();
            filterInputs.forEach(input => {
                localStorage.removeItem(`product_filter_${input.name}`);
            });
            window.location.href = "{{ route('admin.products.index') }}";
        });

        // Restore filters if no URL parameters
        if (!window.location.search) {
            restoreFilters();
            let hasFilters = false;
            filterInputs.forEach(input => {
                if (input.value) hasFilters = true;
            });
            
            if (hasFilters) {
                filterForm.submit();
            }
        }
    });
</script>
@endsection
