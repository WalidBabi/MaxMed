@extends('layouts.app')

@section('title', 'Your Laboratory Cart | MaxMed UAE')
@section('meta_description', 'Review and checkout laboratory equipment in your MaxMed UAE shopping cart. Secure payment options for scientific instruments in Dubai.')

@if(!session()->has('cart') || count(session('cart')) == 0)
    @section('meta_robots', 'noindex, follow')
@endif

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4 text-[#171e60] border-bottom pb-3">
                <i class="fas fa-flask me-2"></i>Your Laboratory Cart
            </h1>
        </div>
    </div>

    <!-- Alerts Section -->
    <div class="alerts-container mb-4">
        @if(session('success'))
            <div class="alert lab-alert success alert-dismissible fade show shadow-sm border-green-500 position-relative" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert lab-alert error alert-dismissible fade show shadow-sm position-relative" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <script>
            // Automatically hide alerts after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        </script>
    </div>

    <!-- Cart Items Section -->
    @if(session()->has('cart') && count(session('cart')) > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-lg border-0 rounded-3 overflow-hidden" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                    <div class="card-header bg-[#171e60] text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Cart Items ({{ count(session('cart')) }})</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3"><i class="fas fa-vial me-2"></i>Product</th>
                                    <th class="py-3"><i class="fas fa-microscope me-2"></i>Photo</th>
                                    <th class="py-3"><i class="fas fa-balance-scale me-2"></i>Quantity</th>
                                    <th class="py-3"><i class="fas fa-tag me-2"></i>Price</th>
                                    <th class="py-3"><i class="fas fa-cogs me-2"></i>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $item)
                                <tr class="border-bottom">
                                    <td class="align-middle fw-semibold">
                                        <div class="d-flex flex-column">
                                            <span class="text-[#171e60]">{{ $item['name'] }}</span>
                                            <small class="text-muted">Item #{{ $id }}</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="microscope-lens rounded-circle border border-2 border-light shadow-sm overflow-hidden" style="width: 70px; height: 70px;">
                                            <img src="{{ asset($item['photo']) }}" alt="{{ $item['name'] }}" 
                                                class="h-100 w-100 object-cover transition-transform hover:scale-110" style="object-fit: cover;">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-[#171e60] rounded-pill px-3 py-2">{{ $item['quantity'] }}</span>
                                           
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="text-success fw-bold">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            <small class="text-muted">${{ number_format($item['price'], 2) }} each</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm hover:bg-danger hover:text-white transition-colors">
                                                <i class="fas fa-trash-alt me-1"></i>Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-lg border-0 rounded-3 sticky-top" style="top: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                    <div class="card-header bg-[#171e60] text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-medium text-secondary">Subtotal</span>
                            <span class="fw-bold">
                                ${{ number_format(array_sum(array_map(function($item) { 
                                    return $item['price'] * $item['quantity']; 
                                }, $cart)), 2) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-medium text-secondary">Lab Equipment Tax</span>
                            <span class="fw-bold">
                                ${{ number_format(array_sum(array_map(function($item) { 
                                    return $item['price'] * $item['quantity'] * 0.05; 
                                }, $cart)), 2) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-medium text-secondary">Shipping</span>
                            <span class="fw-bold text-success">Free</span>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold fs-5 text-success">
                                ${{ number_format(array_sum(array_map(function($item) { 
                                    return $item['price'] * $item['quantity'] * 1.05; 
                                }, $cart)), 2) }}
                            </span>
                        </div>
                        <form action="{{ route('stripe.checkout') }}" method="POST" id="checkout-form" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-lg w-100 shadow-sm" style="background-color: #635BFF; color: white; border-radius: 4px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s ease;">
                                <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M33.3333 6.66667H6.66667C4.8 6.66667 3.33333 8.13333 3.33333 10V30C3.33333 31.8667 4.8 33.3333 6.66667 33.3333H33.3333C35.2 33.3333 36.6667 31.8667 36.6667 30V10C36.6667 8.13333 35.2 6.66667 33.3333 6.66667Z" fill="white"/>
                                    <path d="M23.3333 20C23.3333 22.7667 21.1 25 18.3333 25C15.5667 25 13.3333 22.7667 13.3333 20C13.3333 17.2333 15.5667 15 18.3333 15C21.1 15 23.3333 17.2333 23.3333 20Z" fill="#635BFF"/>
                                    <path d="M30 20C30 22.7667 27.7667 25 25 25V15C27.7667 15 30 17.2333 30 20Z" fill="#635BFF"/>
                                    <path d="M11.6667 15V25C8.9 25 6.66667 22.7667 6.66667 20C6.66667 17.2333 8.9 15 11.6667 15Z" fill="#635BFF"/>
                                </svg>
                                Proceed to Checkout
                            </button>
                        </form>
                        <div class="d-flex justify-content-center align-items-center mt-3 text-muted">
                            <i class="fas fa-lock me-2"></i>
                            <small>Secure checkout with Stripe</small>
                        </div>
                    </div>
                </div>
                <div class="card mt-3 shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="fas fa-shopping-bag me-2"></i>Continue Shopping</h6>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-microscope me-2"></i>Browse More Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart Section -->
        <div class="empty-cart-section">
            <div class="card border-0 shadow-lg bg-white rounded-3 overflow-hidden">
                <div class="card-body text-center py-5">
                    <div class="empty-cart-animation mb-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                            <i class="fas fa-flask fa-4x text-[#171e60] opacity-75"></i>
                        </div>
                    </div>
                    <h3 class="text-[#171e60] fw-bold">Your Laboratory Cart is Empty</h3>
                    <p class="text-muted mb-4 px-4 mx-auto" style="max-width: 500px;">
                        Looks like you haven't added any laboratory equipment to your cart yet. 
                        Explore our collection to find the perfect tools for your research.
                    </p>
                    <a href="{{ route('products.index') }}" 
                        class="btn btn-lg px-4 py-2 shadow-sm" style="background-color: #171e60; color: white;">
                        <i class="fas fa-microscope me-2"></i>Browse Laboratory Equipment
                    </a>
                </div>
            </div>
         
        </div>
    @endif
</div>

<style>
    /* Custom animations and transitions */
    .transition {
        transition: all 0.3s ease;
    }
    
    .hover\:shadow-md:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .hover\:scale-110:hover {
        transform: scale(1.1);
    }
    
    .hover\:bg-danger:hover {
        background-color: #dc3545 !important;
    }
    
    .hover\:text-white:hover {
        color: white !important;
    }
    
    .transition-colors {
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .transition-transform {
        transition: transform 0.3s ease;
    }
    
    .lab-alert {
        border-left: 4px solid;
        padding-left: 1rem;
    }
    
    .lab-alert.success {
        border-left-color: #28a745;
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .lab-alert.error {
        border-left-color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    /* Cart responsive styles */
    @media (max-width: 992px) {
        .sticky-top {
            position: relative;
            top: 0 !important;
        }
    }
    
    /* Empty cart animation */
    .empty-cart-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }
</style>

{{-- Footer is included in app.blade.php --}}
@endsection
