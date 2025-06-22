@props(['product'])

@php
    // Group specifications by category
    $specsByCategory = $product->specifications()
        ->where('show_on_detail', true)
        ->orderBy('category', 'asc')
        ->orderBy('sort_order', 'asc')
        ->get()
        ->groupBy('category');
@endphp

@if($specsByCategory->isNotEmpty())
<div class="section-card">
    <div class="card-header">
        <h4><i class="fas fa-cogs me-2"></i>Product Specifications</h4>
    </div>
    <div class="card-body">
        @foreach($specsByCategory as $category => $specifications)
            <div class="specification-category mb-4">
                @if($category)
                    <h5 class="category-title mb-3">
                        <i class="fas fa-tag me-2"></i>{{ $category }}
                    </h5>
                @endif
                
                <div class="specifications-grid">
                    @foreach($specifications as $spec)
                        <div class="specification-item" data-category="{{ $category }}">
                            <div class="spec-label">
                                {{ $spec->display_name }}
                                @if($spec->description)
                                    <i class="fas fa-info-circle ms-1" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="{{ $spec->description }}"></i>
                                @endif
                            </div>
                            <div class="spec-value">
                                <strong>{{ $spec->specification_value }}</strong>
                                @if($spec->unit)
                                    <span class="unit">{{ $spec->unit }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            @if(!$loop->last)
                <hr class="category-divider">
            @endif
        @endforeach
    </div>
</div>

<style>
/* Product Specifications Styling */
.specification-category {
    margin-bottom: 1.5rem;
}

.category-title {
    color: var(--main-color);
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--auxiliary-color);
}

.specifications-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

@media (max-width: 768px) {
    .specifications-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}

.specification-item {
    background-color: var(--light-gray);
    border-radius: 6px;
    padding: 1rem;
    border-left: 3px solid var(--auxiliary-color);
    transition: var(--transition);
    position: relative;
}

.specification-item:hover {
    background-color: #f0f2f5;
    border-left-color: var(--main-color);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.spec-label {
    font-size: 0.9rem;
    color: var(--dark-gray);
    margin-bottom: 0.5rem;
    font-weight: 500;
    display: flex;
    align-items: center;
}

.spec-label i.fa-info-circle {
    color: var(--auxiliary-color);
    cursor: help;
    font-size: 0.8rem;
    opacity: 0.7;
}

.spec-label i.fa-info-circle:hover {
    opacity: 1;
}

.spec-value {
    font-size: 1rem;
    color: var(--main-color);
    font-weight: 600;
    display: flex;
    align-items: baseline;
    gap: 0.25rem;
}

.spec-value .unit {
    font-size: 0.85rem;
    color: var(--auxiliary-color);
    font-weight: 400;
}

.category-divider {
    border: none;
    height: 1px;
    background: linear-gradient(to right, transparent, var(--medium-gray), transparent);
    margin: 1.5rem 0;
}

/* Tooltip styling */
.tooltip {
    font-size: 0.8rem;
}

.tooltip-inner {
    background-color: var(--main-color);
    color: white;
    border-radius: 4px;
    padding: 0.5rem 0.75rem;
    max-width: 200px;
}

.tooltip.bs-tooltip-top .tooltip-arrow::before {
    border-top-color: var(--main-color);
}

.tooltip.bs-tooltip-bottom .tooltip-arrow::before {
    border-bottom-color: var(--main-color);
}

.tooltip.bs-tooltip-start .tooltip-arrow::before {
    border-left-color: var(--main-color);
}

.tooltip.bs-tooltip-end .tooltip-arrow::before {
    border-right-color: var(--main-color);
}

/* Category-specific styling */
.specification-item[data-category="Performance"] {
    border-left-color: #28a745;
}

.specification-item[data-category="Physical"] {
    border-left-color: #17a2b8;
}

.specification-item[data-category="Regulatory"] {
    border-left-color: #ffc107;
}

.specification-item[data-category="Technical"] {
    border-left-color: #6f42c1;
}

.specification-item[data-category="Chemical"] {
    border-left-color: #e83e8c;
}

.specification-item[data-category="Storage"] {
    border-left-color: #fd7e14;
}

.specification-item[data-category="Quality"] {
    border-left-color: #20c997;
}

.specification-item[data-category="Construction"] {
    border-left-color: #6c757d;
}

.specification-item[data-category="Electrical"] {
    border-left-color: #dc3545;
}

.specification-item[data-category="General"] {
    border-left-color: var(--auxiliary-color);
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .specification-item {
        padding: 0.75rem;
    }
    
    .spec-label {
        font-size: 0.85rem;
    }
    
    .spec-value {
        font-size: 0.95rem;
    }
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover focus'
        });
    });
});
</script>
@endif 