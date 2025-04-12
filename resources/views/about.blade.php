@extends('layouts.app')

@section('title', 'About MaxMed UAE - Leading Laboratory Equipment Supplier in Dubai')

@section('content')
<style>
    /* Enhanced Hero Section */
    .about-hero {
        position: relative;
        height: 450px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-size: cover;
        background-position: center;
        color: white;
        overflow: hidden;
    }
    
    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(rgba(23, 30, 96, 0.7), rgba(23, 30, 96, 0.8));
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
        max-width: 800px;
        padding: 0 1rem;
    }
    
    .hero-title {
        font-size: 3rem;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.2s;
    }
    
    .hero-subtitle {
        font-size: 1rem;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.4s;
    }
    
    /* Our Story Section */
    .story-section {
        padding: 5rem 0;
    }
    
    .story-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }
    
    .section-title {
        font-size: 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        display: inline-block;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        width: 70%;
        height: 4px;
        background: linear-gradient(to right, #171e60, #0a5694);
        bottom: -12px;
        left: 0;
        border-radius: 2px;
    }
    
    .section-text {
        color: #555;
        font-size: 0.875rem;
        line-height: 1.8;
        margin-bottom: 1.5rem;
        opacity: 0;
        transform: translateY(20px);
    }
    
    .section-text.animated {
        animation: fadeUp 0.8s ease forwards;
    }
    
    .story-image-container {
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transform: translateX(30px);
    }
    
    .story-image-container.animated {
        animation: fadeLeft 1s ease forwards;
    }
    
    .story-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        transition: all 0.5s ease;
    }
    
    .story-image-container:hover .story-image {
        transform: scale(1.05);
    }
    
    /* Values Section */
    .values-section {
        background-color: #f8f9fa;
        padding: 5rem 0;
    }
    
    .values-title {
        font-size: 2rem;
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
        color: #333;
    }
    
    .values-title::after {
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
    
    .value-card {
        background-color: white;
        padding: 2.5rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        height: 100%;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        opacity: 0;
        transform: translateY(30px);
    }
    
    .value-card.animated {
        animation: fadeUp 0.8s ease forwards;
    }
    
    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .value-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 0;
        background: linear-gradient(to bottom, #171e60, #0a5694);
        transition: height 0.4s ease;
    }
    
    .value-card:hover::before {
        height: 100%;
    }
    
    .value-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: rgba(23, 30, 96, 0.1);
        color: #171e60;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .value-card:hover .value-icon {
        background-color: #171e60;
        color: white;
        transform: scale(1.1);
    }
    
    .value-title {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: #333;
    }
    
    .value-desc {
        color: #666;
        font-size: 0.875rem;
        line-height: 1.7;
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
    
    @keyframes fadeLeft {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Responsive Styles */
    @media (max-width: 992px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .about-hero {
            height: 400px;
        }
        
        .section-title {
            font-size: 1.25rem;
        }
        
        .story-image {
            height: 350px;
        }
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .about-hero {
            height: 350px;
        }
        
        .story-content {
            margin-bottom: 2rem;
        }
        
        .value-card {
            margin-bottom: 1.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.75rem;
        }
        
        .hero-subtitle {
            font-size: 1rem;
        }
        
        .about-hero {
            height: 300px;
        }
        
        .section-title {
            font-size: 1.25rem;
        }
    }
</style>

<!-- Hero Section -->
<div class="about-hero" style="background-image: url('{{ asset('Images/maxmed building.png') }}');">
    <div class="hero-content">
        <h1 class="hero-title">About MaxMed</h1>
        <p class="hero-subtitle">Leading the Future of Medical Laboratory Equipment</p>
    </div>
</div>

<!-- Our Story Section -->
<div class="story-section">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="story-content">
                <h3 class="section-title">Our Story</h3>
                <p class="section-text" id="story-text-1">
                    Founded in 2024, MaxMed has been at the forefront of medical laboratory innovation in the UAE.
                    What started as a small distribution company has grown into one of the region's leading providers
                    of cutting-edge laboratory equipment.
                </p>
                <p class="section-text" id="story-text-2">
                    Our commitment to quality, innovation, and customer service has helped us build lasting
                    relationships with healthcare facilities across the Middle East.
                </p>
            </div>
            <div class="story-image-container" id="story-image">
                <img src="{{ asset('Images/banner2.jpeg') }}"
                    alt="Laboratory Equipment"
                    class="story-image">
            </div>
        </div>
    </div>
</div>

<!-- Values Section -->
<div class="values-section">
    <div class="max-w-7xl mx-auto px-4">
        <h3 class="values-title">Our Core Values</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="value-card" id="value-card-1">
                <div class="value-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h4 class="value-title">Innovation</h4>
                <p class="value-desc">Continuously pushing boundaries to provide the latest technological advancements in laboratory equipment.</p>
            </div>

            <div class="value-card" id="value-card-2">
                <div class="value-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h4 class="value-title">Quality</h4>
                <p class="value-desc">Ensuring the highest standards in every piece of equipment we provide to our clients.</p>
            </div>

            <div class="value-card" id="value-card-3">
                <div class="value-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h4 class="value-title">Customer Support</h4>
                <p class="value-desc">Dedicated to providing exceptional service and support to our clients at every step.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate the story section elements when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.id === 'story-text-1') {
                        entry.target.classList.add('animated');
                        setTimeout(() => {
                            document.getElementById('story-text-2').classList.add('animated');
                        }, 200);
                    } else if (entry.target.id === 'story-image') {
                        entry.target.classList.add('animated');
                    } else if (entry.target.id === 'value-card-1') {
                        entry.target.classList.add('animated');
                        setTimeout(() => {
                            document.getElementById('value-card-2').classList.add('animated');
                        }, 150);
                        setTimeout(() => {
                            document.getElementById('value-card-3').classList.add('animated');
                        }, 300);
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });
        
        // Observe the story text, image, and value cards
        observer.observe(document.getElementById('story-text-1'));
        observer.observe(document.getElementById('story-image'));
        observer.observe(document.getElementById('value-card-1'));
    });
</script>

@include('layouts.footer')
@endsection