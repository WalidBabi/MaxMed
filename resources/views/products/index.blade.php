@extends('layouts.app')

@section('title', 'Medical Laboratory Equipment & Supplies | MaxMed UAE')

@section('content')
<style>
    .lab-alert {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 8px 32px 0 rgba(31, 38, 135, 0.37),
            inset 0 0 80px rgba(255, 255, 255, 0.3);
    }

    .lab-alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 50%;
        background: linear-gradient(
            180deg,
            rgba(255, 255, 255, 0.3) 0%,
            rgba(255, 255, 255, 0.1) 100%
        );
        border-radius: 8px 8px 0 0;
        pointer-events: none;
    }

    .lab-alert.error {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 255, 255, 0.9));
    }

    .lab-alert.success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.1), rgba(255, 255, 255, 0.9));
    }

    .microscope-lens {
        position: relative;
        overflow: hidden;
        border-radius: 50%;
    }

    .microscope-lens::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(
            circle at center,
            rgba(255, 255, 255, 0.8) 0%,
            rgba(255, 255, 255, 0.1) 50%,
            transparent 100%
        );
        pointer-events: none;
    }

    .subcategory-list {
        display: none;
    }

    .category-item:hover .subcategory-list {
        display: block;
    }

    .category-card {
        width: 100%;
        height: 250px;
        display: flex;
        flex-direction: column;
    }

    .category-card img {
        height: 150px;
        object-fit: cover;
    }

    .category-card .card-body {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="container-fluid mb-3 mt-3">
    @if(session('error'))
        <div class="alert border-0 rounded-3 shadow-lg mb-4 position-relative" role="alert"
             x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2">
            <div class="lab-alert error d-flex align-items-center p-3 rounded-3" 
                 style="border-left: 4px solid #dc3545;">
                <div class="microscope-lens d-flex align-items-center justify-content-center bg-danger bg-opacity-25 rounded-circle p-2 me-3">
                    <i class="fas fa-flask-vial fs-5 text-danger"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-microscope me-2"></i>
                        <strong class="me-2">Lab Alert:</strong>
                        {{ session('error') }}
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <small class="text-danger me-2">
                        <i class="fas fa-atom fa-spin"></i>
                    </small>
                    <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert border-0 rounded-3 shadow-lg mb-4 position-relative" role="alert"
             x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2">
            <div class="lab-alert success d-flex align-items-center p-3 rounded-3"
                 style="border-left: 4px solid #198754;">
                <div class="microscope-lens d-flex align-items-center justify-content-center bg-success bg-opacity-25 rounded-circle p-2 me-3">
                    <i class="fas fa-vial-circle-check fs-5 text-success"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-flask me-2"></i>
                        <strong class="me-2">Lab Success:</strong>
                        {{ session('success') }}
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <small class="text-success me-2">
                        <i class="fas fa-atom fa-spin"></i>
                    </small>
                    <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    if (alert.__x) {
                        alert.__x.$data.show = false;
                    }
                });
            }, 5000);
        });
    </script>

    <div class="row">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9">
            <div class="row">
                @foreach($categories as $category)
                <div class="col-md-4 mb-4">
                    <a href="{{ route('categories.show', $category) }}" class="text-decoration-none">
                        <div class="card category-card h-100 border-0 shadow-sm">
                            <img src="{{ $category->image_url }}" class="card-img-top" alt="{{ $category->name }}">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $category->name }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection