@extends('layouts.app')

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9">
            <div class="row">
                @foreach($categories as $category)
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $category->name }}</h5>
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-primary mt-auto">
                                View {{ $category->subcategories->isNotEmpty() ? 'Subcategories' : 'Products' }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection 