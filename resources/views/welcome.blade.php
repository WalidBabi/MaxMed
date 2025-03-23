@extends('layouts.app')

@section('content')
<style>
    /* Enhanced Hero Section */
    .hero-section {
        position: relative;
        height: 500px;
        overflow: hidden;
    }
    
    .hero-slide {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        transition: opacity 0.8s ease-in-out;
    }
    
    .hero-content {
        position: relative;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        z-index: 10;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        color: white;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.2s;
    }
    
    .hero-subtitle {
        font-size: 1.4rem;
        margin-bottom: 2rem;
        color: white;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.4s;
    }
    
    .hero-btn {
        background-color: #171e60;
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        box-shadow: 0 4px 15px rgba(23, 30, 96, 0.4);
        position: relative;
        overflow: hidden;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.6s;
    }
    
    .hero-btn:hover {
        background-color: #0a5694;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(10, 86, 148, 0.5);
    }
    
    .hero-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }
    
    .hero-btn:hover::after {
        animation: ripple 1s ease-out;
    }
    
    .nav-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.4);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 20;
    }
    
    .nav-arrow:hover {
        background-color: rgba(0, 0, 0, 0.6);
        transform: translateY(-50%) scale(1.1);
    }
    
    .nav-arrow.left {
        left: 20px;
    }
    
    .nav-arrow.right {
        right: 20px;
    }
    
    .brand-border {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 10px;
        z-index: 30;
    }
    
    /* Features Section */
    .features-section {
        background-color: #f8f9fa;
        padding: 5rem 0;
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
        color: #333;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, #171e60, #0a5694);
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        border-radius: 2px;
    }
    
    .feature-slide {
        position: relative;
        height: 400px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.5s ease;
    }
    
    .feature-content {
        padding: 3rem;
        color: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.5s ease;
    }
    
    .feature-content:hover {
        transform: scale(1.05);
    }
    
    .feature-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .feature-desc {
        font-size: 1.2rem;
        line-height: 1.6;
    }
    
    .feature-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .feature-slide:hover .feature-img {
        transform: scale(1.05);
    }
    
    .feature-nav-dots {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 2rem;
    }
    
    .feature-dot {
        height: 3px;
        transition: all 0.3s ease;
        border-radius: 3px;
    }
    
    /* Suppliers Section */
    .suppliers-section {
        padding: 4rem 0;
    }
    
    .supplier-slide {
        height: 100px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        transition: opacity 0.5s ease;
    }
    
    .supplier-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        transition: all 0.3s ease;
    }
    
    .supplier-logo img {
        max-height: 60px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    .supplier-logo:hover img {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.1);
    }
    
    .supplier-nav-dots {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-top: 1.5rem;
    }
    
    .supplier-dot {
        height: 2px;
        transition: all 0.3s ease;
        border-radius: 2px;
    }
    
    /* Animations */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        100% {
            transform: scale(20, 20);
            opacity: 0;
        }
    }
    
    /* Responsive Styles */
    @media (max-width: 992px) {
        .hero-title {
            font-size: 3rem;
        }
        
        .hero-section {
            height: 450px;
        }
        
        .feature-content {
            padding: 2rem;
        }
        
        .feature-title {
            font-size: 1.8rem;
        }
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
        }
        
        .hero-section {
            height: 400px;
        }
        
        .supplier-slide {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .feature-slide {
            height: auto;
        }
        
        .feature-slide .d-flex {
            flex-direction: column;
        }
        
        .feature-slide .w-1/2 {
            width: 100%;
        }
        
        .feature-img {
            height: 200px;
        }
    }
    
    @media (max-width: 576px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1rem;
        }
        
        .hero-section {
            height: 350px;
        }
        
        .nav-arrow {
            width: 35px;
            height: 35px;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .feature-title {
            font-size: 1.5rem;
        }
    }
</style>

<!-- Hero Section -->
<div x-data="{ activeSlide: 0 }"
    x-init="setInterval(() => { activeSlide = activeSlide === 2 ? 0 : activeSlide + 1 }, 5000)"
    class="hero-section">
    <!-- Background Images -->
    <div class="hero-slide"
        x-show="activeSlide === 0"
        x-transition:enter="transition ease-out duration-800"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-800"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: linear-gradient(rgba(23, 30, 96, 0.6), rgba(23, 30, 96, 0.6)), url('/Images/banner.png'); background-size: cover; background-position: center;">
    </div>
    <div class="hero-slide"
        x-show="activeSlide === 1"
        x-transition:enter="transition ease-out duration-800"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-800"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: linear-gradient(rgba(23, 30, 96, 0.6), rgba(23, 30, 96, 0.6)), url('/Images/banner2.jpeg'); background-size: cover; background-position: center;">
    </div>
    <div class="hero-slide"
        x-show="activeSlide === 2"
        x-transition:enter="transition ease-out duration-800"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-800"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: linear-gradient(rgba(23, 30, 96, 0.6), rgba(23, 30, 96, 0.6)), url('/Images/banner3.jpg'); background-size: cover; background-position: center;">
    </div>

    <!-- Content -->
    <div class="hero-content">
        <div class="text-center px-4">
            <h2 class="hero-title">Lab And Medical Solutions</h2>
            <p class="hero-subtitle">Providing cutting-edge medical Laboratory equipment for modern laboratories</p>
            <a href="{{ route('products.index') }}" class="hero-btn inline-block">
                Explore Products
            </a>
        </div>
    </div>

    <!-- Navigation Arrows -->
    <button @click="activeSlide = activeSlide === 0 ? 2 : activeSlide - 1"
        class="nav-arrow left">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="activeSlide = activeSlide === 2 ? 0 : activeSlide + 1"
        class="nav-arrow right">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>
    
    <!-- Brand Border -->
    <div class="brand-border">
        <svg class="w-full h-10 relative" preserveAspectRatio="none" viewBox="0 0 100 10" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0 5 Q 25 0, 50 5 T 100 5" fill="none" stroke="url(#gradient)" stroke-width="2" />
            <defs>
                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color: #171e60" />
                    <stop offset="50%" style="stop-color: #0a5694" />
                    <stop offset="100%" style="stop-color: #171e60" />
                </linearGradient>
            </defs>
        </svg>
    </div>
</div>



<!-- Features Section -->
<div class="features-section py-16">
    <div class="max-w-7xl mx-auto px-4" x-data="{ activeSlide: 0, autoplay: null }" x-init="autoplay = setInterval(() => { activeSlide = activeSlide === 2 ? 0 : activeSlide + 1 }, 5000)">
        <h3 class="section-title">Why Choose MaxMed?</h3>

        <!-- Carousel container -->
        <div class="feature-slide shadow-lg">
            <!-- Slide 1 -->
            <div class="absolute inset-0 w-full h-full"
                x-show="activeSlide === 0"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="flex h-full">
                    <div class="w-1/2 bg-[#171e60]">
                        <div class="feature-content">
                            <h4 class="feature-title">Innovation First</h4>
                            <p class="feature-desc">Leading the industry with cutting-edge medical laboratory solutions that define the future of diagnostics.</p>
                        </div>
                    </div>
                    <div class="w-1/2 overflow-hidden">
                        <img src="{{ asset('/Images/Innovation.jpg') }}"
                            alt="Innovation"
                            class="feature-img">
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="absolute inset-0 w-full h-full"
                x-show="activeSlide === 1"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="flex h-full">
                    <div class="w-1/2 overflow-hidden">
                        <img src="{{ asset('/Images/bacteria.jpg') }}"
                            alt="Quality"
                            class="feature-img">
                    </div>
                    <div class="w-1/2 bg-[#0a5694]">
                        <div class="feature-content">
                            <h4 class="feature-title">Quality Assured</h4>
                            <p class="feature-desc">Every piece of equipment undergoes rigorous testing to ensure reliable performance and accurate results.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="absolute inset-0 w-full h-full"
                x-show="activeSlide === 2"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="flex h-full">
                    <div class="w-1/2 bg-[#171e60]">
                        <div class="feature-content">
                            <h4 class="feature-title">Expert Support</h4>
                            <p class="feature-desc">Our team of specialists provides comprehensive training and ongoing technical support.</p>
                        </div>
                    </div>
                    <div class="w-1/2 overflow-hidden">
                        <img src="{{ asset('/Images/Expert.jpg') }}"
                            alt="Support"
                            class="feature-img">
                    </div>
                </div>
            </div>

            <!-- Navigation dots -->
            <div class="absolute bottom-5 left-0 right-0 feature-nav-dots z-10">
                <button @click="activeSlide = 0"
                    class="feature-dot"
                    :class="activeSlide === 0 ? 'w-8 bg-[#171e60]' : 'w-3 bg-gray-400 hover:bg-[#171e60]'">
                    <span class="sr-only">Slide 1</span>
                </button>
                <button @click="activeSlide = 1"
                    class="feature-dot"
                    :class="activeSlide === 1 ? 'w-8 bg-[#171e60]' : 'w-3 bg-gray-400 hover:bg-[#171e60]'">
                    <span class="sr-only">Slide 2</span>
                </button>
                <button @click="activeSlide = 2"
                    class="feature-dot"
                    :class="activeSlide === 2 ? 'w-8 bg-[#171e60]' : 'w-3 bg-gray-400 hover:bg-[#171e60]'">
                    <span class="sr-only">Slide 3</span>
                </button>
            </div>

            <!-- Arrow buttons -->
            <button @click="activeSlide = activeSlide === 0 ? 2 : activeSlide - 1"
                class="nav-arrow left">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button @click="activeSlide = activeSlide === 2 ? 0 : activeSlide + 1"
                class="nav-arrow right">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>
</div>
<!-- Key Suppliers Section -->
<div class="suppliers-section max-w-7xl mx-auto px-4">
    <h3 class="section-title">Key Suppliers</h3>
    <div x-data="{ activeSupplier: 0 }"
        x-init="setInterval(() => { activeSupplier = activeSupplier === 1 ? 0 : activeSupplier + 1 }, 5000)"
        class="relative overflow-hidden">

        <!-- Supplier logos container with fixed height -->
        <div class="relative h-[100px]">
            <!-- Supplier logos - Group 1 -->
            <div class="supplier-slide absolute w-full"
                x-show="activeSupplier === 0"
                x-transition:enter="transition ease-out duration-800"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-800"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-full">
                <div class="supplier-logo">
                    <img src="{{ asset('/Images/supplier1.png') }}" alt="Supplier 1">
                </div>
                <div class="supplier-logo">
                    <img src="{{ asset('/Images/supplier2.png') }}" alt="Supplier 2">
                </div>
                <div class="supplier-logo">
                    <img src="{{ asset('/Images/supplier3.png') }}" alt="Supplier 3">
                </div>
                <div class="supplier-logo">
                    <img src="{{ asset('/Images/supplier4.png') }}" alt="Supplier 4">
                </div>
            </div>

            <!-- Supplier logos - Group 2 -->
            <div class="supplier-slide absolute w-full"
                x-show="activeSupplier === 1"
                x-transition:enter="transition ease-out duration-800"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-800"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-full">
                <div class="supplier-logo">
                    <img src="{{ asset('/Images/supplier5.png') }}" alt="Supplier 5">
                </div>
                <div class="supplier-logo">
                    <img src="{{ asset('/Images/supplier1.png') }}" alt="Supplier 1">
                </div>
                <div class="supplier-logo">
                    <img src="{{ asset('/Images/supplier2.png') }}" alt="Supplier 2">
                </div>
                <div class="supplier-logo">
                    <img src="{{ asset('/Images/supplier3.png') }}" alt="Supplier 3">
                </div>
            </div>
        </div>

        <!-- Navigation dots -->
        <div class="supplier-nav-dots">
            <button @click="activeSupplier = 0"
                class="supplier-dot"
                :class="activeSupplier === 0 ? 'w-6 bg-[#171e60]' : 'w-2 bg-gray-300 hover:bg-[#171e60]'">
            </button>
            <button @click="activeSupplier = 1"
                class="supplier-dot"
                :class="activeSupplier === 1 ? 'w-6 bg-[#171e60]' : 'w-2 bg-gray-300 hover:bg-[#171e60]'">
            </button>
        </div>
    </div>
</div>

@include('layouts.footer')
@endsection