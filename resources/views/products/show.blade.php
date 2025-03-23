@extends('layouts.app')

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h2>{{ $product->name }}</h2>
                    <p>${{ number_format($product->price, 2) }}</p>
                    <p>{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection