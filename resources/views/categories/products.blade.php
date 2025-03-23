@extends('layouts.app')

@section('content')
<div class="container-fluid mb-3 mt-3">
    <style>
        .product-card {
            height: 100%;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body {
            flex: 1 1 auto;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 15px;
            justify-content: space-between;
        }
        .card-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: bold;
            font-size: 1.1em;
        }
        .card-text {
            color: #555;
            margin-bottom: 10px;
        }
        .badge {
            font-size: 0.9em;
        }
        .btn {
            border-radius: 5px;
            margin-top: 5px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
            transition: background-color 0.3s, transform 0.3s;
            padding: 10px 20px;
            font-size: 0.9em;
        }
        .btn-primary:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
            transition: background-color 0.3s, transform 0.3s;
            padding: 10px 20px;
            font-size: 0.9em;
        }
        .btn-secondary:hover {
            background-color: #138496;
            transform: translateY(-2px);
        }
        .btn-outline-secondary {
            border-color: #6c757d;
        }
        .card-footer {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .quantity-controls input {
            width: 60px;
            text-align: center;
        }
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
    </style>
    <div class="row">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9">
            
            <div class="row">
                @if($products->isEmpty())
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            Coming Soon
                        </div>
                    </div>
                @else
                    @foreach($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text">${{ number_format($product->price, 2) }}</p>
                                <div class="card-footer">
                                    @if($product->inventory->quantity > 0)
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-success me-3">In Stock</span>
                                        </div>
                                        <div class="button-group">
                                            <a href="{{ route('quotation.form', $product) }}" class="btn btn-secondary">
                                                Request Quotation
                                            </a>
                                        </div>
                                    @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                        <div class="button-group">
                                            <a href="{{ route('quotation.form', $product) }}" class="btn btn-secondary">
                                                Request Quotation
                                            </a>
                                        </div>
                                    @endif
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
@endsection 