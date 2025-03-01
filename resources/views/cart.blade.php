@extends('layouts.app')

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
        <div class="cart-items-section mb-4">
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-[#171e60] text-white">
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
                            <tr>
                                <td class="align-middle fw-semibold">{{ $item['name'] }}</td>
                                <td class="align-middle">
                                    <div class="microscope-lens" style="width: 80px; height: 80px;">
                                        <img src="{{ asset($item['photo']) }}" alt="{{ $item['name'] }}" 
                                             class="rounded-3 shadow-sm" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-[#171e60] rounded-pill px-3 py-2">{{ $item['quantity'] }}</span>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="text-success fw-bold">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                </td>
                                <td class="align-middle">
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
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

        <!-- Checkout Section -->
        <div class="checkout-section">
            <div class="d-flex justify-content-end mt-4">
                <form action="{{ route('stripe.checkout') }}" method="POST" id="checkout-form">
                    @csrf
                    <button type="submit" class="btn btn-lg px-5 shadow-sm" style="background-color: #635BFF; color: white; border-radius: 4px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M33.3333 6.66667H6.66667C4.8 6.66667 3.33333 8.13333 3.33333 10V30C3.33333 31.8667 4.8 33.3333 6.66667 33.3333H33.3333C35.2 33.3333 36.6667 31.8667 36.6667 30V10C36.6667 8.13333 35.2 6.66667 33.3333 6.66667Z" fill="white"/>
                            <path d="M23.3333 20C23.3333 22.7667 21.1 25 18.3333 25C15.5667 25 13.3333 22.7667 13.3333 20C13.3333 17.2333 15.5667 15 18.3333 15C21.1 15 23.3333 17.2333 23.3333 20Z" fill="#635BFF"/>
                            <path d="M30 20C30 22.7667 27.7667 25 25 25V15C27.7667 15 30 17.2333 30 20Z" fill="#635BFF"/>
                            <path d="M11.6667 15V25C8.9 25 6.66667 22.7667 6.66667 20C6.66667 17.2333 8.9 15 11.6667 15Z" fill="#635BFF"/>
                        </svg>
                        Checkout
                    </button>
                </form>
            </div>
        </div>
    @else
        <!-- Empty Cart Section -->
        <div class="empty-cart-section">
            <div class="text-center py-5" style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 10px; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);">
                <i class="fas fa-flask fa-4x text-[#171e60] mb-4"></i>
                <h3 class="text-[#171e60]">Your laboratory cart is empty</h3>
                <p class="text-gray-600 mb-4">Looks like you haven't added any laboratory equipment to your cart yet.</p>
                <a href="{{ route('products.index') }}" 
                    class="btn bg-[#171e60] hover:bg-[#0a5694] text-white px-4">
                    <i class="fas fa-microscope me-2"></i>Browse Laboratory Equipment
                </a>
            </div>
        </div>
    @endif
</div>
@include('layouts.footer')
@endsection
