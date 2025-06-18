<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Services\SeoService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GenerateAdvancedSeoAudit extends Command
{
    protected $signature = 'seo:audit-advanced 
                            {--fix : Automatically fix issues where possible}
                            {--export=json : Export format (json, html, csv)}
                            {--output=storage/seo-audit : Output directory}';
    protected $description = 'Generate comprehensive advanced SEO audit with Core Web Vitals and accessibility checks';

    private $seoService;
    private $auditResults = [];
    private $fixes = [];
    private $baseUrl;

    public function __construct(SeoService $seoService)
    {
        parent::__construct();
        $this->seoService = $seoService;
        $this->baseUrl = config('app.url');
    }

    public function handle()
    {
        $this->info('🚀 Starting Advanced SEO Audit for MaxMed UAE...');
        $this->newLine();

        $this->auditResults = [
            'timestamp' => now()->toISOString(),
            'domain' => $this->baseUrl,
            'summary' => [],
            'technical_seo' => [],
            'content_seo' => [],
            'mobile_seo' => [],
            'accessibility' => [],
            'performance' => [],
            'security' => [],
            'fixes_applied' => [],
            'recommendations' => [],
            'score' => 0
        ];

        // Run comprehensive audits
        $this->auditTechnicalSeo();
        $this->auditContentSeo();
        $this->auditMobileSeo();
        $this->auditAccessibility();
        $this->auditPerformance();
        $this->auditSecurity();
        $this->auditInternationalization();

        // Apply fixes if requested
        if ($this->option('fix')) {
            $this->applyFixes();
        }

        // Calculate overall score
        $this->calculateAdvancedScore();

        // Export results
        $this->exportResults();

        // Display summary
        $this->displayAdvancedSummary();

        return 0;
    }

    private function auditTechnicalSeo()
    {
        $this->info('🔧 Auditing Technical SEO...');
        
        $issues = [];
        $recommendations = [];
        
        // Check robots.txt
        $robotsPath = public_path('robots.txt');
        if (!File::exists($robotsPath)) {
            $issues[] = 'robots.txt file missing';
            $recommendations[] = 'Create robots.txt file for better crawler control';
        } else {
            $robotsContent = File::get($robotsPath);
            if (!str_contains($robotsContent, 'Sitemap:')) {
                $issues[] = 'robots.txt missing sitemap reference';
                $recommendations[] = 'Add sitemap URL to robots.txt';
            }
        }

        // Check sitemap.xml
        $sitemapPath = public_path('sitemap.xml');
        if (!File::exists($sitemapPath)) {
            $issues[] = 'sitemap.xml file missing';
            $recommendations[] = 'Generate XML sitemap for better indexing';
        }

        // Check meta tags on sample pages
        $this->checkMetaTags($issues, $recommendations);

        // Check canonical URLs
        $this->checkCanonicalUrls($issues, $recommendations);

        // Check duplicate content
        $this->checkDuplicateContent($issues, $recommendations);

        // Check URL structure
        $this->checkUrlStructure($issues, $recommendations);

        $this->auditResults['technical_seo'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 10)
        ];
    }

    private function auditContentSeo()
    {
        $this->info('📝 Auditing Content SEO...');
        
        $issues = [];
        $recommendations = [];
        
        // Check product content quality
        $productsWithoutDescription = Product::whereNull('description')
                                            ->orWhere('description', '')
                                            ->count();
        
        if ($productsWithoutDescription > 0) {
            $issues[] = "{$productsWithoutDescription} products missing descriptions";
            $recommendations[] = "Add unique descriptions to all products for better SEO";
        }

        // Check title lengths
        $this->checkTitleLengths($issues, $recommendations);

        // Check meta description lengths
        $this->checkMetaDescriptionLengths($issues, $recommendations);

        // Check heading structure
        $this->checkHeadingStructure($issues, $recommendations);

        // Check internal linking
        $this->checkInternalLinking($issues, $recommendations);

        // Check content freshness
        $this->checkContentFreshness($issues, $recommendations);

        $this->auditResults['content_seo'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 8)
        ];
    }

    private function auditMobileSeo()
    {
        $this->info('📱 Auditing Mobile SEO...');
        
        $issues = [];
        $recommendations = [];
        
        // Check viewport meta tag
        $this->checkViewportMeta($issues, $recommendations);

        // Check mobile-friendly design
        $this->checkMobileFriendlyDesign($issues, $recommendations);

        // Check touch targets
        $this->checkTouchTargets($issues, $recommendations);

        // Check mobile page speed
        $this->checkMobilePageSpeed($issues, $recommendations);

        $this->auditResults['mobile_seo'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 6)
        ];
    }

    private function auditAccessibility()
    {
        $this->info('♿ Auditing Accessibility...');
        
        $issues = [];
        $recommendations = [];
        
        // Check alt text for images
        $this->checkImageAltText($issues, $recommendations);

        // Check heading hierarchy
        $this->checkHeadingHierarchy($issues, $recommendations);

        // Check form labels
        $this->checkFormLabels($issues, $recommendations);

        // Check color contrast
        $this->checkColorContrast($issues, $recommendations);

        // Check keyboard navigation
        $this->checkKeyboardNavigation($issues, $recommendations);

        $this->auditResults['accessibility'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 7)
        ];
    }

    private function auditPerformance()
    {
        $this->info('⚡ Auditing Performance...');
        
        $issues = [];
        $recommendations = [];
        
        // Check image optimization
        $this->checkImageOptimization($issues, $recommendations);

        // Check CSS/JS minification
        $this->checkAssetMinification($issues, $recommendations);

        // Check caching headers
        $this->checkCachingHeaders($issues, $recommendations);

        // Check Core Web Vitals
        $this->checkCoreWebVitals($issues, $recommendations);

        $this->auditResults['performance'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 8)
        ];
    }

    private function auditSecurity()
    {
        $this->info('🔒 Auditing Security...');
        
        $issues = [];
        $recommendations = [];
        
        // Check HTTPS
        if (!str_starts_with($this->baseUrl, 'https://')) {
            $issues[] = 'Website not using HTTPS';
            $recommendations[] = 'Implement SSL certificate for better security and SEO';
        }

        // Check security headers
        $this->checkSecurityHeaders($issues, $recommendations);

        // Check mixed content
        $this->checkMixedContent($issues, $recommendations);

        $this->auditResults['security'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 5)
        ];
    }

    private function auditInternationalization()
    {
        $this->info('🌍 Auditing Internationalization...');
        
        $issues = [];
        $recommendations = [];
        
        // Check hreflang implementation
        $this->checkHreflangImplementation($issues, $recommendations);

        // Check language targeting
        $this->checkLanguageTargeting($issues, $recommendations);

        $this->auditResults['internationalization'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 3)
        ];
    }

    private function checkMetaTags(&$issues, &$recommendations)
    {
        // Check homepage meta
        $homeMeta = $this->seoService->generateHomeMeta();
        
        if (strlen($homeMeta['title']) > 60) {
            $issues[] = 'Homepage title too long (' . strlen($homeMeta['title']) . ' chars)';
        }
        
        if (strlen($homeMeta['meta_description']) > 160) {
            $issues[] = 'Homepage meta description too long (' . strlen($homeMeta['meta_description']) . ' chars)';
        }

        // Sample product pages
        $products = Product::limit(5)->get();
        foreach ($products as $product) {
            $meta = $this->seoService->generateProductMeta($product);
            if (strlen($meta['title']) > 60) {
                $issues[] = "Product '{$product->name}' title too long";
            }
        }
    }

    private function checkCanonicalUrls(&$issues, &$recommendations)
    {
        // Check if canonical URLs are properly implemented
        $viewPath = resource_path('views/layouts/meta.blade.php');
        if (File::exists($viewPath)) {
            $content = File::get($viewPath);
            if (!str_contains($content, 'rel="canonical"')) {
                $issues[] = 'Canonical URLs not implemented';
                $recommendations[] = 'Add canonical URL tags to prevent duplicate content issues';
            }
        }
    }

    private function checkDuplicateContent(&$issues, &$recommendations)
    {
        // Check for products with identical names
        $duplicateProducts = Product::select('name')
                                  ->groupBy('name')
                                  ->havingRaw('COUNT(*) > 1')
                                  ->count();
        
        if ($duplicateProducts > 0) {
            $issues[] = "{$duplicateProducts} products with duplicate names found";
            $recommendations[] = 'Ensure all products have unique names and descriptions';
        }
    }

    private function checkUrlStructure(&$issues, &$recommendations)
    {
        // Check URL patterns
        $products = Product::limit(10)->get();
        foreach ($products as $product) {
            $url = route('product.show', $product);
            if (!preg_match('/^[a-z0-9\-\/]+$/', parse_url($url, PHP_URL_PATH))) {
                $issues[] = "Product URL not SEO-friendly: {$url}";
                break;
            }
        }
    }

    private function checkTitleLengths(&$issues, &$recommendations)
    {
        $longTitles = 0;
        $shortTitles = 0;
        
        // Check sample of products
        Product::chunk(100, function($products) use (&$longTitles, &$shortTitles) {
            foreach ($products as $product) {
                $meta = $this->seoService->generateProductMeta($product);
                if (strlen($meta['title']) > 60) $longTitles++;
                if (strlen($meta['title']) < 30) $shortTitles++;
            }
        });
        
        if ($longTitles > 0) {
            $issues[] = "{$longTitles} pages with titles too long (>60 chars)";
        }
        if ($shortTitles > 0) {
            $issues[] = "{$shortTitles} pages with titles too short (<30 chars)";
        }
    }

    private function checkMetaDescriptionLengths(&$issues, &$recommendations)
    {
        $longDescriptions = 0;
        $shortDescriptions = 0;
        
        Product::chunk(100, function($products) use (&$longDescriptions, &$shortDescriptions) {
            foreach ($products as $product) {
                $meta = $this->seoService->generateProductMeta($product);
                if (strlen($meta['meta_description']) > 160) $longDescriptions++;
                if (strlen($meta['meta_description']) < 120) $shortDescriptions++;
            }
        });
        
        if ($longDescriptions > 0) {
            $issues[] = "{$longDescriptions} pages with meta descriptions too long (>160 chars)";
        }
        if ($shortDescriptions > 0) {
            $recommendations[] = "Consider expanding {$shortDescriptions} short meta descriptions for better CTR";
        }
    }

    private function checkHeadingStructure(&$issues, &$recommendations)
    {
        // Check if H1 tags are properly implemented in views
        $viewFiles = ['welcome.blade.php', 'products/show.blade.php', 'categories/index.blade.php'];
        
        foreach ($viewFiles as $file) {
            $path = resource_path("views/{$file}");
            if (File::exists($path)) {
                $content = File::get($path);
                if (!str_contains($content, '<h1')) {
                    $issues[] = "Missing H1 tag in {$file}";
                }
            }
        }
    }

    private function checkInternalLinking(&$issues, &$recommendations)
    {
        // Check if internal linking service is being used
        $serviceContent = File::get(app_path('Services/SeoService.php'));
        if (!str_contains($serviceContent, 'generateInternalLinks')) {
            $recommendations[] = 'Implement automated internal linking suggestions';
        }
    }

    private function checkContentFreshness(&$issues, &$recommendations)
    {
        $oldProducts = Product::where('updated_at', '<', now()->subMonths(6))->count();
        if ($oldProducts > 0) {
            $recommendations[] = "{$oldProducts} products haven't been updated in 6+ months";
        }

        $oldNews = News::where('created_at', '<', now()->subMonths(3))->count();
        if ($oldNews === News::count()) {
            $issues[] = 'No recent news articles published';
            $recommendations[] = 'Publish fresh content regularly for better SEO';
        }
    }

    private function checkViewportMeta(&$issues, &$recommendations)
    {
        $metaPath = resource_path('views/layouts/meta.blade.php');
        if (File::exists($metaPath)) {
            $content = File::get($metaPath);
            if (!str_contains($content, 'viewport')) {
                $issues[] = 'Missing viewport meta tag';
                $recommendations[] = 'Add viewport meta tag for mobile optimization';
            }
        }
    }

    private function checkMobileFriendlyDesign(&$issues, &$recommendations)
    {
        // Check if responsive design is implemented
        $cssFiles = File::glob(public_path('css/*.css'));
        $hasResponsive = false;
        
        foreach ($cssFiles as $file) {
            $content = File::get($file);
            if (str_contains($content, '@media') || str_contains($content, 'responsive')) {
                $hasResponsive = true;
                break;
            }
        }
        
        if (!$hasResponsive) {
            $issues[] = 'No responsive CSS detected';
            $recommendations[] = 'Implement responsive design for mobile devices';
        }
    }

    private function checkTouchTargets(&$issues, &$recommendations)
    {
        // This would require DOM analysis - simplified check
        $recommendations[] = 'Ensure touch targets are at least 44px for mobile usability';
    }

    private function checkMobilePageSpeed(&$issues, &$recommendations)
    {
        // Simplified check - would require actual performance testing
        $recommendations[] = 'Test mobile page speed with Google PageSpeed Insights';
    }

    private function checkImageAltText(&$issues, &$recommendations)
    {
        // Check if alt text component is being used
        $componentPath = resource_path('views/components/seo-image.blade.php');
        if (!File::exists($componentPath)) {
            $issues[] = 'SEO image component not found';
            $recommendations[] = 'Implement SEO-optimized image component with automatic alt text';
        }
    }

    private function checkHeadingHierarchy(&$issues, &$recommendations)
    {
        $recommendations[] = 'Ensure proper heading hierarchy (H1 > H2 > H3) throughout the site';
    }

    private function checkFormLabels(&$issues, &$recommendations)
    {
        // Check contact form and other forms for proper labels
        $contactPath = resource_path('views/contact.blade.php');
        if (File::exists($contactPath)) {
            $content = File::get($contactPath);
            if (!str_contains($content, '<label')) {
                $issues[] = 'Forms missing proper labels';
                $recommendations[] = 'Add proper labels to all form inputs for accessibility';
            }
        }
    }

    private function checkColorContrast(&$issues, &$recommendations)
    {
        $recommendations[] = 'Verify color contrast ratios meet WCAG 2.1 AA standards (4.5:1 for normal text)';
    }

    private function checkKeyboardNavigation(&$issues, &$recommendations)
    {
        $recommendations[] = 'Test all interactive elements are accessible via keyboard navigation';
    }

    private function checkImageOptimization(&$issues, &$recommendations)
    {
        // Check if lazy loading is implemented
        $lazyPath = resource_path('views/components/lazy-image.blade.php');
        if (!File::exists($lazyPath)) {
            $issues[] = 'Lazy loading component not found';
            $recommendations[] = 'Implement lazy loading for images to improve performance';
        }
    }

    private function checkAssetMinification(&$issues, &$recommendations)
    {
        // Check if assets are minified
        $cssFiles = File::glob(public_path('css/*.css'));
        $hasMinified = false;
        
        foreach ($cssFiles as $file) {
            if (str_contains(basename($file), '.min.')) {
                $hasMinified = true;
                break;
            }
        }
        
        if (!$hasMinified) {
            $recommendations[] = 'Minify CSS and JavaScript files for better performance';
        }
    }

    private function checkCachingHeaders(&$issues, &$recommendations)
    {
        // Check .htaccess for caching headers
        $htaccessPath = public_path('.htaccess');
        if (File::exists($htaccessPath)) {
            $content = File::get($htaccessPath);
            if (!str_contains($content, 'ExpiresByType')) {
                $issues[] = 'Browser caching headers not configured';
                $recommendations[] = 'Configure browser caching in .htaccess for better performance';
            }
        }
    }

    private function checkCoreWebVitals(&$issues, &$recommendations)
    {
        $recommendations[] = 'Monitor Core Web Vitals (LCP, FID, CLS) using Google Search Console';
        $recommendations[] = 'Optimize for LCP < 2.5s, FID < 100ms, CLS < 0.1';
    }

    private function checkSecurityHeaders(&$issues, &$recommendations)
    {
        $htaccessPath = public_path('.htaccess');
        if (File::exists($htaccessPath)) {
            $content = File::get($htaccessPath);
            
            if (!str_contains($content, 'X-Content-Type-Options')) {
                $issues[] = 'Missing X-Content-Type-Options header';
            }
            
            if (!str_contains($content, 'X-Frame-Options')) {
                $issues[] = 'Missing X-Frame-Options header';
            }
            
            if (!str_contains($content, 'X-XSS-Protection')) {
                $issues[] = 'Missing X-XSS-Protection header';
            }
        }
    }

    private function checkMixedContent(&$issues, &$recommendations)
    {
        // Check for mixed content issues
        if (str_starts_with($this->baseUrl, 'https://')) {
            $recommendations[] = 'Ensure all resources (images, CSS, JS) are loaded over HTTPS';
        }
    }

    private function checkHreflangImplementation(&$issues, &$recommendations)
    {
        $metaPath = resource_path('views/layouts/meta.blade.php');
        if (File::exists($metaPath)) {
            $content = File::get($metaPath);
            if (!str_contains($content, 'hreflang')) {
                $issues[] = 'Hreflang tags not implemented';
                $recommendations[] = 'Add hreflang tags for international SEO targeting';
            }
        }
    }

    private function checkLanguageTargeting(&$issues, &$recommendations)
    {
        // Check if proper language and region targeting is set
        $recommendations[] = 'Verify language and region targeting in Google Search Console';
    }

    private function calculateSectionScore($issues, $maxIssues)
    {
        $issueCount = count($issues);
        return max(0, (($maxIssues - $issueCount) / $maxIssues) * 100);
    }

    private function calculateAdvancedScore()
    {
        $sections = [
            'technical_seo' => 25,
            'content_seo' => 20,
            'mobile_seo' => 15,
            'accessibility' => 15,
            'performance' => 15,
            'security' => 10
        ];

        $totalScore = 0;
        foreach ($sections as $section => $weight) {
            if (isset($this->auditResults[$section]['score'])) {
                $totalScore += ($this->auditResults[$section]['score'] * $weight / 100);
            }
        }

        $this->auditResults['score'] = round($totalScore);
        
        // Determine grade
        $grade = 'F';
        if ($totalScore >= 90) $grade = 'A+';
        elseif ($totalScore >= 80) $grade = 'A';
        elseif ($totalScore >= 70) $grade = 'B';
        elseif ($totalScore >= 60) $grade = 'C';
        elseif ($totalScore >= 50) $grade = 'D';

        $this->auditResults['grade'] = $grade;
    }

    private function applyFixes()
    {
        $this->info('🔨 Applying automatic fixes...');
        
        // This would contain automated fixes for common issues
        $this->fixes[] = 'Automated fixes would be applied here';
        
        $this->auditResults['fixes_applied'] = $this->fixes;
    }

    private function exportResults()
    {
        $format = $this->option('export');
        $outputDir = $this->option('output');
        
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        
        switch ($format) {
            case 'html':
                $this->exportHtml($outputDir, $timestamp);
                break;
            case 'csv':
                $this->exportCsv($outputDir, $timestamp);
                break;
            case 'json':
            default:
                $this->exportJson($outputDir, $timestamp);
                break;
        }
    }

    private function exportJson($outputDir, $timestamp)
    {
        $filename = "advanced-seo-audit_{$timestamp}.json";
        $path = $outputDir . '/' . $filename;
        
        File::put($path, json_encode($this->auditResults, JSON_PRETTY_PRINT));
        $this->info("📊 Advanced audit results exported: {$path}");
    }

    private function exportHtml($outputDir, $timestamp)
    {
        // Would generate HTML report
        $this->info("📊 HTML export would be implemented here");
    }

    private function exportCsv($outputDir, $timestamp)
    {
        // Would generate CSV report
        $this->info("📊 CSV export would be implemented here");
    }

    private function displayAdvancedSummary()
    {
        $this->newLine();
        $this->info('🎯 Advanced SEO Audit Summary');
        $this->info('================================');
        $this->info("Overall Score: {$this->auditResults['score']}/100 (Grade: {$this->auditResults['grade']})");
        $this->newLine();

        foreach (['technical_seo', 'content_seo', 'mobile_seo', 'accessibility', 'performance', 'security'] as $section) {
            if (isset($this->auditResults[$section])) {
                $score = round($this->auditResults[$section]['score']);
                $issues = count($this->auditResults[$section]['issues']);
                $recommendations = count($this->auditResults[$section]['recommendations']);
                
                $this->info("📊 " . ucwords(str_replace('_', ' ', $section)) . ": {$score}% ({$issues} issues, {$recommendations} recommendations)");
            }
        }

        $this->newLine();
        $this->info('✅ Advanced SEO audit completed!');
    }
} 