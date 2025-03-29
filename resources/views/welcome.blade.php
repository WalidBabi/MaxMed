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
        
        .feature-content {
            padding: 1.5rem;
        }
        
        .feature-desc {
            font-size: 1rem;
        }
        
        .feature-dot {
            height: 4px;
            width: 25px;
        }
    }

    /* Replace the Nebula CSS with this Biology-themed simulation */
    .particle-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 5;
        background: radial-gradient(ellipse at center, rgba(0, 60, 80, 0.3) 0%, rgba(0, 40, 60, 0.4) 100%);
    }

    .bio-particle {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        opacity: 0;
        z-index: 5;
    }

    .cell {
        background-color: rgba(50, 200, 180, 0.15);
        border: 2px solid rgba(100, 240, 220, 0.3);
        box-shadow: inset 0 0 15px rgba(80, 220, 200, 0.3);
        filter: blur(0.5px);
    }

    .cell-nucleus {
        background-color: rgba(30, 120, 180, 0.3);
        border: 1px solid rgba(60, 150, 210, 0.4);
        box-shadow: 0 0 8px rgba(40, 130, 190, 0.2);
    }

    .molecule {
        background-color: transparent;
        border: 1.5px solid rgba(220, 240, 255, 0.5);
        transform: rotate(0deg);
    }

    .dna-segment {
        position: absolute;
        height: 4px;
        background-color: rgba(100, 200, 255, 0.4);
        transform-origin: center left;
        border-radius: 2px;
    }

    .microorganism {
        background-color: rgba(130, 210, 90, 0.2);
        border: 1px solid rgba(160, 230, 120, 0.3);
        border-radius: 40% 60% 60% 40% / 60% 30% 70% 40%;
        filter: blur(1px);
    }

    .protein {
        background-color: rgba(220, 150, 70, 0.25);
        border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        border: 1px solid rgba(240, 170, 90, 0.3);
    }

    .connection {
        position: absolute;
        background-color: rgba(100, 200, 255, 0.15);
        transform-origin: left center;
        height: 1px;
        pointer-events: none;
        z-index: 4;
    }

    @keyframes floatBioParticle {
        0% {
            transform: translate(0, 0) rotate(0deg);
            opacity: 0;
        }
        15% {
            opacity: var(--max-opacity);
        }
        85% {
            opacity: var(--max-opacity);
        }
        100% {
            transform: translate(var(--move-x), var(--move-y)) rotate(var(--rotation));
            opacity: 0;
        }
    }

    @keyframes pulseBioParticle {
        0%, 100% {
            transform: scale(1) rotate(var(--rotation));
        }
        50% {
            transform: scale(1.1) rotate(var(--rotation));
        }
    }

    @keyframes mitosis {
        0%, 100% { 
            clip-path: circle(50% at 50% 50%);
        }
        50% { 
            clip-path: inset(0 50% 0 0);
        }
    }

    @keyframes dnaRotate {
        0% { transform: rotate(0deg) translateY(2px); }
        100% { transform: rotate(360deg) translateY(-2px); }
    }
</style>

<!-- Hero Section -->
<div x-data="{ activeSlide: 0, particleSystem: null }"
    x-init="
        setInterval(() => { activeSlide = activeSlide === 2 ? 0 : activeSlide + 1 }, 5000);
        particleSystem = initBiologyParticles();
    "
    class="hero-section">
    
    <!-- Add Particle Container -->
    <div class="particle-container" id="particle-container"></div>
    
    <!-- Background Images (biology-themed gradients) -->
    <div class="hero-slide"
        x-show="activeSlide === 0"
        x-transition:enter="transition ease-out duration-800"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-800"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: linear-gradient(rgba(0, 60, 80, 0.7), rgba(0, 80, 100, 0.6)), url('/Images/banner.png'); background-size: cover; background-position: center;">
    </div>
    <div class="hero-slide"
        x-show="activeSlide === 1"
        x-transition:enter="transition ease-out duration-800"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-800"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: linear-gradient(rgba(20, 80, 90, 0.7), rgba(0, 100, 110, 0.6)), url('/Images/banner2.jpeg'); background-size: cover; background-position: center;">
    </div>
    <div class="hero-slide"
        x-show="activeSlide === 2"
        x-transition:enter="transition ease-out duration-800"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-800"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: linear-gradient(rgba(10, 70, 80, 0.7), rgba(0, 90, 90, 0.6)), url('/Images/banner3.jpg'); background-size: cover; background-position: center;">
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

<!-- Add JavaScript for Biology Particles -->
<script>
    function initBiologyParticles() {
        const container = document.getElementById('particle-container');
        if (!container) return null;
        
        const width = container.offsetWidth;
        const height = container.offsetHeight;
        
        // Calculate appropriate number of particles based on screen size
        const particleCount = Math.min(Math.floor(width * height / 12000), 80);
        
        const particles = [];
        const connections = [];
        
        // Clear existing particles
        container.innerHTML = '';
        
        // Particle types
        const bioTypes = ['cell', 'cell-nucleus', 'molecule', 'microorganism', 'protein'];
        
        // Create particles
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            const type = bioTypes[Math.floor(Math.random() * bioTypes.length)];
            
            // Determine size based on type
            let size;
            if (type === 'cell') {
                size = Math.random() * 60 + 40;
            } else if (type === 'cell-nucleus') {
                size = Math.random() * 20 + 10;
            } else if (type === 'molecule') {
                size = Math.random() * 25 + 15;
            } else if (type === 'microorganism') {
                size = Math.random() * 35 + 20;
            } else {
                size = Math.random() * 30 + 15;
            }
            
            particle.className = `bio-particle ${type}`;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            
            // Random position
            const x = Math.random() * width;
            const y = Math.random() * height;
            particle.style.left = `${x}px`;
            particle.style.top = `${y}px`;
            
            // Animation properties
            const moveX = (Math.random() - 0.5) * width * 0.3;
            const moveY = (Math.random() - 0.5) * height * 0.3;
            const rotation = Math.random() * 360;
            const duration = Math.random() * 30 + 20;
            
            // Randomize opacity
            const maxOpacity = Math.random() * 0.3 + 0.6;
            particle.style.setProperty('--max-opacity', maxOpacity);
            
            particle.style.setProperty('--move-x', `${moveX}px`);
            particle.style.setProperty('--move-y', `${moveY}px`);
            particle.style.setProperty('--rotation', `${rotation}deg`);
            
            // Set animations
            if (type === 'cell' && Math.random() > 0.7) {
                // Some cells will undergo mitosis animation
                particle.style.animation = `floatBioParticle ${duration}s infinite alternate ease-in-out, mitosis ${Math.random() * 10 + 10}s infinite ease-in-out`;
            } else {
                particle.style.animation = `floatBioParticle ${duration}s infinite alternate ease-in-out, pulseBioParticle ${Math.random() * 5 + 5}s infinite ease-in-out`;
            }
            
            // Add delay to stagger animations
            particle.style.animationDelay = `${Math.random() * 15}s`;
            
            container.appendChild(particle);
            particles.push({
                element: particle,
                x: x,
                y: y,
                size: size,
                type: type
            });
            
            // Add DNA segments to molecules
            if (type === 'molecule') {
                // Create DNA double helix structure
                const segments = Math.floor(Math.random() * 4) + 3;
                const segmentLength = size * 0.8;
                
                for (let j = 0; j < segments; j++) {
                    const segmentTop = document.createElement('div');
                    const segmentBottom = document.createElement('div');
                    
                    segmentTop.className = 'dna-segment';
                    segmentBottom.className = 'dna-segment';
                    
                    segmentTop.style.width = `${segmentLength}px`;
                    segmentBottom.style.width = `${segmentLength}px`;
                    
                    segmentTop.style.left = `${size/2}px`;
                    segmentTop.style.top = `${size/2 - j * (size/segments) - 2}px`;
                    segmentTop.style.transform = `rotate(${j * 25}deg)`;
                    
                    segmentBottom.style.left = `${size/2}px`;
                    segmentBottom.style.top = `${size/2 + j * (size/segments) + 2}px`;
                    segmentBottom.style.transform = `rotate(${180 + j * 25}deg)`;
                    
                    segmentTop.style.animation = `dnaRotate ${Math.random() * 8 + 12}s infinite alternate ease-in-out`;
                    segmentBottom.style.animation = `dnaRotate ${Math.random() * 8 + 12}s infinite alternate-reverse ease-in-out`;
                    
                    particle.appendChild(segmentTop);
                    particle.appendChild(segmentBottom);
                }
            }
            
            // Add nucleus to cells
            if (type === 'cell' && Math.random() > 0.3) {
                const nucleus = document.createElement('div');
                const nucleusSize = size * 0.3;
                
                nucleus.className = 'bio-particle cell-nucleus';
                nucleus.style.width = `${nucleusSize}px`;
                nucleus.style.height = `${nucleusSize}px`;
                
                // Position nucleus within cell
                const offsetX = (size - nucleusSize) * (0.4 + Math.random() * 0.2);
                const offsetY = (size - nucleusSize) * (0.4 + Math.random() * 0.2);
                
                nucleus.style.left = `${offsetX}px`;
                nucleus.style.top = `${offsetY}px`;
                nucleus.style.position = 'absolute';
                
                particle.appendChild(nucleus);
            }
        }
        
        // Create connections between some particles (molecular bonds)
        for (let i = 0; i < particleCount / 3; i++) {
            const p1 = particles[Math.floor(Math.random() * particles.length)];
            const p2 = particles[Math.floor(Math.random() * particles.length)];
            
            if (p1 !== p2 && p1.type === 'molecule' || p2.type === 'molecule') {
                const dx = p2.x - p1.x;
                const dy = p2.y - p1.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < 200) {  // Only connect if reasonably close
                    const angle = Math.atan2(dy, dx) * 180 / Math.PI;
                    
                    const connection = document.createElement('div');
                    connection.className = 'connection';
                    connection.style.width = `${distance}px`;
                    connection.style.left = `${p1.x + p1.size/2}px`;
                    connection.style.top = `${p1.y + p1.size/2}px`;
                    connection.style.transform = `rotate(${angle}deg)`;
                    
                    container.appendChild(connection);
                    connections.push(connection);
                }
            }
        }
        
        return {
            particles: particles,
            connections: connections
        };
    }
    
    // Reinitialize particles when window resizes
    window.addEventListener('resize', function() {
        setTimeout(() => {
            initBiologyParticles();
        }, 500);
    });
    
    // Initialize on page load if not handled by Alpine
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.Alpine) {
            initBiologyParticles();
        }
    });
</script>