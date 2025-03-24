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

            <!-- Display validation errors -->
            @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Request Quotation for {{ $product->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('quotation.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity Required</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Specific Requirements</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="3" 
                                    placeholder="Any specific requirements or customizations?"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                    placeholder="Any additional information or questions?"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Quote Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection