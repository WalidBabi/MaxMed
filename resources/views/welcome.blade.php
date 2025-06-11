@extends('layouts.app')

@section('title', 'MaxMed - Premium Medical & Laboratory Equipment Supplier in Middle East & Africa')

@section('meta_description', 'MaxMed provides high-quality medical equipment, laboratory supplies, and diagnostic tools across the Middle East, Africa, and global markets. Explore our premium products and innovative solutions.')

@section('meta_keywords', 'medical equipment supplier, laboratory equipment Middle East, diagnostic supplies Africa, premium lab equipment, healthcare solutions international, medical technology, laboratory analysis tools, scientific instruments, hospital supplies Middle East, diagnostic equipment Africa')

@push('head')
    <meta name="google-signin-client_id" content="{{ config('services.google.client_id') }}">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
@endpush

@section('content')
<!-- Google One Tap Sign In -->
@guest
    <div id="g_id_onload"
        data-client_id="{{ config('services.google.client_id') }}"
        data-callback="handleCredentialResponse"
        data-auto_prompt="true"
        data-cancel_on_tap_outside="false"
        data-context="signin"
        data-ux_mode="popup"
        data-itp_support="true">
    </div>
    <div class="fixed top-4 right-4 z-50">
        <div class="bg-white rounded-lg shadow-md p-2">
            <div class="g_id_signin"
                data-type="icon"
                data-shape="pill"
                data-theme="outline"
                data-text="signin_with"
                data-size="medium"
                data-logo_alignment="left">
            </div>
        </div>
    </div>
@endguest
<style>
    /* Google One Tap Container Styles */
    .g_id_signin {
        border: none !important;
        background: transparent !important;
    }
    
    .g_id_signin > div {
        margin: 0 !important;
        width: auto !important;
        height: auto !important;
    }

    /* Enhanced Hero Section */
    .hero-section {
        position: relative;
        height: 300px;
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
        font-size: 2.5rem;

        margin-bottom: 1.5rem;
        color: white;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.2s;
    }

    .hero-subtitle {
        font-size: 1.2rem;
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
        padding: 0.75rem 1.75rem;
        border-radius: 50px;

        font-size: 0.875rem;
        ;
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
        bottom: -1px;
        left: 0;
        right: 0;
        height: 70px;
        z-index: 30;
        overflow: hidden;
    }

    /* Features Section */
    .features-section {
        background-color: #f8f9fa;

    }

    .section-title {
        font-size: 2rem;
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
        font-size: 1.6rem;
        margin-bottom: 1rem;
    }

    .feature-desc {
        font-size: 1.1rem;
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
            font-size: 2.2rem;
        }

        .hero-section {
            height: 450px;
        }

        .feature-content {
            padding: 2rem;
        }

        .feature-title {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .hero-section {
            height: 400px;
        }

        .supplier-slide {
            grid-template-columns: repeat(2, 1fr);
        }

        .feature-slide {
            height: 600px;
            /* Increase height for mobile to accommodate stacked content */
        }

        .feature-slide .flex {
            flex-direction: column;
        }

        .feature-slide .md\:w-1\/2 {
            width: 100%;
        }

        .feature-img {
            height: 200px;
        }

        .category-container {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .category-card .card-title {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.75rem;
        }

        .hero-subtitle {
            font-size: 0.95rem;
        }

        .hero-section {
            height: 350px;
        }

        .nav-arrow {
            width: 35px;
            height: 35px;
        }

        .section-title {
            font-size: 1.75rem;
        }

        .feature-title {
            font-size: 1.3rem;
        }

        .feature-content {
            padding: 1.5rem;
        }

        .feature-desc {
            font-size: 0.95rem;
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

        0%,
        100% {
            transform: scale(1) rotate(var(--rotation));
        }

        50% {
            transform: scale(1.1) rotate(var(--rotation));
        }
    }

    @keyframes mitosis {

        0%,
        100% {
            clip-path: circle(50% at 50% 50%);
        }

        50% {
            clip-path: inset(0 50% 0 0);
        }
    }

    @keyframes dnaRotate {
        0% {
            transform: rotate(0deg) translateY(2px);
        }

        100% {
            transform: rotate(360deg) translateY(-2px);
        }
    }

    /* Category Card Styling */
    .category-container {
        margin-top: 15px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 28px;
    }

    .category-card-wrapper {
        height: 100%;
        transition: transform 0.3s;
        opacity: 0;
        transform: translateY(20px);
    }

    .category-card {
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

    .category-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
    }

    .category-card img {
        height: 180px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .category-card:hover img {
        transform: scale(1.08);
    }

    .category-card .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 22px;
        position: relative;
    }

    .category-card .card-title {
        text-align: center;
        font-size: 0.875rem;

        color: #222;
        margin-bottom: 0;
        position: relative;
        transition: color 0.3s;
    }

    .category-card:hover .card-title {
        color: #17a2b8;
    }

    .category-card .card-title:after {
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

    .category-card:hover .card-title:after {
        width: 50%;
    }

    /* Lab theme badge for categories */
    .lab-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(23, 162, 184, 0.9);
        color: white;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 500;
        z-index: 10;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        transition: all 0.3s;
    }

    .category-card:hover .lab-badge {
        background: rgba(40, 167, 69, 0.9);
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
    }

    /* Responsive category styles */
    @media (max-width: 992px) {
        .category-container {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .category-container {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .category-card .card-title {
            font-size: 0.875rem;
            ;
        }

        .feature-slide .w-1\/2 {
            width: 100%;
        }

        .feature-img {
            height: 200px;
        }
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.6rem;
        }

        .category-container {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
        }

        .category-card img {
            height: 140px;
        }

        .category-card .card-body {
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
        style="background: linear-gradient(rgba(0, 60, 80,0), rgba(0, 80, 100, 0.6)), url('/Images/banner.png'); background-size: cover; background-position: center;">
    </div>
    <div class="hero-slide"
        x-show="activeSlide === 1"
        x-transition:enter="transition ease-out duration-800"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-800"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: linear-gradient(rgba(20, 80, 90,0), rgba(0, 100, 110, 0.6)), url('/Images/banner2.jpeg'); background-size: cover; background-position: center;">
    </div>
    <div class="hero-slide"
        x-show="activeSlide === 2"
        x-transition:enter="transition ease-out duration-800"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-800"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: linear-gradient(rgba(10, 70, 80,0), rgba(0, 90, 90, 0.6)), url('/Images/banner3.jpg'); background-size: cover; background-position: center;">
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
        class="nav-arrow left" aria-label="Previous slide">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="activeSlide = activeSlide === 2 ? 0 : activeSlide + 1"
        class="nav-arrow right" aria-label="Next slide">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Brand Border -->
    <!-- <div class="brand-border">
        <svg class="w-full h-10 relative" preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="white"></path>
            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="white"></path>
            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="white"></path>
        </svg>
    </div> -->
</div>

<!-- Categories Section -->
<div class="py-9 max-w-7xl mx-auto px-4">


    <div class="category-container" x-data x-init="
        setTimeout(() => {
            document.querySelectorAll('.category-card-wrapper').forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 50));
            });
        }, 300);
    ">
        @foreach($categories->where('parent_id', null)->sortBy(function($category) {
        // Define your preferred order here - replace these with your actual category names
        $order = [
        'Molecular & Clinical Diagnostics' => 1,
        'Life Science & Research' => 2,
        'Lab Equipment' => 3,
        'Medical Consumables' => 4,
        'Lab Consumables' => 5
        // Add more as needed
        ];
        return $order[$category->name] ?? 999; // Categories not in list will appear last
        }) as $category)
        <div class="category-card-wrapper" style="opacity: 0; transform: translateY(20px); transition: opacity 0.4s ease, transform 0.5s ease;">
            <a href="{{ route('categories.show', $category) }}" class="text-decoration-none">
                <div class="category-card">
                    <div class="overflow-hidden">
                        <img src="{{ $category->image_url }}" class="card-img-top" alt="Category image for {{ $category->name }}">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $category->name }}</h3>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Start of Selection -->
<!-- Start of Selection -->
<!-- FEATURED PROMOTIONS -->
<div class="py-10 max-w-7xl mx-auto px-4">
    <h3 class="section-title">Featured Products</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
        @forelse($featuredProducts as $product)
        <div class="bg-white  overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
            x-data="{ showDetails: false }"
            @mouseenter="showDetails = true"
            @mouseleave="showDetails = false">

            <div class="relative h-80 w-full overflow-hidden">
                <!-- Product image -->
                <img src="{{ $product->image_url ?? asset('/Images/placeholder.jpg') }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">

                <!-- "New" badge if added in the last 14 days -->
                @if($product->created_at >= \Carbon\Carbon::now()->subDays(14))
                <div class="absolute top-3 left-3 bg-green-800 text-white text-xs font-bold px-3 py-1.5 rounded-full transform -rotate-3 shadow-md">
                    NEW
                </div>
                @endif
                <!-- End of Selection -->

                <!-- Overlay with details on hover -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#171e60] to-transparent opacity-0 transition-opacity duration-300"
                    :class="{ 'opacity-90': showDetails }">
                    <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 transition-transform duration-300"
                        :class="{ 'translate-y-0': showDetails }">
                        <p class="text-sm font-normal">{{ Str::limit($product->description ?? '', 100) }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <h4 class="small font-medium text-gray-900 mb-1">{{ $product->name }}</h4>

                <div class="flex items-end justify-between mt-2">
                    <!-- <div>
                           
                                <span class="text-[#171e60] font-bold">{{ number_format($product->price, 2) }} AED</span>
                    
                        </div> -->

                    <a href="{{ route('product.show', $product) }}"
                        class="inline-flex items-center justify-center bg-[#0a5694] hover:bg-[#171e60] text-white text-sm font-medium py-2 px-3 rounded-md transition-colors duration-300">
                        View Details
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center py-10">
            <div class="text-gray-500 mb-4">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h4 class="text-lg font-medium text-gray-800">No featured products available</h4>
            <p class="text-gray-600 mt-2">Check back soon for new promotions and featured items!</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block bg-[#171e60] text-white px-6 py-2 rounded-md hover:bg-[#0a5694] transition-colors">
                Browse All Products
            </a>
        </div>
        @endforelse
    </div>
</div>
<!-- Featured Brands Section -->
@if($featuredBrands->count() > 0)
<div class="py-10 max-w-7xl mx-auto px-4">
    <h3 class="section-title">Featured Brands</h3>
    
    @if($featuredBrands->count() > 4)
    <div x-data="{ activeBrandSlide: 0 }"
         x-init="setInterval(() => { activeBrandSlide = activeBrandSlide === Math.ceil({{ $featuredBrands->count() }} / 4) - 1 ? 0 : activeBrandSlide + 1 }, 4000)"
         class="relative mt-8 overflow-hidden">
        
        <div class="relative h-[180px]">
            @php
                $brandsChunks = $featuredBrands->chunk(4);
            @endphp
            
            @foreach($brandsChunks as $index => $brandChunk)
            <div class="absolute w-full transition-all duration-500"
                x-show="activeBrandSlide === {{ $index }}"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-full">
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($brandChunk as $brand)
                    <div class="bg-white  overflow-hidden shadow-md p-4 flex flex-col items-center justify-center h-full">
                        <div class="flex items-center justify-center h-28">
                            @if($brand->logo_url)
                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" 
                                 class="max-h-20 max-w-full object-contain transition-transform duration-300 hover:scale-110">
                            @else
                            <div class="text-lg font-semibold text-center text-gray-800">{{ $brand->name }}</div>
                            @endif
                        </div>
                        <div class="mt-3 text-center">
                            <h4 class="text-sm font-medium text-gray-800">{{ $brand->name }}</h4>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Navigation dots -->
        @if($brandsChunks->count() > 1)
        <div class="flex justify-center space-x-2 mt-4">
            @foreach($brandsChunks as $index => $chunk)
            <button @click="activeBrandSlide = {{ $index }}"
                    class="h-2 rounded-full transition-all duration-300"
                    :class="activeBrandSlide === {{ $index }} ? 'w-8 bg-[#171e60]' : 'w-2 bg-gray-300'"></button>
            @endforeach
        </div>
        @endif
    </div>
    @else
    <!-- Simple display for 4 or fewer brands -->
    <div class="mt-8">
        <div class="grid grid-cols-2 md:grid-cols-{{ $featuredBrands->count() > 1 ? ($featuredBrands->count() > 2 ? '4' : '2') : '1' }} gap-6">
            @foreach($featuredBrands as $brand)
            <div class="bg-white  overflow-hidden shadow-md p-4 flex flex-col items-center justify-center">
                <div class="flex items-center justify-center h-28">
                    @if($brand->logo_url)
                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" 
                         class="max-h-20 max-w-full object-contain transition-transform duration-300 hover:scale-110">
                    @else
                    <div class="text-lg font-semibold text-center text-gray-800">{{ $brand->name }}</div>
                    @endif
                </div>
                <div class="mt-3 text-center">
                    <h4 class="text-sm font-medium text-gray-800">{{ $brand->name }}</h4>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
<!-- why choose maxmed Section -->
<div class="features-section py-8 md:py-10 w-full">
    <div class="w-full mx-auto px-4" x-data="{ activeSlide: 0, autoplay: null }" x-init="autoplay = setInterval(() => { activeSlide = activeSlide === 2 ? 0 : activeSlide + 1 }, 5000)">
        <h3 class="section-title text-center">Why Choose MaxMed?</h3>

        <!-- Carousel container -->
        <div class="feature-slide relative overflow-hidden shadow-lg w-full">
            <!-- Slide 1 -->
            <div class="absolute inset-0 w-full h-full transition-opacity duration-700"
                x-show="activeSlide === 0"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="flex flex-col md:flex-row h-full">
                    <div class="md:w-1/2 bg-gradient-to-r from-[#171e60] to-[#0a5694] p-6">
                        <div class="feature-content">
                            <h4 class="feature-title text-xl font-medium text-white">Innovation First</h4>
                            <p class="feature-desc text-white">Leading the industry with cutting-edge medical laboratory solutions that define the future of diagnostics.</p>
                        </div>
                    </div>
                    <div class="md:w-1/2 overflow-hidden">
                        <img src="{{ asset('/Images/Innovation.jpg') }}"
                            alt="Innovation"
                            class="feature-img w-full h-auto shadow-md">
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="absolute inset-0 w-full h-full transition-opacity duration-700"
                x-show="activeSlide === 1"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="flex flex-col md:flex-row h-full">
                    <div class="md:w-1/2 overflow-hidden">
                        <img src="{{ asset('/Images/bacteria.jpg') }}"
                            alt="Quality"
                            class="feature-img w-full h-auto shadow-md">
                    </div>
                    <div class="md:w-1/2 bg-gradient-to-l from-[#0a5694] to-[#171e60] p-6">
                        <div class="feature-content">
                            <h4 class="feature-title text-xl font-medium text-white">Quality Assured</h4>
                            <p class="feature-desc text-white">Every piece of equipment undergoes rigorous testing to ensure reliable performance and accurate results.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="absolute inset-0 w-full h-full transition-opacity duration-700"
                x-show="activeSlide === 2"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="flex flex-col md:flex-row h-full">
                    <div class="md:w-1/2 bg-gradient-to-r from-[#171e60] to-[#0a5694] p-6">
                        <div class="feature-content">
                            <h4 class="feature-title text-xl font-medium text-white">Expert Support</h4>
                            <p class="feature-desc text-white">Our team of specialists provides comprehensive training and ongoing technical support.</p>
                        </div>
                    </div>
                    <div class="md:w-1/2 overflow-hidden">
                        <img src="{{ asset('/Images/Expert.jpg') }}"
                            alt="Support"
                            class="feature-img w-full h-auto shadow-md">
                    </div>
                </div>
            </div>

            <!-- Navigation dots -->
            <div class="absolute bottom-5 left-0 right-0 feature-nav-dots z-10 flex justify-center space-x-3">
                <button @click="activeSlide = 0"
                    class="h-3 rounded-full transition-all duration-300 transform hover:scale-110"
                    :class="activeSlide === 0 ? 'w-8 bg-[#171e60]' : 'w-3 bg-white/70 hover:bg-[#171e60]/70'"
                    aria-label="Go to slide 1">
                    <span class="sr-only">Slide 1</span>
                </button>
                <button @click="activeSlide = 1"
                    class="h-3 rounded-full transition-all duration-300 transform hover:scale-110"
                    :class="activeSlide === 1 ? 'w-8 bg-[#171e60]' : 'w-3 bg-white/70 hover:bg-[#171e60]/70'"
                    aria-label="Go to slide 2">
                    <span class="sr-only">Slide 2</span>
                </button>
                <button @click="activeSlide = 2"
                    class="h-3 rounded-full transition-all duration-300 transform hover:scale-110"
                    :class="activeSlide === 2 ? 'w-8 bg-[#171e60]' : 'w-3 bg-white/70 hover:bg-[#171e60]/70'"
                    aria-label="Go to slide 3">
                    <span class="sr-only">Slide 3</span>
                </button>
            </div>


        </div>
    </div>
</div>
<!-- Key Suppliers Section -->
<!-- <div class=" max-w-7xl mx-auto px-4">
    <h3 class="section-title">Key Suppliers</h3>
    <div x-data="{ activeSupplier: 0 }"
        x-init="setInterval(() => { activeSupplier = activeSupplier === 1 ? 0 : activeSupplier + 1 }, 5000)"
        class="relative overflow-hidden">

     
        <div class="relative h-[100px]">
        
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
    </div>
</div> -->



{{-- Footer is included in app.blade.php --}}
@endsection

<!-- Add JavaScript for Biology Particles -->
<script>
    // Handle Google One Tap response
    function handleCredentialResponse(response) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Show loading state
        const signInButton = document.querySelector('.g_id_signin');
        const originalHtml = signInButton ? signInButton.innerHTML : '';
        if (signInButton) {
            signInButton.innerHTML = '<div style="padding: 10px 0;">Signing in...</div>';
        }

        fetch("{{ route('google.one-tap') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                credential: response.credential,
                g_csrf_token: '{{ csrf_token() }}'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.error) {
                throw new Error(data.error);
            }
        })
        .catch(error => {
            console.error('Authentication error:', error);
            alert('Failed to sign in with Google. Please try again.');
            // Reset the button
            if (signInButton) {
                signInButton.innerHTML = originalHtml;
            }
            // Re-initialize the Google One Tap prompt
            google.accounts.id.prompt(notification => {
                if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                    console.log('One Tap prompt was not displayed');
                }
            });
        });
    }

    // Initialize Google One Tap
    window.onload = function() {
        try {
            const clientId = "{{ config('services.google.client_id') }}";
            if (!clientId) {
                console.error('Google Client ID is not configured');
                return;
            }

            google.accounts.id.initialize({
                client_id: clientId,
                callback: handleCredentialResponse,
                auto_select: true,
                cancel_on_tap_outside: false,
                context: 'signin',
                ux_mode: 'popup',
                itp_support: true
            });
            
            // Show the One Tap prompt after a short delay to ensure everything is loaded
            setTimeout(() => {
                google.accounts.id.prompt(notification => {
                    if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                        console.log('One Tap prompt was not displayed');
                    }
                });
            }, 1000);
        } catch (error) {
            console.error('Error initializing Google One Tap:', error);
        }
    };

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

                if (distance < 200) { // Only connect if reasonably close
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