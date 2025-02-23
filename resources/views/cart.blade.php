@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4 text-[#171e60] border-bottom pb-3">Your Shopping Cart</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm bg-green-100 border-green-500" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            {{ session('error') }}
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

    @if($cart)
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">Product</th>
                            <th class="py-3">Photo</th>
                            <th class="py-3">Quantity</th>
                            <th class="py-3">Price</th>
                            <th class="py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $id => $item)
                        <tr>
                            <td class="align-middle fw-semibold">{{ $item['name'] }}</td>
                            <td class="align-middle">
                                <img src="{{ asset($item['photo']) }}" alt="{{ $item['name'] }}" 
                                     class="rounded-3 shadow-sm" style="width: auto; height: auto; max-width: 100%; max-height: 100%; object-fit: contain;">
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill px-3 py-2">{{ $item['quantity'] }}</span>
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

        <div class="d-flex justify-content-end mt-4">
            <form action="" method="POST">
                @csrf
                <button type="submit" class="btn bg-[#171e60] hover:bg-[#0a5694] text-white btn-lg px-5 shadow-sm">
                    <i class="fas fa-shopping-cart me-2"></i>Proceed to Checkout
                </button>
            </form>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-[#171e60] mb-4"></i>
            <h3 class="text-[#171e60]">Your cart is empty</h3>
            <p class="text-gray-600 mb-4">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('products.index') }}" 
                class="btn bg-[#171e60] hover:bg-[#0a5694] text-white px-4">
                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
            </a>
        </div>
    @endif
</div>
@include('layouts.footer')
@endsection
