@extends('layouts.app')

@section('content')
<div class="container-fluid mb-3 mt-3">
    <div class="row">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9">
          
            <div class="row">
                @foreach($category->subcategories as $subcategory)
                <div class="col-md-4 mb-4">
                    <a href="{{ route('categories.subcategory.show', [$category, $subcategory]) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm custom-card">
                            <img src="{{ $subcategory->image_url }}" class="card-img-top" alt="{{ $subcategory->name }}">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $subcategory->name }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection 