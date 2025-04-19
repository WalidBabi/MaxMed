@extends('layouts.app')

@section('title', 'Your Shopping Cart | MaxMed UAE')
@section('meta_description', 'Review your laboratory equipment and medical supplies in your MaxMed UAE cart. Secure checkout for scientific instruments and lab equipment in Dubai.')

@section('content')
<!-- Add noindex for empty carts to prevent soft 404s -->
@if(!$cartItems || $cartItems->isEmpty())
    @push('head')
    <meta name="robots" content="noindex, follow">
    @endpush
@endif

<div class="container py-5">
    <h1 class="mb-4">Your Cart</h1>
    
    @if(!$cartItems || $cartItems->isEmpty())
        <div class="alert alert-info">
            <p>Your cart is currently empty.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Browse Products</a>
        </div>
    @else
        <!-- Rest of existing cart display code -->
        // ... existing code ...
    @endif
</div>
@endsection 