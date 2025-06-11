@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('news.index') }}">News</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $news->title }}</li>
                </ol>
            </nav>
            
            <article class="bg-white p-4 rounded shadow-sm">
                <h1 class="mb-3">{{ $news->title }}</h1>
                
                <div class="text-muted mb-4">
                    <small>Published on {{ $news->created_at->format('F d, Y') }}</small>
                </div>
                
                @if($news->image_url)
                    <div class="mb-4">
                        <img src="{{ asset($news->image_url) }}" alt="{{ $news->title }}" class="img-fluid rounded">
                    </div>
                @endif
                
                <div class="news-content">
                    {!! nl2br(e($news->content)) !!}
                </div>
                
                <div class="mt-4 pt-3 border-top">
                    <a href="{{ route('news.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to News
                    </a>
                </div>
            </article>
        </div>
    </div>
</div>
{{-- Footer is included in app.blade.php --}}
@endsection 