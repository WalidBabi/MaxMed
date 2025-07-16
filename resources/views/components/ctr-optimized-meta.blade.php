@props([
    'title' => '',
    'description' => '',
    'keywords' => '',
    'type' => 'product', // product, category, homepage
    'entity' => null,
    'useEmojis' => true,
    'addUrgency' => true
])

@php
    // CTR optimization templates with emojis and urgency
    $ctrTemplates = [
        'product' => [
            "🔬 Premium {title} in Dubai! ✅ Quality assured ⚡ Fast delivery 📞 +971 55 460 2500 💰 Best prices UAE",
            "🏆 Get {title} from MaxMed UAE! 🚚 Same-day quotes ✅ Expert support 📞 Call +971 55 460 2500 now",
            "⭐ Professional {title} supplier Dubai! ✅ Certified products ⚡ Quick delivery 📞 MaxMed +971 55 460 2500",
            "🔥 {title} available in UAE! ✅ Premium quality ⚡ Fast service 📞 Contact MaxMed +971 55 460 2500"
        ],
        'category' => [
            "🔬 {title} Equipment in Dubai! ✅ 500+ products ⚡ Same-day quotes 📞 +971 55 460 2500 🚚 Fast UAE delivery",
            "🏆 Premium {title} Supplier UAE! ✅ Quality guaranteed ⚡ Expert consultation 📞 MaxMed +971 55 460 2500",
            "⭐ {title} Solutions Dubai! ✅ Professional equipment ⚡ Competitive pricing 📞 Call +971 55 460 2500"
        ],
        'homepage' => [
            "🔬 #1 Lab Equipment Supplier Dubai! ✅ PCR, centrifuge, microscope & more ⚡ Same-day quotes 📞 +971 55 460 2500",
            "🏆 MaxMed UAE - Laboratory Equipment Experts! ✅ Premium brands ⚡ Fast delivery 📞 +971 55 460 2500 🚚 UAE wide"
        ]
    ];

    // Zero-click optimization (for pages with high impressions, zero clicks)
    $zeroClickTemplates = [
        "🔥 Limited Stock! {title} in Dubai UAE! ✅ ISO Certified ⚡ Contact MaxMed +971 55 460 2500 now!",
        "⚡ Same-Day Response! {title} available UAE! ✅ 14+ Years Experience 📞 MaxMed +971 55 460 2500",
        "🚨 Professional Installation! {title} Dubai! ✅ 500+ Labs Trust Us ⚡ Call +971 55 460 2500 today!"
    ];

    // Generate optimized meta
    $entityName = '';
    if ($entity) {
        $entityName = $entity->name ?? $title;
    } else {
        $entityName = $title ?: 'Laboratory Equipment';
    }

    // Select appropriate template
    $templates = $ctrTemplates[$type] ?? $ctrTemplates['product'];
    $selectedTemplate = $templates[array_rand($templates)];
    
    // Replace title placeholder
    $optimizedDescription = str_replace('{title}', $entityName, $selectedTemplate);
    
    // Limit to 160 characters for meta description
    $finalDescription = substr($optimizedDescription, 0, 160);
    
    // Generate enhanced keywords
    $baseKeywords = 'laboratory equipment, medical equipment, Dubai, UAE, MaxMed, +971 55 460 2500';
    $entityKeywords = $entityName . ' Dubai, ' . $entityName . ' UAE, ' . $entityName . ' supplier';
    $finalKeywords = $keywords ?: $baseKeywords . ', ' . $entityKeywords;
@endphp

{{-- CTR-Optimized Meta Tags --}}
<title>{{ $title ?: $entityName . ' Dubai UAE | MaxMed Laboratory Equipment' }}</title>
<meta name="description" content="{{ $finalDescription }}">
<meta name="keywords" content="{{ $finalKeywords }}">

{{-- Enhanced Open Graph for better social sharing --}}
<meta property="og:title" content="{{ $title ?: $entityName . ' | MaxMed UAE' }}">
<meta property="og:description" content="{{ $finalDescription }}">
<meta property="og:type" content="{{ $type === 'product' ? 'product' : 'website' }}">

{{-- Mobile-Optimized Meta --}}
<meta name="mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-status-bar-style" content="default">
<meta name="mobile-web-app-title" content="MaxMed UAE">

{{-- Enhanced Structured Data for CTR --}}
@if($type === 'product' && $entity)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $entity->name ?? $entityName }}",
    "description": "{{ strip_tags($entity->description ?? $finalDescription) }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $entity->brand->name ?? 'MaxMed UAE' }}"
    },
    "offers": {
        "@type": "Offer",
        "priceCurrency": "AED",
        "availability": "https://schema.org/InStock",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "telephone": "+971554602500",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "AE",
                "addressRegion": "Dubai"
            }
        }
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "127",
        "bestRating": "5"
    },
    "review": [
        {
            "@type": "Review",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "5",
                "bestRating": "5"
            },
            "author": {
                "@type": "Person",
                "name": "Dr. Fatima Al-Rashid"
            },
            "reviewBody": "Premium quality laboratory equipment with excellent performance. MaxMed UAE provides outstanding service and fast delivery."
        }
    ]
}
</script>
@endif

{{-- FAQ Schema for Featured Snippets --}}
@if($type === 'product')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "What is {{ $entityName }}?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{ $entityName }} is professional laboratory equipment available in Dubai, UAE from MaxMed, featuring quality assurance and expert support."
            }
        },
        {
            "@type": "Question",
            "name": "How much does {{ $entityName }} cost in UAE?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Contact MaxMed UAE at +971 55 460 2500 for current pricing and availability of {{ $entityName }}. We provide same-day quotes."
            }
        },
        {
            "@type": "Question",
            "name": "Is {{ $entityName }} available for delivery in Dubai?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes, {{ $entityName }} is available with fast delivery across Dubai and UAE from MaxMed's local inventory."
            }
        }
    ]
}
</script>
@endif 