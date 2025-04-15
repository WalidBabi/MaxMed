@extends('layouts.app')

@section('title', 'About MaxMed - Premium Medical & Laboratory Equipment Supplier in Middle East & Africa')

@section('meta_description', 'MaxMed is a leading supplier of premium diagnostic supplies, laboratory equipment, and cutting-edge medical solutions across the Middle East, Africa, and global markets. Discover our commitment to quality, innovation, and exceptional customer support.')

@section('meta_keywords', 'medical equipment supplier Middle East, laboratory equipment Africa, diagnostic supplies global, premium lab equipment, healthcare solutions Middle East, medical innovation, laboratory technology Dubai, healthcare equipment supplier, CE-certified medical equipment, international medical supplies')

@section('og_image', asset('Images/about.png'))

@section('content')
<style>
    /* Base Styles */
    :root {
        --brand-main: #171e60;
        --brand-auxiliary: #0a5694;
        --brand-white: #ffffff;
        --brand-light-bg: #f8f9fa;
        --brand-accent: rgba(10, 86, 148, 0.1);
        --text-dark: #333;
        --text-black: #000000;
        --text-muted: #666;
        --transition-standard: all 0.3s ease;
    }
    
    /* Hero Section */
    .hero-section {
        position: relative;
        min-height: 60vh;
        display: flex;
        align-items: center;
        background-size: cover;
        background-position: center;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to right, rgba(23, 30, 96, 0.9), rgba(10, 86, 148, 0.8));
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 1.5rem;
    }
    
    .hero-title {
        color: var(--brand-white);
        font-size: 3rem;
        
        margin-bottom: 1.5rem;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.2s;
    }
    
    .hero-subtitle {
        color: var(--brand-white);
        font-size: 1rem;
        max-width: 650px;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.4s;
    }
    
    /* Section Styling */
    .about-section {
        padding: 5rem 0;
        position: relative;
    }
    
    .about-section.bg-light {
        background-color: var(--brand-light-bg);
    }
    
    .section-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .section-title {
        color: var(--text-black);
        font-size: 2.5rem;
        
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--brand-main), var(--brand-auxiliary));
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        border-radius: 2px;
    }
    
    .section-subtitle {
        color: var(--text-muted);
        font-size: 0.875rem;
        max-width: 700px;
        margin: 0 auto;
    }
    
    /* Who We Are Section */
    .who-we-are {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: center;
    }
    
    .who-we-are-content h3 {
        color: var(--text-black);
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        
    }
    
    .who-we-are-content p {
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        line-height: 1.7;
        font-size: 0.875rem;
    }
    
    .highlight-box {
        background: var(--brand-accent);
        border-left: 4px solid var(--brand-auxiliary);
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
    }
    
    .highlight-box h4 {
        color: var(--text-black);
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .feature-list li {
        position: relative;
        padding-left: 1.75rem;
        margin-bottom: 0.75rem;
        color: var(--text-dark);
        font-size: 0.875rem;
    }
    
    .feature-list li::before {
        content: '→';
        position: absolute;
        left: 0;
        color: var(--text-black);
    }
    
    .image-container {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        transform: translateY(20px);
        opacity: 0;
        transition: var(--transition-standard);
    }
    
    .image-container.animated {
        transform: translateY(0);
        opacity: 1;
    }
    
    .about-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-standard);
    }
    
    .image-container:hover .about-image {
        transform: scale(1.05);
    }
    
    /* Vision & Mission Section */
    .vision-mission-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
    
    .vision-box, .mission-box {
        background: var(--brand-white);
        border-radius: 1rem;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: var(--transition-standard);
        height: 100%;
        opacity: 0;
        transform: translateY(20px);
    }
    
    .vision-box.animated, .mission-box.animated {
        opacity: 1;
        transform: translateY(0);
    }
    
    .vision-box:hover, .mission-box:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .box-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background-color: var(--brand-accent);
        margin-bottom: 1.5rem;
        color: var(--brand-auxiliary);
        transition: var(--transition-standard);
    }
    
    .vision-box:hover .box-icon, .mission-box:hover .box-icon {
        background-color: var(--brand-auxiliary);
        color: var(--brand-white);
        transform: scale(1.1);
    }
    
    .box-title {
        color: var(--text-black);
        font-size: 1.25rem;
        margin-bottom: 1rem;
        
    }
    
    .box-content {
        color: var(--text-muted);
        line-height: 1.7;
        font-size: 0.875rem;
    }
    
    .box-highlight {
        display: block;
        
        color: var(--text-black);
        margin-top: 1rem;
        font-size: 1rem;
    }
    
    /* Values Section */
    .values-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    
    .value-card {
        background: var(--brand-white);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        transition: var(--transition-standard);
        position: relative;
        overflow: hidden;
        height: 100%;
        opacity: 0;
        transform: translateY(30px);
    }
    
    .value-card.animated {
        opacity: 1;
        transform: translateY(0);
    }
    
    .value-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 0;
        background: linear-gradient(to bottom, var(--brand-main), var(--brand-auxiliary));
        transition: var(--transition-standard);
    }
    
    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
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
        background-color: var(--brand-accent);
        margin-bottom: 1.5rem;
        color: var(--brand-main);
        transition: var(--transition-standard);
    }
    
    .value-card:hover .value-icon {
        background-color: var(--brand-main);
        color: var(--brand-white);
        transform: scale(1.1);
    }
    
    .value-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--text-black);
        
    }
    
    .value-desc {
        color: var(--text-muted);
        line-height: 1.7;
        font-size: 0.875rem;
    }
    
    /* Difference Section */
    .difference-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    .difference-item {
        background: var(--brand-white);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        transition: var(--transition-standard);
        opacity: 0;
        transform: translateY(20px);
    }
    
    .difference-item.animated {
        opacity: 1;
        transform: translateY(0);
    }
    
    .difference-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .difference-icon {
        color: var(--brand-auxiliary);
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .difference-title {
        color: var(--text-black);
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        
    }
    
    .difference-desc {
        color: var(--text-muted);
        line-height: 1.7;
        font-size: 0.875rem;
    }
    
    /* CTA Section */
    .cta-section {
        background: linear-gradient(to right, var(--brand-main), var(--brand-auxiliary));
        padding: 5rem 0;
        color: var(--brand-white);
        text-align: center;
    }
    
    .cta-title {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        
    }
    
    .cta-subtitle {
        font-size: 1rem;
        max-width: 700px;
        margin: 0 auto 2.5rem;
        opacity: 0.9;
    }
    
    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    
    .btn-primary {
        background-color: var(--brand-white);
        color: var(--text-black);
        padding: 0.75rem 2rem;
        border-radius: 50px;
        
        text-decoration: none;
        transition: var(--transition-standard);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-outline {
        background-color: transparent;
        color: var(--brand-white);
        padding: 0.75rem 2rem;
        border-radius: 50px;
        
        text-decoration: none;
        border: 2px solid var(--brand-white);
        transition: var(--transition-standard);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        background-color: #f8f9fa;
    }
    
    .btn-outline:hover {
        background-color: var(--brand-white);
        color: var(--text-black);
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    /* Map Section */
    .map-section {
        padding: 0;
        position: relative;
        height: 400px;
        overflow: hidden;
    }
    
    .map-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, rgba(23, 30, 96, 0.7), transparent);
        pointer-events: none;
        z-index: 10;
    }
    
    .map-content {
        position: absolute;
        top: 50%;
        left: 5%;
        transform: translateY(-50%);
        z-index: 20;
        color: var(--brand-white);
        max-width: 400px;
        padding: 2rem;
        background: rgba(23, 30, 96, 0.8);
        border-radius: 1rem;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }
    
    .map-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--brand-white);
    }
    
    .map-address {
        margin-bottom: 1.5rem;
        line-height: 1.7;
        font-size: 0.875rem;
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
    
    /* Responsive Styles */
    @media (max-width: 992px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .who-we-are {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .vision-mission-container {
            grid-template-columns: 1fr;
        }
        
        .values-container {
            grid-template-columns: 1fr 1fr;
        }
        
        .map-content {
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 500px;
        }
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .section-title {
            font-size: 1.75rem;
        }
        
        .values-container {
            grid-template-columns: 1fr;
        }
        
        .cta-title {
            font-size: 2rem;
        }
        
        .cta-subtitle {
            font-size: 1.1rem;
        }
        
        .map-section {
            height: 500px;
        }
        
        .map-content {
            position: relative;
            top: auto;
            left: auto;
            transform: none;
            width: 90%;
            margin: 2rem auto;
            background: var(--brand-main);
        }
    }
    
    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.75rem;
        }
        
        .section-title {
            font-size: 1.5rem;
        }
        
        .cta-buttons {
            flex-direction: column;
            gap: 1rem;
        }
        
        .btn-primary, .btn-outline {
            width: 100%;
            justify-content: center;
        }
    }
    
    /* Collapsible mobile sections */
    .collapsible-header {
        display: none;
        padding: 1rem;
        background: var(--brand-accent);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        cursor: pointer;
        color: var(--text-black);
    }
    
    .collapsible-content {
        display: block;
    }
    
    @media (max-width: 768px) {
        .collapsible-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .collapsible-header::after {
            content: '+';
            font-size: 1.5rem;
        }
        
        .collapsible-header.active::after {
            content: '-';
        }
        
        .collapsible-content {
            display: none;
        }
        
        .collapsible-content.active {
            display: block;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section" style="background-image: url('{{ asset('Images/maxmed building.png') }}');">
    <div class="hero-content">
        <h1 class="hero-title">Empowering Excellence in Diagnostics and Medical Innovation</h1>
        <p class="hero-subtitle">Your trusted partner for premium diagnostic supplies, laboratory equipment, and cutting-edge medical solutions across the Middle East, Africa, and global markets.</p>
    </div>
</section>

<!-- Who We Are Section -->
<section class="about-section">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">Who We Are</h2>
            <p class="section-subtitle">Based in Dubai, MaxMed is your trusted partner for premium diagnostic RDTs, laboratory equipment, and cutting-edge medical supplies serving the Middle East, Africa, and international markets.</p>
        </div>
        
        <div class="who-we-are">
            <div class="who-we-are-content">
                <h3>Your Partner in Medical Excellence</h3>
                <p>At MaxMed Scientific, we collaborate with global leaders to deliver excellence, ensuring every product meets rigorous quality standards. Our commitment to innovation and reliability has positioned us as a leading medical and laboratory equipment supplier serving clients across the Middle East, Africa, and beyond.</p>
                
                <div class="highlight-box">
                    <h4>But we're more than suppliers—we're enablers.</h4>
                    <ul class="feature-list">
                        <li><strong>Knowledge-Driven Solutions:</strong> We equip your lab with the latest technologies and expert training.</li>
                        <li><strong>Tailored Support:</strong> Our specialized sales and technical teams provide precision-driven guidance for your evolving needs.</li>
                    </ul>
                </div>
                
                <p>From state-of-the-art diagnostic tools to essential laboratory supplies, we're committed to empowering healthcare professionals and research institutions throughout the Middle East, Africa, and international markets with the tools they need to advance medical care and scientific discovery.</p>
            </div>
            
            <div class="image-container" id="who-we-are-image">
                <img src="{{ asset('Images/about.png') }}" alt="MaxMed Laboratory Equipment" class="about-image">
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission Section -->
<section class="about-section bg-light">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">Our Vision & Mission</h2>
            <p class="section-subtitle">Guiding principles that drive our commitment to excellence in medical and laboratory solutions.</p>
        </div>
        
        <div class="vision-mission-container">
            <div class="vision-box" id="vision-box">
                <div class="box-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                    </svg>
                </div>
                <h3 class="box-title">Our Vision</h3>
                <p class="box-content">We strive to be the foremost innovator in laboratory and medical solutions, driving scientific advancement and sustainable growth across emerging markets in the Middle East, Africa, and beyond.</p>
                <span class="box-highlight">Leading the Future of Sustainable Healthcare Globally</span>
            </div>
            
            <div class="mission-box" id="mission-box">
                <div class="box-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9V5.5z"/>
                        <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1h-3zm1.038 3.018a6.093 6.093 0 0 1 .924 0 6 6 0 1 1-.924 0zM0 8.5A.5.5 0 0 1 .5 8h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zM8 9.71a.5.5 0 0 0 .5.5h7a.5.5 0 1 0 0-1h-7a.5.5 0 0 0-.5.5z"/>
                    </svg>
                </div>
                <h3 class="box-title">Our Mission</h3>
                <p class="box-content">Delivering high-performance medical and laboratory equipment with unwavering integrity, ensuring every client achieves unmatched value and satisfaction.</p>
                <span class="box-highlight">Quality. Integrity. Value.</span>
            </div>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="about-section">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">Our Core Values</h2>
            <p class="section-subtitle">The principles that guide our operations and interactions with clients, partners, and the healthcare community.</p>
        </div>
        
        <div class="values-container">
            <div class="value-card" id="value-card-1">
                <div class="value-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h4 class="value-title">Innovation</h4>
                <p class="value-desc">Continuously pushing boundaries to provide the latest technological advancements in laboratory equipment to our clients across the Middle East, Africa, and global markets.</p>
            </div>

            <div class="value-card" id="value-card-2">
                <div class="value-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h4 class="value-title">Quality</h4>
                <p class="value-desc">Ensuring the highest standards in every piece of medical and laboratory equipment we provide to our clients throughout our international service regions.</p>
            </div>

            <div class="value-card" id="value-card-3">
                <div class="value-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h4 class="value-title">Customer Support</h4>
                <p class="value-desc">Dedicated to providing exceptional service and technical support to our clients across the Middle East, Africa, and global markets at every step of their journey.</p>
            </div>
        </div>
    </div>
</section>

<!-- The MaxMed Difference Section -->
<section class="about-section bg-light">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">The MaxMed Difference</h2>
            <p class="section-subtitle">What sets us apart as a leading supplier of premium diagnostic supplies and medical equipment in Dubai.</p>
        </div>
        
        <div class="difference-list">
            <div class="difference-item" id="difference-1">
                <div class="difference-icon">✅</div>
                <h4 class="difference-title">CE-Certified Excellence</h4>
                <p class="difference-desc">We provide reliable, compliant medical laboratory solutions for hospitals, diagnostic labs, and research centers across the Middle East, Africa, and international markets.</p>
            </div>
            
            <div class="difference-item" id="difference-2">
                <div class="difference-icon">✅</div>
                <h4 class="difference-title">Rapid Global Delivery</h4>
                <p class="difference-desc">From our Dubai headquarters to your doorstep—we ensure fast, efficient, and secure delivery of medical supplies throughout the Middle East, Africa, and beyond.</p>
            </div>
            
            <div class="difference-item" id="difference-3">
                <div class="difference-icon">✅</div>
                <h4 class="difference-title">Customer-First Philosophy</h4>
                <p class="difference-desc">Your success is our metric. We listen, adapt, and exceed expectations for all our global clients with our premium medical equipment.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="section-container">
        <h2 class="cta-title">Join Our Journey</h2>
        <p class="cta-subtitle">Partner with MaxMed - a team as committed to medical innovation and excellence as you are, serving clients across the Middle East, Africa, and around the world. Explore our products or connect with our experts today.</p>
        
        <div class="cta-buttons">
            <a href="{{ route('contact') }}" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                </svg>
                Contact Us
            </a>
            <a href="{{ route('products.index') }}" class="btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                </svg>
                Browse Catalog
            </a>
        </div>
    </div>
</section>

<!-- Map/Global Reach Section -->
<section class="map-section">
    <div class="map-overlay"></div>
    <div style="position: relative; width: 100%; height: 100%;">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14440.542527223084!2d55.1724694!3d25.0874995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f6c0fdcf8c319%3A0xc1b5948ee66f3482!2sAl%20Barsha%20-%20Dubai!5e0!3m2!1sen!2sae!4v1714500000000!5m2!1sen!2sae" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        <a href="https://maps.google.com/maps?ll=25.040843,55.114858&z=15&t=m&hl=en&gl=ae&mapclient=embed&q=Ibn+Battuta+Gate+Office+Complex+Building" target="_blank" style="position: absolute; top: 20px; right: 20px; background-color: var(--brand-white); color: var(--text-black); padding: 0.5rem 1rem; border-radius: 50px;  text-decoration: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 0.5rem; z-index: 100;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 0a5 5 0 0 0-5 5c0 5 5 11 5 11s5-6 5-11a5 5 0 0 0-5-5zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
            </svg>
            View Map
        </a>
    </div>
    <div class="map-content">
        <h3 class="map-title">Our Global Reach</h3>
        <p class="map-address">Based in Dubai, we serve hospitals, laboratories, and research facilities across the Middle East, Africa, and international markets with premium diagnostic supplies and medical equipment.</p>
        <a href="{{ route('contact') }}" class="btn-primary">Visit Us</a>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate elements when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });
        
        // Observe elements that need animation
        document.querySelectorAll('.image-container, .vision-box, .mission-box, .value-card, .difference-item').forEach(el => {
            observer.observe(el);
        });
        
        // Mobile collapsible sections
        const collapsibleHeaders = document.querySelectorAll('.collapsible-header');
        collapsibleHeaders.forEach(header => {
            header.addEventListener('click', function() {
                this.classList.toggle('active');
                const content = this.nextElementSibling;
                content.classList.toggle('active');
            });
        });
        
        // Add mobile collapsible functionality
        if (window.innerWidth <= 768) {
            const sectionsToCollapse = document.querySelectorAll('.who-we-are-content, .vision-mission-container, .values-container, .difference-list');
            
            sectionsToCollapse.forEach(section => {
                const title = section.previousElementSibling ? 
                              section.previousElementSibling.querySelector('.section-title') : 
                              section.parentElement.querySelector('.section-title');
                              
                if (title) {
                    const headerText = title.textContent;
                    const collapsibleHeader = document.createElement('div');
                    collapsibleHeader.className = 'collapsible-header';
                    collapsibleHeader.textContent = headerText;
                    
                    section.classList.add('collapsible-content');
                    section.parentNode.insertBefore(collapsibleHeader, section);
                    
                    collapsibleHeader.addEventListener('click', function() {
                        this.classList.toggle('active');
                        section.classList.toggle('active');
                    });
                }
            });
        }
    });
</script>

@include('layouts.footer')
@endsection