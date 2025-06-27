@extends('layouts.app')

@section('title', 'Fume Hood Suppliers UAE - Laboratory Safety Equipment | MaxMed Dubai')
@section('meta_description', '🔬 #1 Fume Hood Suppliers in UAE! Chemical, biological & radioisotope fume hoods ✅ CE certified ⚡ Installation included ☎️ +971 55 460 2500')
@section('meta_keywords', 'fume hood suppliers UAE, laboratory fume hoods Dubai, chemical fume hood, biological safety cabinet, lab ventilation UAE, ducted fume hood, ductless fume hood')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Hero Section -->
            <div class="bg-gradient-primary text-white rounded-lg p-5 mb-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-3">🔬 Fume Hood Suppliers UAE</h1>
                        <p class="lead mb-4">Leading supplier of laboratory fume hoods and safety equipment in UAE. Chemical, biological, and radioisotope fume hoods with CE certification and professional installation.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#contact-form" class="btn btn-light btn-lg">
                                <i class="fas fa-phone me-2"></i>Get Quote Now
                            </a>
                            <a href="#fume-hood-types" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-eye me-2"></i>View Products
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="position-relative">
                            <img src="{{ asset('Images/fume-hood-hero.jpg') }}" 
                                 alt="Laboratory Fume Hoods UAE" 
                                 class="img-fluid rounded shadow-lg"
                                 style="max-height: 300px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Why Choose MaxMed for Fume Hoods -->
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="text-center mb-4">Why Choose MaxMed for Fume Hoods in UAE?</h2>
                    <div class="row g-4">
                        <div class="col-md-3 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-certificate fa-3x text-primary"></i>
                            </div>
                            <h5>CE Certified</h5>
                            <p class="text-muted">All fume hoods meet European safety standards</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-tools fa-3x text-success"></i>
                            </div>
                            <h5>Installation Included</h5>
                            <p class="text-muted">Professional installation and commissioning</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-shield-alt fa-3x text-info"></i>
                            </div>
                            <h5>Safety Compliance</h5>
                            <p class="text-muted">SEFA and ASHRAE compliant designs</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-headset fa-3x text-warning"></i>
                            </div>
                            <h5>24/7 Support</h5>
                            <p class="text-muted">Maintenance and technical support</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fume Hood Types -->
            <div id="fume-hood-types" class="mb-5">
                <h2 class="text-center mb-4">Types of Fume Hoods Available</h2>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-wind fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Ducted Fume Hoods</h5>
                                <ul class="list-unstyled">
                                    <li>✅ Chemical resistance</li>
                                    <li>✅ High airflow capacity</li>
                                    <li>✅ External exhaust</li>
                                    <li>✅ Energy efficient</li>
                                </ul>
                                <a href="#contact-form" class="btn btn-primary">Get Quote</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-filter fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Ductless Fume Hoods</h5>
                                <ul class="list-unstyled">
                                    <li>✅ Carbon filtration</li>
                                    <li>✅ No ductwork required</li>
                                    <li>✅ Portable options</li>
                                    <li>✅ Cost effective</li>
                                </ul>
                                <a href="#contact-form" class="btn btn-success">Get Quote</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-radiation fa-3x text-danger mb-3"></i>
                                <h5 class="card-title">Radioisotope Fume Hoods</h5>
                                <ul class="list-unstyled">
                                    <li>✅ Lead-lined construction</li>
                                    <li>✅ HEPA filtration</li>
                                    <li>✅ Radiation monitoring</li>
                                    <li>✅ Safety interlocks</li>
                                </ul>
                                <a href="#contact-form" class="btn btn-danger">Get Quote</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features & Benefits -->
            <div class="bg-light rounded-lg p-5 mb-5">
                <h2 class="text-center mb-4">Fume Hood Features & Benefits</h2>
                <div class="row">
                    <div class="col-lg-6">
                        <h5><i class="fas fa-check-circle text-success me-2"></i>Safety Features</h5>
                        <ul>
                            <li>Low airflow alarms</li>
                            <li>Sash position indicators</li>
                            <li>Emergency shut-off controls</li>
                            <li>Face velocity monitoring</li>
                            <li>VAV (Variable Air Volume) controls</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h5><i class="fas fa-cogs text-primary me-2"></i>Construction Features</h5>
                        <ul>
                            <li>Chemical-resistant work surfaces</li>
                            <li>Epoxy resin or stainless steel construction</li>
                            <li>Tempered safety glass sash</li>
                            <li>LED lighting systems</li>
                            <li>Electrical outlets with GFCI protection</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Applications -->
            <div class="mb-5">
                <h2 class="text-center mb-4">Laboratory Applications</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="fas fa-flask text-primary me-3 fa-2x"></i>
                            <div>
                                <h6 class="mb-1">Chemical Analysis</h6>
                                <small class="text-muted">Acid digestion, extractions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="fas fa-microscope text-success me-3 fa-2x"></i>
                            <div>
                                <h6 class="mb-1">Research Labs</h6>
                                <small class="text-muted">University, pharmaceutical</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="fas fa-hospital text-info me-3 fa-2x"></i>
                            <div>
                                <h6 class="mb-1">Clinical Labs</h6>
                                <small class="text-muted">Hospital, diagnostic centers</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div id="contact-form" class="bg-primary text-white rounded-lg p-5">
                <div class="row">
                    <div class="col-lg-6">
                        <h3 class="mb-4">Get Your Fume Hood Quote Today!</h3>
                        <div class="mb-3">
                            <i class="fas fa-phone me-2"></i>
                            <strong>Call: +971 55 460 2500</strong>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-envelope me-2"></i>
                            <strong>Email: sales@maxmedme.com</strong>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Response Time: Same Day</strong>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-truck me-2"></i>
                            <strong>Delivery: UAE Wide</strong>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form class="bg-white text-dark p-4 rounded">
                            <h5 class="mb-3">Quick Quote Form</h5>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Email Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" class="form-control" placeholder="Phone Number" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" required>
                                    <option value="">Select Fume Hood Type</option>
                                    <option value="ducted">Ducted Fume Hood</option>
                                    <option value="ductless">Ductless Fume Hood</option>
                                    <option value="radioisotope">Radioisotope Fume Hood</option>
                                    <option value="biological">Biological Safety Cabinet</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="3" placeholder="Specific Requirements"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Get Quote Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-5">
                <h2 class="text-center mb-4">Frequently Asked Questions</h2>
                <div class="accordion" id="fumeHoodFAQ">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What types of fume hoods do you supply in UAE?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#fumeHoodFAQ">
                            <div class="accordion-body">
                                We supply ducted fume hoods, ductless fume hoods, radioisotope fume hoods, and biological safety cabinets. All units are CE certified and meet international safety standards.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Do you provide installation services?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#fumeHoodFAQ">
                            <div class="accordion-body">
                                Yes, we provide complete installation, commissioning, and certification services across UAE. Our certified technicians ensure proper setup and safety compliance.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                What maintenance services do you offer?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#fumeHoodFAQ">
                            <div class="accordion-body">
                                We offer annual maintenance contracts, filter replacements, airflow testing, and emergency repair services. Our support team is available 24/7.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('structured-data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "Laboratory Fume Hoods",
    "description": "Professional laboratory fume hoods and safety equipment in UAE. Chemical, biological, and radioisotope fume hoods with CE certification.",
    "brand": {
        "@type": "Brand",
        "name": "MaxMed UAE"
    },
    "offers": {
        "@type": "Offer",
        "availability": "https://schema.org/InStock",
        "priceCurrency": "AED",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "telephone": "+971 55 460 2500",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "AE",
                "addressRegion": "Dubai"
            }
        }
    },
    "category": "Laboratory Safety Equipment",
    "keywords": "fume hood suppliers UAE, laboratory fume hoods Dubai, chemical fume hood, biological safety cabinet"
}
</script>
@endpush

@endsection 