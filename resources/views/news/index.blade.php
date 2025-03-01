@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-3xl font-bold">News & Updates</h1>
            <p class="text-gray-600">Stay updated with the latest information from MaxMed</p>
        </div>
    </div>

    @if(count($news) > 0)
        <div class="row g-4">
            @foreach($news as $item)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm hover:shadow-md transition-shadow">
                        @if($item->image_url)
                            <img src="{{ asset($item->image_url) }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title font-bold text-xl mb-2">{{ $item->title }}</h5>
                            <p class="card-text text-gray-500 mb-2">
                                <small>{{ $item->created_at->format('F d, Y') }}</small>
                            </p>
                            <p class="card-text">{{ Str::limit($item->content, 150) }}</p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="{{ route('news.show', $item) }}" class="btn btn-outline-primary">Read More</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-12 text-center py-5">
                <p class="text-gray-500">No news articles available at the moment.</p>
            </div>
        </div>
    @endif
</div>
@include('layouts.footer')
@endsection