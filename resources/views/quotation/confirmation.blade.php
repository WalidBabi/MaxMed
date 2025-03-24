@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Success and Error Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Quotation Request Confirmation</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                    </div>
                    <h5 class="mb-3">Your request for "{{ $product->name }}" has been submitted.</h5>
                    <p>We will review your request and send you a quotation soon.</p>
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-primary">Browse More Products</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 