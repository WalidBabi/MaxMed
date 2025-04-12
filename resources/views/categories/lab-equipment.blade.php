@extends('layouts.app')

@section('title', 'Laboratory Equipment & Scientific Instruments | MaxMed UAE Dubai')

@section('meta_description', 'Premium laboratory equipment, analytical instruments, and scientific research tools from MaxMed UAE. High-quality lab technology for medical and research facilities in Dubai, Abu Dhabi, and across UAE.')

@section('meta_keywords', 'laboratory equipment Dubai, analytical instruments UAE, clinical laboratory equipment, research lab supplies, lab glassware, scientific equipment Dubai, laboratory consumables UAE')

@section('content')
<div class="container my-5">
    <div class="row sidebar-content-container justify-content-center">
        <div class="col-md-3 sidebar-column transition-all duration-300">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9 main-content-column transition-all duration-300">
            <div class="page-header mb-4">
                <h1 class="page-title">Laboratory Equipment & Scientific Instruments</h1>
                <div class="breadcrumbs mb-3">
                    <a href="{{ route('home') }}">Home</a> &gt;
                    <a href="{{ route('categories.index') }}">Categories</a> &gt;
                    <span>Laboratory Equipment</span>
                </div>
                
                <div class="category-description mb-4">
                    <p>Explore MaxMed UAE's comprehensive collection of premium laboratory equipment, designed to meet the exacting standards of medical and research facilities throughout Dubai and the UAE. Our laboratory equipment range includes:</p>
                    
                    <ul class="mt-3 lab-equipment-list">
                        <li><strong>Analytical Instruments</strong> - Precision tools for accurate analysis and measurement</li>
                        <li><strong>Clinical Laboratory Equipment</strong> - Essential tools for medical diagnostics</li>
                        <li><strong>Research Laboratory Equipment</strong> - Advanced instruments for scientific research</li>
                        <li><strong>Laboratory Glassware</strong> - High-quality glassware for various lab applications</li>
                        <li><strong>Laboratory Consumables</strong> - Essential supplies for daily lab operations</li>
                    </ul>
                </div>
            </div>
            
            <!-- Products listing -->
            <div class="products-container">
                <!-- Display lab equipment products here -->
            </div>
            
            <!-- FAQ Section with schema.org markup -->
            <div class="faq-section mt-5">
                <h2>Laboratory Equipment FAQs</h2>
                
                <div class="accordion" id="labEquipmentFAQ" itemscope itemtype="https://schema.org/FAQPage">
                    <div class="accordion-item" itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" itemprop="name">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What types of laboratory equipment does MaxMed UAE offer?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                            <div class="accordion-body" itemprop="text">
                                MaxMed UAE offers a comprehensive range of laboratory equipment including analytical instruments, clinical diagnostic tools, research equipment, laboratory glassware, and consumables. Our products are sourced from leading manufacturers and meet international quality standards.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add more FAQ items with schema markup -->
                </div>
            </div>
            
            <!-- Additional content section with relevant keywords -->
            <div class="additional-info mt-5">
                <h2>Laboratory Equipment in UAE</h2>
                <p>MaxMed UAE is a trusted supplier of laboratory equipment in Dubai, Abu Dhabi, and across the United Arab Emirates. We provide high-quality scientific instruments and lab supplies for hospitals, research centers, and educational institutions. Our extensive range of products includes centrifuges, microscopes, incubators, analytical balances, and much more.</p>
                
                <h3>Why Choose MaxMed for Laboratory Equipment?</h3>
                <ul>
                    <li>Comprehensive product range from leading manufacturers</li>
                    <li>Expert technical support and installation services</li>
                    <li>Competitive pricing with maintenance contracts available</li>
                    <li>Fast delivery throughout UAE and GCC countries</li>
                    <li>After-sales service and calibration support</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle sidebar toggle event to adjust content
        document.body.addEventListener('sidebar-toggle', function(e) {
            // Additional adjustments for smoother transitions
            const contentContainer = document.querySelector('.main-content-column');
            if (contentContainer) {
                setTimeout(() => {
                    // Force re-layout for smoother transition
                    contentContainer.style.display = 'none';
                    setTimeout(() => {
                        contentContainer.style.display = 'block';
                    }, 10);
                }, 300);
            }
        });
    });
</script>
@endsection 