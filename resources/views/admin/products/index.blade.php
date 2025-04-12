@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <h1 class="page-title">Products Management</h1>
        <p class="page-description text-muted">Manage your product inventory, pricing, and categories</p>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <!-- Filter Toggle Button -->
        <button class="btn btn-outline-primary me-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="{{ request()->hasAny(['search', 'category_id', 'brand_id', 'application', 'stock_status', 'min_price', 'max_price', 'sort_by', 'sort_order']) ? 'true' : 'false' }}" aria-controls="filterCollapse">
            <i class="fas fa-filter me-1"></i> Filters
            @if(request()->hasAny(['search', 'category_id', 'brand_id', 'application', 'stock_status', 'min_price', 'max_price']))
                <span class="badge bg-primary ms-1">{{ count(array_filter(request()->only(['search', 'category_id', 'brand_id', 'application', 'stock_status', 'min_price', 'max_price']))) }}</span>
            @endif
        </button>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Product
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 collapse {{ request()->hasAny(['search', 'category_id', 'brand_id', 'application', 'stock_status', 'min_price', 'max_price', 'sort_by', 'sort_order']) ? 'show' : '' }}" id="filterCollapse">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Filter Products</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" id="filter-form">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search by Name</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select class="form-select" id="brand_id" name="brand_id">
                            <option value="">All Brands</option>
                            @foreach(App\Models\Brand::orderBy('name')->get() as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="application" class="form-label">Application</label>
                        <select class="form-select" id="application" name="application">
                            <option value="">All Applications</option>
                            <option value="clinical" {{ request('application') == 'clinical' ? 'selected' : '' }}>Clinical</option>
                            <option value="research" {{ request('application') == 'research' ? 'selected' : '' }}>Research</option>
                            <option value="industrial" {{ request('application') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                            <option value="educational" {{ request('application') == 'educational' ? 'selected' : '' }}>Educational</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="stock_status" class="form-label">Stock Status</label>
                        <select class="form-select" id="stock_status" name="stock_status">
                            <option value="">All</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="min_price" class="form-label">Min Price</label>
                        <input type="number" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="max_price" class="form-label">Max Price</label>
                        <input type="number" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <select class="form-select" id="sort_order" name="sort_order">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>
                    <div class="col-md-9 d-flex align-items-end mb-3">
                        <div class="d-grid gap-2 d-md-flex w-100">
                            <button type="submit" class="btn btn-primary flex-grow-1">Apply Filters</button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary flex-grow-1">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row category-container">
        @foreach($products as $product)
            <div class=" mb-4 category-card-wrapper">
                <div class="card h-100 category-card">
                    <img src="{{ asset($product->image_url) }}" 
                         class="card-img-top" 
                         alt="{{ $product->name }}"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="h5 mb-0">${{ number_format($product->price, 2) }}</span>
                                <span class="h5 mb-0 ms-2">AED {{ number_format($product->price_aed, 2) }}</span>
                            </div>
                            <span class="badge bg-{{ $product->inventory->quantity > 0 ? 'success' : 'danger' }} lab-badge">
                                Stock: {{ $product->inventory->quantity }}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($products->isEmpty())
    <div class="lab-alert text-center py-5">
        <div class="mb-3 microscope-lens">
            <i class="fas fa-box-open fa-4x text-muted"></i>
        </div>
        <h3>No products found</h3>
        <p class="text-muted">Try changing your filter criteria</p>
    </div>
    @else
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-md-0">
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
                </div>
                <div>
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    /* Lab Alert Styling */
    .lab-alert {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 8px 32px 0 rgba(31, 38, 135, 0.37),
            inset 0 0 80px rgba(255, 255, 255, 0.3);
        position: relative;
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .lab-alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 50%;
        background: linear-gradient(
            180deg,
            rgba(255, 255, 255, 0.3) 0%,
            rgba(255, 255, 255, 0.1) 100%
        );
        border-radius: 8px 8px 0 0;
        pointer-events: none;
    }

    .microscope-lens {
        position: relative;
        overflow: hidden;
        border-radius: 50%;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        display: inline-block;
        width: 80px;
        height: 80px;
        line-height: 80px;
    }
    
    .lab-alert:hover .microscope-lens {
        transform: scale(1.1) rotate(5deg);
    }

    /* Category Card Animation */
    .category-card-wrapper {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .category-container {
        margin-top: 15px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 28px;
    }
    
    /* Lab badge for categories */
    .lab-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(23, 30, 96, 0.9);
        color: white;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 10;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        transition: all 0.3s;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate cards on page load
        const cards = document.querySelectorAll('.category-card-wrapper');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });

        // Restore filter values from localStorage
        const filterForm = document.getElementById('filter-form');
        const filterInputs = filterForm.querySelectorAll('input, select');
        
        // Function to restore saved filters
        const restoreFilters = () => {
            filterInputs.forEach(input => {
                const savedValue = localStorage.getItem(`product_filter_${input.name}`);
                if (savedValue !== null) {
                    input.value = savedValue;
                }
            });
        };
        
        // Save filter values when they change
        filterInputs.forEach(input => {
            input.addEventListener('change', () => {
                localStorage.setItem(`product_filter_${input.name}`, input.value);
            });
        });
        
        // Clear filters when reset button is clicked
        const resetButton = filterForm.querySelector('a.btn-secondary');
        resetButton.addEventListener('click', (e) => {
            e.preventDefault();
            // Clear localStorage
            filterInputs.forEach(input => {
                localStorage.removeItem(`product_filter_${input.name}`);
            });
            // Redirect to index
            window.location.href = "{{ route('admin.products.index') }}";
        });
        
        // Only restore filters if not already set in URL
        if (!window.location.search) {
            restoreFilters();
            // Submit form if any filters were restored
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
