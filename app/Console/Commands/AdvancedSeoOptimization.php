<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;

class AdvancedSeoOptimization extends Command
{
    protected $signature = 'seo:optimize-advanced 
                            {--dry-run : Show what would be optimized without making changes}
                            {--type=all : Type of optimization (schema, performance, content, all)}';
    protected $description = 'Advanced SEO optimizations for MaxMed UAE';

    public function handle()
    {
        $this->info('🚀 Starting Advanced SEO Optimization for MaxMed UAE...');
        
        $isDryRun = $this->option('dry-run');
        $type = $this->option('type');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $optimizations = 0;

        if ($type === 'all' || $type === 'schema') {
            $optimizations += $this->optimizeStructuredData($isDryRun);
        }

        if ($type === 'all' || $type === 'performance') {
            $optimizations += $this->optimizePerformance($isDryRun);
        }

        if ($type === 'all' || $type === 'content') {
            $optimizations += $this->optimizeContent($isDryRun);
        }

        if ($type === 'all' || $type === 'technical') {
            $optimizations += $this->optimizeTechnicalSeo($isDryRun);
        }

        $this->info("✅ Advanced SEO optimization completed! {$optimizations} improvements " . ($isDryRun ? 'would be' : 'were') . " applied.");
        
        if (!$isDryRun && $optimizations > 0) {
            $this->info('🚀 Recommended next steps:');
            $this->line('   1. php artisan sitemap:ultimate --validate');
            $this->line('   2. Test page speed with Google PageSpeed Insights');
            $this->line('   3. Submit updated sitemap to Google Search Console');
        }

        return 0;
    }

    private function optimizeStructuredData($isDryRun)
    {
        $this->info('📊 Optimizing Structured Data (Schema.org)...');
        
        $optimizations = 0;

        // Create Organization Schema
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'MaxMed Scientific & Laboratory Equipment Trading Co L.L.C',
            'alternateName' => 'MaxMed UAE',
            'url' => 'https://maxmedme.com',
            'logo' => 'https://maxmedme.com/Images/logo.png',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+971-55-460-2500',
                'contactType' => 'sales',
                'areaServed' => 'AE',
                'availableLanguage' => 'English'
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'AE',
                'addressRegion' => 'Dubai',
                'addressLocality' => 'Dubai'
            ],
            'sameAs' => [
                'https://www.linkedin.com/company/maxmed-uae',
                'https://twitter.com/maxmeduae'
            ]
        ];

        if (!$isDryRun) {
            $this->createSchemaComponent('organization-schema', $organizationSchema);
        }
        $this->line('   ✓ Organization schema created');
        $optimizations++;

        // Create Product Schema Template
        $productSchemaTemplate = $this->createProductSchemaTemplate();
        if (!$isDryRun) {
            $this->createSchemaComponent('product-schema', $productSchemaTemplate);
        }
        $this->line('   ✓ Product schema template created');
        $optimizations++;

        // Create Local Business Schema
        $localBusinessSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'MaxMed UAE',
            'image' => 'https://maxmedme.com/Images/about.png',
            'telephone' => '+971-55-460-2500',
            'email' => 'sales@maxmedme.com',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'AE',
                'addressRegion' => 'Dubai',
                'addressLocality' => 'Dubai'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 25.2048,
                'longitude' => 55.2708
            ],
            'openingHoursSpecification' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '09:00',
                'closes' => '18:00'
            ]
        ];

        if (!$isDryRun) {
            $this->createSchemaComponent('local-business-schema', $localBusinessSchema);
        }
        $this->line('   ✓ Local business schema created');
        $optimizations++;

        return $optimizations;
    }

    private function optimizePerformance($isDryRun)
    {
        $this->info('⚡ Optimizing Performance...');
        
        $optimizations = 0;

        // Create optimized robots.txt
        $robotsTxt = $this->generateOptimizedRobotsTxt();
        if (!$isDryRun) {
            File::put(public_path('robots.txt'), $robotsTxt);
        }
        $this->line('   ✓ Optimized robots.txt updated');
        $optimizations++;

        // Create .htaccess optimizations
        $htaccessRules = $this->generateHtaccessOptimizations();
        if (!$isDryRun) {
            $existingHtaccess = File::exists(public_path('.htaccess')) ? File::get(public_path('.htaccess')) : '';
            if (!str_contains($existingHtaccess, '# MaxMed SEO Optimizations')) {
                File::append(public_path('.htaccess'), "\n\n" . $htaccessRules);
            }
        }
        $this->line('   ✓ Performance .htaccess rules added');
        $optimizations++;

        return $optimizations;
    }

    private function optimizeContent($isDryRun)
    {
        $this->info('📝 Optimizing Content...');
        
        $optimizations = 0;

        // Create category descriptions for better SEO
        $categories = Category::whereNull('description')->orWhere('description', '')->get();
        
        foreach ($categories as $category) {
            $description = $this->generateCategoryDescription($category->name);
            
            if (!$isDryRun) {
                $category->update(['description' => $description]);
            }
            
            $this->line("   ✓ Added description for category: {$category->name}");
            $optimizations++;
        }

        // Optimize existing content - Skip product meta_description updates since column doesn't exist
        $this->line("   ⚡ Skipping product meta_description updates (column not available)");
        
        // Instead, focus on category descriptions which are working
        $this->line("   ✓ Category descriptions optimization completed successfully");

        return $optimizations;
    }

    private function optimizeTechnicalSeo($isDryRun)
    {
        $this->info('🔧 Optimizing Technical SEO...');
        
        $optimizations = 0;

        // Create security headers component
        $securityHeaders = $this->createSecurityHeadersComponent();
        if (!$isDryRun) {
            File::put(resource_path('views/components/security-headers.blade.php'), $securityHeaders);
        }
        $this->line('   ✓ Security headers component created');
        $optimizations++;

        // Create canonical URL component
        $canonicalComponent = $this->createCanonicalUrlComponent();
        if (!$isDryRun) {
            File::put(resource_path('views/components/canonical-url.blade.php'), $canonicalComponent);
        }
        $this->line('   ✓ Canonical URL component created');
        $optimizations++;

        return $optimizations;
    }

    private function createSchemaComponent($name, $schema)
    {
        $component = '<script type="application/ld+json">' . "\n";
        $component .= json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        $component .= '</script>';

        File::put(resource_path("views/components/{$name}.blade.php"), $component);
    }

    private function createProductSchemaTemplate()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => '{{ $product->name }}',
            'description' => '{{ $product->description }}',
            'image' => '{{ $product->image_url ?? asset("/Images/placeholder.jpg") }}',
            'brand' => [
                '@type' => 'Brand',
                'name' => '{{ $product->brand->name ?? "MaxMed UAE" }}'
            ],
            'manufacturer' => [
                '@type' => 'Organization',
                'name' => 'MaxMed UAE'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => '{{ route("product.show", $product) }}',
                'priceCurrency' => 'AED',
                'availability' => 'https://schema.org/InStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'MaxMed UAE'
                ]
            ]
        ];
    }

    private function generateOptimizedRobotsTxt()
    {
        return "User-agent: *
Allow: /
Disallow: /admin/
Disallow: /crm/
Disallow: /supplier/
Disallow: /login
Disallow: /register
Disallow: /password/
Disallow: /cart/
Disallow: /checkout/
Disallow: /search?
Disallow: /*?sort=
Disallow: /*?filter=
Disallow: /*?page=
Allow: /products/
Allow: /categories/
Allow: /news/
Allow: /about
Allow: /contact

# Sitemaps
Sitemap: https://maxmedme.com/sitemap.xml
Sitemap: https://maxmedme.com/rss/feed.xml

# Crawl delay for respectful crawling
Crawl-delay: 1

# Block AI training bots (optional)
User-agent: ChatGPT-User
Disallow: /

User-agent: CCBot
Disallow: /

User-agent: anthropic-ai
Disallow: /

User-agent: Claude-Web
Disallow: /";
    }

    private function generateHtaccessOptimizations()
    {
        return "# MaxMed SEO Optimizations

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType image/jpg \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 month\"
    ExpiresByType image/gif \"access plus 1 month\"
    ExpiresByType image/png \"access plus 1 month\"
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/pdf \"access plus 1 month\"
    ExpiresByType text/javascript \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType application/x-javascript \"access plus 1 month\"
    ExpiresByType application/x-shockwave-flash \"access plus 1 month\"
    ExpiresByType image/x-icon \"access plus 1 year\"
    ExpiresDefault \"access plus 2 days\"
</IfModule>";
    }

    private function generateCategoryDescription($categoryName)
    {
        $descriptions = [
            'Molecular & Clinical Diagnostics' => 'Advanced molecular and clinical diagnostic equipment from MaxMed UAE. Professional laboratory solutions for accurate medical testing and research applications in the Middle East.',
            'Life Science & Research' => 'Comprehensive life science and research equipment for laboratories, universities, and research institutions. High-quality instruments for biological and chemical analysis.',
            'Lab Equipment' => 'Professional laboratory equipment and instruments from leading manufacturers. Complete solutions for analytical chemistry, materials testing, and quality control applications.',
            'Medical Consumables' => 'High-quality medical consumables and supplies for healthcare facilities. Sterile products, diagnostic kits, and medical devices for clinical applications.',
            'Lab Consumables' => 'Laboratory consumables, glassware, and supplies for research and analytical work. Professional-grade products for accurate and reliable laboratory operations.'
        ];

        return $descriptions[$categoryName] ?? "Professional {$categoryName} equipment and supplies from MaxMed UAE. High-quality laboratory solutions for research, analysis, and diagnostic applications.";
    }

    private function generateProductMetaDescription($product)
    {
        $description = $product->name . ' from MaxMed UAE. ';
        
        if ($product->description) {
            $description .= substr(strip_tags($product->description), 0, 100) . '... ';
        }
        
        $description .= 'Professional laboratory equipment with reliable performance. Contact +971 55 460 2500.';
        
        return substr($description, 0, 160);
    }

    private function createSecurityHeadersComponent()
    {
        return '{{-- Security Headers Component --}}
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<meta http-equiv="X-Frame-Options" content="DENY">
<meta http-equiv="X-XSS-Protection" content="1; mode=block">
<meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
<meta http-equiv="Permissions-Policy" content="geolocation=(), microphone=(), camera=()">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">';
    }

    private function createCanonicalUrlComponent()
    {
        return '@props([\'url\' => request()->url()])

<link rel="canonical" href="{{ $url }}">';
    }
} 