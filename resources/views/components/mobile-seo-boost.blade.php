@props([
    'entity' => null,
    'type' => 'product'
])

@php
    $entityName = $entity && isset($entity->name) ? $entity->name : 'Laboratory Equipment';
    
    // Mobile-optimized meta descriptions (shorter, more direct)
    $mobileMetas = [
        "🔬 {$entityName} Dubai! ✅ Fast quotes 📞 +971-55-460-2500 ⚡ Same day response",
        "🏆 MaxMed UAE: {$entityName} ✅ Quality + Speed 📞 Call now +971-55-460-2500",
        "⭐ {$entityName} UAE supplier ✅ Pro service 📞 +971-55-460-2500 🚚 Fast delivery"
    ];
    
    $selectedMeta = $mobileMetas[array_rand($mobileMetas)];
    $mobileDescription = substr($selectedMeta, 0, 120); // Shorter for mobile
@endphp

{{-- Mobile-Specific Meta Optimization --}}
@push('meta')
<meta name="mobile-optimized" content="true">
<meta name="mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-status-bar-style" content="#171e60">
<meta name="apple-mobile-web-app-title" content="MaxMed UAE">
<meta name="theme-color" content="#171e60">

{{-- Mobile viewport optimization --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">

{{-- Mobile-specific structured data --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "MobileApplication",
    "name": "MaxMed UAE Mobile",
    "operatingSystem": "All",
    "applicationCategory": "BusinessApplication",
    "description": "Laboratory equipment supplier mobile experience",
    "offers": {
        "@type": "Offer",
        "category": "Laboratory Equipment",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "telephone": "+971554602500"
        }
    }
}
</script>

{{-- Mobile click-to-call optimization --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "MaxMed UAE",
    "telephone": "+971-55-460-2500",
    "priceRange": "$$",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "AE",
        "addressRegion": "Dubai"
    },
    "potentialAction": {
        "@type": "CallAction",
        "target": "tel:+971554602500"
    }
}
</script>
@endpush

{{-- Mobile-specific CSS for better UX --}}
@push('styles')
<style>
@media (max-width: 768px) {
    /* Mobile CTA optimization */
    .mobile-cta {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(45deg, #171e60, #0a5694);
        color: white;
        padding: 15px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(23, 30, 96, 0.3);
        z-index: 1000;
        transition: all 0.3s ease;
        border: none;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .mobile-cta:hover {
        transform: translateX(-50%) translateY(-2px);
        box-shadow: 0 6px 20px rgba(23, 30, 96, 0.4);
        color: white;
        text-decoration: none;
    }
    
    /* Mobile-optimized touch targets */
    .mobile-touch-target {
        min-height: 44px;
        min-width: 44px;
        padding: 12px;
    }
    
    /* Mobile-friendly product cards */
    .mobile-product-card {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Mobile navigation optimization */
    .mobile-nav-optimized {
        position: sticky;
        top: 0;
        z-index: 999;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
    }
}
</style>
@endpush

{{-- Mobile CTA Button --}}
<div class="d-md-none">
    <a href="tel:+971554602500" class="mobile-cta">
        📞 Call MaxMed Now
    </a>
</div>

{{-- Mobile-optimized structured data for local search --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $entityName }}",
    "description": "{{ $mobileDescription }}",
    "offers": {
        "@type": "Offer",
        "priceCurrency": "AED",
        "availability": "https://schema.org/InStock",
        "seller": {
            "@type": "LocalBusiness",
            "name": "MaxMed UAE",
            "telephone": "+971-55-460-2500",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "AE",
                "addressRegion": "Dubai"
            }
        }
    },
    "potentialAction": [
        {
            "@type": "CallAction",
            "target": "tel:+971554602500"
        },
        {
            "@type": "ViewAction",
            "target": "{{ url()->current() }}"
        }
    ]
}
</script> 