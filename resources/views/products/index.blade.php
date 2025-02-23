@extends('layouts.app')

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 p-4">
            <div class="sidebar-wrapper rounded shadow-sm bg-white p-4">
                <h2 class="mb-4 border-bottom pb-2">Categories</h2>
                <div class="list-group list-group-flush" x-data>
                    <a href="{{ route('products.index') }}"
                        class="list-group-item list-group-item-action d-flex align-items-center {{ !request('category') ? 'bg-[#171e60] text-white' : '' }}"
                        @click.prevent="window.location.href = $el.href">
                        <i class="fas fa-th-large me-2"></i>
                        All Products
                    </a>
                    @foreach(\App\Models\Category::all() as $category)
                    <a href="{{ route('products.index', ['category' => $category->name]) }}"
                        class="list-group-item list-group-item-action d-flex align-items-center {{ request('category') === $category->name ? 'active' : '' }}"
                        @click.prevent="window.location.href = $el.href">
                        <i class="fas fa-microscope me-2"></i>
                        {{ $category->name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9 p-4">
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover:shadow transition">
                        <div class="position-relative" style="height: 300px;">
                            <a href="{{ asset($product->image_url) }}" target="_blank">
                                <img src="{{ asset($product->image_url) }}" class="img-fluid transition" alt="{{ $product->name }}" style="height: 70%; object-fit: cover; transform: scale(1);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                            @if($product->category)
                            <span class="position-absolute bottom-0 end-0 bg-[#171e60] text-white m-2 px-2 py-1 rounded-pill small">
                                {{ $product->category->name }}
                            </span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-success fw-bold mb-2">${{ number_format($product->price, 2) }}</p>
                            @if($product->inStock())
                            <span class="badge bg-success mb-3 w-25">In Stock</span>
                            @else
                            <span class="badge bg-danger mb-3 w-25">Out of Stock</span>
                            @endif
                            <div class="mt-auto d-grid gap-2 ">
                                <a href="{{ route('product.show', $product) }}" 
                                    class="btn border-[#171e60] text-[#171e60] hover:bg-[#171e60] hover:text-white">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </a>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex gap-2 position-relative" x-data="{ showMessage: false }">
                                    @csrf
                                    <div class="input-group" style="width: 120px;">
                                        <button type="button" class="btn btn-outline-secondary"
                                            @click="let input = $refs.qtyInput; if (input.value > 1) input.stepDown()">
                                            -
                                        </button>
                                        <input type="number"
                                            name="quantity"
                                            value="0"
                                            min="0"
                                            max="{{ $product->inventory->quantity }}"
                                            class="form-control text-center"
                                            style="width: 50px;"
                                            x-ref="qtyInput"
                                            readonly
                                            {{ !$product->inStock() ? 'disabled' : '' }}>
                                        <button type="button" class="btn btn-outline-secondary"
                                            @click="let input = $refs.qtyInput; if(input.value < input.max) {input.stepUp()} else { showMessage = true; setTimeout(() => showMessage = false, 3000); }">
                                            +
                                        </button>
                                    </div>
                                    <button type="submit" 
                                        class="btn bg-[#171e60] text-white hover:bg-[#0a5694]" 
                                        {{ !$product->inStock() ? 'disabled' : '' }}>
                                        <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                    </button>
                                    <div x-show="showMessage" x-transition
                                        class="position-absolute top-100 start-50 translate-middle-x mt-2 bg-danger text-white px-3 py-1 rounded small z-10">
                                        No more items in stock!
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection