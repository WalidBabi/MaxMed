@props(['title' => '', 'description' => ''])

{{-- Mobile-optimized meta tags --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="MaxMed UAE">
<meta name="theme-color" content="#171e60">

{{-- Mobile-specific structured data --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "MobileApplication",
    "name": "MaxMed UAE",
    "operatingSystem": "All",
    "applicationCategory": "BusinessApplication",
    "description": "Laboratory equipment supplier in UAE"
}
</script>