@extends('layouts.app')

@section('title', 'Medical Laboratory Equipment & Supplies | MaxMed UAE')

@section('content')
<style>
    .lab-alert {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 8px 32px 0 rgba(31, 38, 135, 0.37),
            inset 0 0 80px rgba(255, 255, 255, 0.3);
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

    .lab-alert.error {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 255, 255, 0.9));
    }

    .lab-alert.success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.1), rgba(255, 255, 255, 0.9));
    }

    .microscope-lens {
        position: relative;
        overflow: hidden;
        border-radius: 50%;
    }

    .microscope-lens::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(
            circle at center,
            rgba(255, 255, 255, 0.8) 0%,
            rgba(255, 255, 255, 0.1) 50%,
            transparent 100%
        );
        pointer-events: none;
    }
</style>

<div class="container-fluid mb-3">
    @if(session('error'))
        <div class="alert border-0 rounded-3 shadow-lg mb-4 position-relative" role="alert"
             x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2">
            <div class="lab-alert error d-flex align-items-center p-3 rounded-3" 
                 style="border-left: 4px solid #dc3545;">
                <div class="microscope-lens d-flex align-items-center justify-content-center bg-danger bg-opacity-25 rounded-circle p-2 me-3">
                    <i class="fas fa-flask-vial fs-5 text-danger"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-microscope me-2"></i>
                        <strong class="me-2">Lab Alert:</strong>
                        {{ session('error') }}
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <small class="text-danger me-2">
                        <i class="fas fa-atom fa-spin"></i>
                    </small>
                    <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert border-0 rounded-3 shadow-lg mb-4 position-relative" role="alert"
             x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2">
            <div class="lab-alert success d-flex align-items-center p-3 rounded-3"
                 style="border-left: 4px solid #198754;">
                <div class="microscope-lens d-flex align-items-center justify-content-center bg-success bg-opacity-25 rounded-circle p-2 me-3">
                    <i class="fas fa-vial-circle-check fs-5 text-success"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-flask me-2"></i>
                        <strong class="me-2">Lab Success:</strong>
                        {{ session('success') }}
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <small class="text-success me-2">
                        <i class="fas fa-atom fa-spin"></i>
                    </small>
                    <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    if (alert.__x) {
                        alert.__x.$data.show = false;
                    }
                });
            }, 5000);
        });
    </script>

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
                @if($products->isEmpty())
                    <div class="col-12 text-center">
                        <h2 class="text-muted text-center mt-5 lab-equipment-style" style="font-size: 3rem; font-weight: bold;">Coming Soon</h2>
                    </div>
                @else
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
                                    <form x-data="{ 
                                        showMessage: false, 
                                        messageText: '',
                                        quantity: 0,
                                        async checkAvailability() {
                                            const response = await fetch(`/check-availability/${this.$el.getAttribute('data-product-id')}/${this.quantity}`);
                                            const data = await response.json();
                                            return data.available;
                                        },
                                        async redirectToAuth() {
                                            @auth
                                                const available = await this.checkAvailability();
                                                if (!available) {
                                                    this.messageText = 'Requested quantity exceeds available stock!';
                                                    this.showMessage = true;
                                                    setTimeout(() => this.showMessage = false, 3000);
                                                    return;
                                                }
                                                this.$el.submit();
                                            @else
                                                window.location.href = '{{ route('register') }}';
                                            @endauth
                                        }
                                    }" 
                                    data-product-id="{{ $product->id }}"
                                    action="{{ route('cart.add', $product->id) }}" 
                                    method="POST" 
                                    class="d-flex gap-2 position-relative"
                                    @submit.prevent="redirectToAuth()"
                                    x-show="{{ $product->inStock() }}">
                                        @csrf
                                        <div class="input-group" style="width: 120px;">
                                            <button type="button" class="btn btn-outline-secondary"
                                                @click="if (quantity > 0) quantity--">
                                                -
                                            </button>
                                            <input type="number"
                                                name="quantity"
                                                x-model="quantity"
                                                min="0"
                                                max="{{ $product->inventory->quantity }}"
                                                class="form-control text-center"
                                                style="width: 50px;"
                                                readonly>
                                            <button type="button" class="btn btn-outline-secondary"
                                                @click="if(quantity < {{ $product->inventory->quantity }}) {quantity++} else { messageText = 'No more items in stock!'; showMessage = true; setTimeout(() => showMessage = false, 3000); }">
                                                +
                                            </button>
                                        </div>
                                        <button type="submit" 
                                            class="btn bg-[#171e60] text-white hover:bg-[#0a5694]" 
                                            :disabled="quantity == 0">
                                            <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                        </button>
                                        <div x-show="showMessage" x-transition
                                            class="position-absolute top-100 start-50 translate-middle-x mt-2 bg-danger text-white px-3 py-1 rounded small z-10"
                                            x-text="messageText">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection