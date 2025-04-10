@extends('layouts.app')

@section('title', 'Laboratory Equipment Subcategories | MaxMed UAE')

@section('meta_description', 'Browse subcategories of laboratory equipment at MaxMed UAE. Find premium scientific instruments and medical supplies for hospitals, research facilities, and medical centers in Dubai.')

@section('meta_keywords', 'laboratory subcategories Dubai, lab equipment UAE, scientific subcategories, laboratory supplies, research lab categories')

@section('content')
<style>
    /* Enhanced Lab Alert Styling */
    .lab-alert {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 8px 32px 0 rgba(31, 38, 135, 0.37),
            inset 0 0 80px rgba(255, 255, 255, 0.3);
        position: relative;
        transition: all 0.3s ease;
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
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .lab-alert:hover .microscope-lens {
        transform: scale(1.1) rotate(5deg);
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
    
    /* Enhanced Page Layout */
    .page-header {
        margin-bottom: 1.5rem;
        position: relative;
        border-left: 4px solid #17a2b8;
        padding-left: 15px;
    }
    
    .page-title {
        font-size: 1.25rem;
     
        color: #333;
        margin-bottom: 0.5rem;
        transition: color 0.3s;
    }
    
    .page-description {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        max-width: 800px;
        line-height: 1.6;
    }
    
    

    /* Enhanced Subcategory Card Styling */
    .subcategory-container {
        margin-top: 15px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 28px;
    }
    
    .subcategory-card-wrapper {
        height: 100%;
        transition: transform 0.3s;
        opacity: 0;
        transform: translateY(20px);
    }
    
    .subcategory-card {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
       
        overflow: hidden;
        transition: all 0.35s;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: none !important;
        background-color: #fff;
        position: relative;
    }
    
    .subcategory-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
    }

    .subcategory-card img {
        height: 180px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .subcategory-card:hover img {
        transform: scale(1.08);
    }
    
    .subcategory-card .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 22px;
        position: relative;
    }
    
    .subcategory-card .card-title {
        text-align: center;
        font-size: 0.875rem;
       
        color: #222;
        margin-bottom: 0;
        position: relative;
        transition: color 0.3s;
    }
    
    .subcategory-card:hover .card-title {
        color: #17a2b8;
    }
    
    .subcategory-card .card-title:after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #17a2b8;
        transition: width 0.3s;
    }
    
    .subcategory-card:hover .card-title:after {
        width: 50%;
    }
    
    /* Lab theme badge for subcategories */
    .lab-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(23, 162, 184, 0.9);
        color: white;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 10;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        transition: all 0.3s;
    }
    
    .subcategory-card:hover .lab-badge {
        background: rgba(40, 167, 69, 0.9);
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
    }
    
    /* Responsive improvements */
    @media (max-width: 992px) {
        .subcategory-container {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.25rem;
        }
        .subcategory-container {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        .subcategory-card .card-title {
            font-size: 1.1rem;
        }
    }
    
    @media (max-width: 576px) {
        .subcategory-container {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
        }
        .subcategory-card img {
            height: 140px;
        }
        .subcategory-card .card-body {
            padding: 15px 10px;
        }
        .lab-badge {
            font-size: 0.7rem;
            padding: 4px 8px;
            top: 10px;
            right: 10px;
        }
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

    <div class="row">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9 mt-2">
            <div class="page-header mb-4">
                <h1 class="page-title">{{ $category->name }}</h1>
                <p class="page-description">Browse subcategories of {{ $category->name }}. Explore our range of high-quality laboratory equipment and scientific instruments.</p>
                
               
            </div>
            
            <div class="subcategory-container">
                @foreach($category->subcategories as $subcategory)
                <div class="subcategory-card-wrapper">
                    <a href="{{ $subcategory->hasSubcategories() ? route('categories.subcategory.show', [$category, $subcategory]) : route('categories.subcategory.show', [$category, $subcategory]) }}" class="text-decoration-none">
                        <div class="subcategory-card">
                           
                            <div class="overflow-hidden">
                                @if(isset($subcategory->image_url))
                                <img src="{{ $subcategory->image_url }}" class="card-img-top" alt="{{ $subcategory->name }}">
                                @else
                                <img src="{{ asset('images/default-subcategory.jpg') }}" class="card-img-top" alt="{{ $subcategory->name }}">
                                @endif
                            </div>
                            <div class="card-body">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Alert auto-dismiss
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                if (alert.__x) {
                    alert.__x.$data.show = false;
                }
            });
        }, 5000);
        
        // Add smooth fade-in effect to subcategory cards
        const subcategoryCards = document.querySelectorAll('.subcategory-card-wrapper');
        subcategoryCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 50));
        });
    });
</script>

@include('layouts.footer')
@endsection 