@props(['product' => null])

@if($product)
<div class="technical-specifications">
    <h3>Technical Specifications</h3>
    <div class="specs-grid">
        <div class="spec-item">
            <strong>Model:</strong> {{ $product->model ?? "N/A" }}
        </div>
        <div class="spec-item">
            <strong>Brand:</strong> {{ $product->brand ? $product->brand->name : "N/A" }}
        </div>
        <div class="spec-item">
            <strong>Category:</strong> {{ $product->category ? $product->category->name : "N/A" }}
        </div>
        <div class="spec-item">
            <strong>Warranty:</strong> Manufacturer warranty included
        </div>
    </div>
</div>
@endif