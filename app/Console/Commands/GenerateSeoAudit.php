<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Services\SeoService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class GenerateSeoAudit extends Command
{
    protected $signature = 'seo:audit {--output=seo-audit.json} {--format=json}';
    protected $description = 'Generate comprehensive SEO audit report for MaxMed website';

    private $seoService;
    private $auditResults = [];

    public function __construct(SeoService $seoService)
    {
        parent::__construct();
        $this->seoService = $seoService;
    }

    public function handle()
    {
        $this->info('🔍 Starting SEO Audit for MaxMed UAE...');
        
        $this->auditResults = [
            'generated_at' => now()->toISOString(),
            'domain' => config('app.url'),
            'summary' => [],
            'issues' => [],
            'recommendations' => [],
            'pages_analyzed' => 0,
            'score' => 0
        ];

        // Run audit checks
        $this->auditMetaTags();
        $this->auditSchemaMarkup();
        $this->auditImages();
        $this->auditInternalLinks();
        $this->auditTechnicalSeo();
        $this->auditContent();
        $this->auditPerformance();
        
        // Calculate overall score
        $this->calculateSeoScore();
        
        // Generate report
        $this->generateReport();
        
        // Display summary
        $this->displaySummary();
        
        return 0;
    }

    private function auditMetaTags()
    {
        $this->info('📋 Auditing Meta Tags...');
        
        $issues = [];
        $recommendations = [];
        
        // Check homepage meta
        $homeMeta = $this->seoService->generateHomeMeta();
        if (strlen($homeMeta['title']) > 60) {
            $issues[] = "Homepage title too long: " . strlen($homeMeta['title']) . " characters";
        }
        if (strlen($homeMeta['meta_description']) > 160) {
            $issues[] = "Homepage description too long: " . strlen($homeMeta['meta_description']) . " characters";
        }

        // Check product pages
        $products = Product::limit(10)->get();
        $productIssues = 0;
        
        foreach ($products as $product) {
            $meta = $this->seoService->generateProductMeta($product);
            if (strlen($meta['title']) > 60) $productIssues++;
            if (strlen($meta['meta_description']) > 160) $productIssues++;
        }
        
        if ($productIssues > 0) {
            $issues[] = "Found {$productIssues} product pages with meta tag issues";
            $recommendations[] = "Review and optimize product meta tags for better SEO";
        }

        // Check category pages
        $categories = Category::limit(10)->get();
        $categoryIssues = 0;
        
        foreach ($categories as $category) {
            $meta = $this->seoService->generateCategoryMeta($category);
            if (strlen($meta['title']) > 60) $categoryIssues++;
            if (strlen($meta['meta_description']) > 160) $categoryIssues++;
        }
        
        if ($categoryIssues > 0) {
            $issues[] = "Found {$categoryIssues} category pages with meta tag issues";
            $recommendations[] = "Optimize category meta descriptions and titles";
        }

        $this->auditResults['meta_tags'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 5)
        ];
    }

    private function auditSchemaMarkup()
    {
        $this->info('🏷️ Auditing Schema Markup...');
        
        $issues = [];
        $recommendations = [];
        
        // Check if schema markup files exist
        $schemaFiles = [
            'resources/views/components/schema-markup.blade.php',
            'resources/views/layouts/schema.blade.php'
        ];
        
        foreach ($schemaFiles as $file) {
            if (!File::exists(base_path($file))) {
                $issues[] = "Schema markup file missing: {$file}";
            }
        }
        
        // Check for common schema types
        $products = Product::limit(5)->get();
        $schemaIssues = 0;
        
        foreach ($products as $product) {
            // Check if product has required fields for schema
            if (!$product->price) $schemaIssues++;
            if (!$product->description) $schemaIssues++;
            if (!$product->image_url) $schemaIssues++;
        }
        
        if ($schemaIssues > 0) {
            $issues[] = "Found {$schemaIssues} products missing required schema data";
            $recommendations[] = "Ensure all products have price, description, and image for rich snippets";
        }

        $this->auditResults['schema_markup'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 3)
        ];
    }

    private function auditImages()
    {
        $this->info('🖼️ Auditing Images...');
        
        $issues = [];
        $recommendations = [];
        
        // Check products for missing alt tags or images
        $products = Product::limit(20)->get();
        $missingImages = 0;
        $missingAlt = 0;
        
        foreach ($products as $product) {
            if (!$product->image_url || $product->image_url === 'default.jpg') {
                $missingImages++;
            }
        }
        
        if ($missingImages > 0) {
            $issues[] = "Found {$missingImages} products without proper images";
            $recommendations[] = "Add high-quality images to all products for better SEO";
        }

        // Check for WebP/AVIF format usage
        $imageFormats = ['webp', 'avif'];
        $modernFormats = false;
        
        foreach ($imageFormats as $format) {
            if (File::exists(public_path("images/optimized/*.{$format}"))) {
                $modernFormats = true;
                break;
            }
        }
        
        if (!$modernFormats) {
            $recommendations[] = "Consider using modern image formats (WebP/AVIF) for better performance";
        }

        $this->auditResults['images'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 2)
        ];
    }

    private function auditInternalLinks()
    {
        $this->info('🔗 Auditing Internal Links...');
        
        $issues = [];
        $recommendations = [];
        
        // Check for orphaned pages (pages with no internal links)
        $products = Product::limit(10)->get();
        $orphanedProducts = 0;
        
        foreach ($products as $product) {
            // Simulate checking for internal links
            // In a real implementation, you'd check the database or crawl the site
            $relatedProducts = $this->seoService->getRelatedProducts($product, 3);
            if (count($relatedProducts) < 2) {
                $orphanedProducts++;
            }
        }
        
        if ($orphanedProducts > 0) {
            $issues[] = "Found {$orphanedProducts} products with few internal links";
            $recommendations[] = "Implement related products and cross-linking strategies";
        }

        $this->auditResults['internal_links'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 1)
        ];
    }

    private function auditTechnicalSeo()
    {
        $this->info('⚙️ Auditing Technical SEO...');
        
        $issues = [];
        $recommendations = [];
        
        // Check for robots.txt
        if (!File::exists(public_path('robots.txt'))) {
            $issues[] = "robots.txt file is missing";
            $recommendations[] = "Create a robots.txt file to guide search engine crawlers";
        }
        
        // Check for sitemap
        if (!File::exists(public_path('sitemap.xml'))) {
            $issues[] = "sitemap.xml file is missing";
            $recommendations[] = "Generate and maintain XML sitemaps for better indexing";
        }
        
        // Check for SSL certificate (if URL uses HTTPS)
        if (str_starts_with(config('app.url'), 'https://')) {
            // In production, you might want to actually check SSL certificate validity
            $recommendations[] = "Ensure SSL certificate is valid and properly configured";
        } else {
            $issues[] = "Website is not using HTTPS";
            $recommendations[] = "Implement SSL certificate and force HTTPS redirects";
        }

        $this->auditResults['technical_seo'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 3)
        ];
    }

    private function auditContent()
    {
        $this->info('📝 Auditing Content...');
        
        $issues = [];
        $recommendations = [];
        
        // Check for duplicate content
        $products = Product::select('name', 'description')->get();
        $duplicateNames = $products->groupBy('name')->filter(function ($group) {
            return $group->count() > 1;
        });
        
        if ($duplicateNames->count() > 0) {
            $issues[] = "Found {$duplicateNames->count()} products with duplicate names";
            $recommendations[] = "Ensure all product names are unique and descriptive";
        }
        
        // Check for thin content
        $thinContent = $products->filter(function ($product) {
            return strlen(strip_tags($product->description ?? '')) < 100;
        });
        
        if ($thinContent->count() > 0) {
            $issues[] = "Found {$thinContent->count()} products with thin content (< 100 chars)";
            $recommendations[] = "Expand product descriptions to at least 150-200 characters";
        }

        $this->auditResults['content'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 2)
        ];
    }

    private function auditPerformance()
    {
        $this->info('⚡ Auditing Performance...');
        
        $issues = [];
        $recommendations = [];
        
        // Check for large images
        $imageDirectory = public_path('Images');
        if (File::exists($imageDirectory)) {
            $largeImages = [];
            $files = File::allFiles($imageDirectory);
            
            foreach ($files as $file) {
                if ($file->getSize() > 500000) { // 500KB
                    $largeImages[] = $file->getFilename();
                }
            }
            
            if (count($largeImages) > 0) {
                $issues[] = "Found " . count($largeImages) . " images larger than 500KB";
                $recommendations[] = "Optimize large images and consider using WebP format";
            }
        }
        
        // Check for CSS/JS optimization
        $cssFiles = File::glob(public_path('css/*.css'));
        $unminifiedCss = 0;
        
        foreach ($cssFiles as $file) {
            if (!str_contains($file, '.min.')) {
                $unminifiedCss++;
            }
        }
        
        if ($unminifiedCss > 0) {
            $recommendations[] = "Consider minifying CSS files for better performance";
        }

        $this->auditResults['performance'] = [
            'issues' => $issues,
            'recommendations' => $recommendations,
            'score' => $this->calculateSectionScore($issues, 1)
        ];
    }

    private function calculateSectionScore($issues, $maxIssues)
    {
        $issueCount = count($issues);
        return max(0, 100 - (($issueCount / $maxIssues) * 100));
    }

    private function calculateSeoScore()
    {
        $sections = [
            'meta_tags' => 25,
            'schema_markup' => 20,
            'images' => 15,
            'internal_links' => 10,
            'technical_seo' => 20,
            'content' => 10
        ];
        
        $totalScore = 0;
        foreach ($sections as $section => $weight) {
            if (isset($this->auditResults[$section])) {
                $totalScore += ($this->auditResults[$section]['score'] * $weight / 100);
            }
        }
        
        $this->auditResults['score'] = round($totalScore);
        
        // Collect all issues and recommendations
        foreach ($this->auditResults as $section => $data) {
            if (is_array($data) && isset($data['issues'])) {
                $this->auditResults['issues'] = array_merge(
                    $this->auditResults['issues'], 
                    $data['issues']
                );
                $this->auditResults['recommendations'] = array_merge(
                    $this->auditResults['recommendations'], 
                    $data['recommendations']
                );
            }
        }
    }

    private function generateReport()
    {
        $outputFile = $this->option('output');
        $format = $this->option('format');
        
        if ($format === 'json') {
            File::put($outputFile, json_encode($this->auditResults, JSON_PRETTY_PRINT));
            $this->info("📊 SEO audit report generated: {$outputFile}");
        }
    }

    private function displaySummary()
    {
        $this->info("\n" . str_repeat('=', 50));
        $this->info('📊 SEO AUDIT SUMMARY');
        $this->info(str_repeat('=', 50));
        
        $score = $this->auditResults['score'];
        $scoreColor = $score >= 80 ? 'green' : ($score >= 60 ? 'yellow' : 'red');
        
        $this->line("Overall SEO Score: <fg={$scoreColor}>{$score}/100</fg>");
        $this->line("Total Issues Found: " . count($this->auditResults['issues']));
        $this->line("Recommendations: " . count($this->auditResults['recommendations']));
        
        if (!empty($this->auditResults['issues'])) {
            $this->warn("\n🚨 TOP ISSUES TO FIX:");
            foreach (array_slice($this->auditResults['issues'], 0, 5) as $issue) {
                $this->line("  • {$issue}");
            }
        }
        
        if (!empty($this->auditResults['recommendations'])) {
            $this->info("\n💡 TOP RECOMMENDATIONS:");
            foreach (array_slice($this->auditResults['recommendations'], 0, 5) as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
        
        $this->info("\n📈 NEXT STEPS:");
        $this->line("  1. Fix critical SEO issues identified above");
        $this->line("  2. Implement recommended optimizations");
        $this->line("  3. Run this audit monthly to track progress");
        $this->line("  4. Monitor Google Search Console for improvements");
        
        $this->info("\n✅ SEO Audit Complete!");
    }
} 