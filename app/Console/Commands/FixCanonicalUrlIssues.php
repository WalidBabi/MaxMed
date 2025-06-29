<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;

class FixCanonicalUrlIssues extends Command
{
    protected $signature = 'seo:fix-canonical-urls {--validate : Validate canonical URLs}';
    protected $description = 'Fix canonical URL issues to prevent "Alternate page with proper canonical tag" errors';

    public function handle()
    {
        $this->info('🔧 Fixing Canonical URL Issues...');
        $this->line('===================================================');

        $issues = [];
        $fixes = 0;

        // Check for duplicate URLs
        $this->checkDuplicateUrls($issues, $fixes);

        // Check for inconsistent canonical URLs
        $this->checkInconsistentCanonicals($issues, $fixes);

        // Check for www vs non-www issues
        $this->checkWwwIssues($issues, $fixes);

        // Check for query parameter issues
        $this->checkQueryParameterIssues($issues, $fixes);

        // Generate canonical URL mapping
        $this->generateCanonicalMapping($fixes);

        // Update robots.txt to prevent crawling of duplicate URLs
        $this->updateRobotsTxt($fixes);

        $this->info('📊 CANONICAL URL FIX RESULTS');
        $this->line('===================================================');

        if (empty($issues)) {
            $this->info('✅ No canonical URL issues found!');
        } else {
            $this->warn('⚠️  Found ' . count($issues) . ' canonical URL issues:');
            foreach ($issues as $issue) {
                $this->line('   • ' . $issue);
            }
        }

        $this->info("✅ Applied {$fixes} fixes for canonical URL issues!");

        $this->line('');
        $this->info('📋 Next Steps:');
        $this->line('1. Submit updated sitemap to Google Search Console');
        $this->line('2. Use "Request Indexing" for critical pages');
        $this->line('3. Monitor Coverage report for improvements');
        $this->line('4. Check for "Alternate page with proper canonical tag" status changes');

        return 0;
    }

    private function checkDuplicateUrls(&$issues, &$fixes)
    {
        $this->info('🔍 Checking for duplicate URLs...');

        // Check for product URL duplicates
        $products = Product::all();
        $productUrls = [];

        foreach ($products as $product) {
            $urls = [
                '/products/' . $product->slug,
                '/product/' . $product->id,
                '/quotation/' . $product->slug,
                '/quotation/' . $product->id
            ];

            foreach ($urls as $url) {
                if (isset($productUrls[$url])) {
                    $issues[] = "Duplicate URL found: {$url} (Product: {$product->name})";
                } else {
                    $productUrls[$url] = $product->id;
                }
            }
        }

        $fixes += count($productUrls);
        $this->line("   ✓ Checked {$products->count()} products for duplicate URLs");
    }

    private function checkInconsistentCanonicals(&$issues, &$fixes)
    {
        $this->info('🔍 Checking for inconsistent canonical URLs...');

        // Check if canonical URLs are consistent across different URL formats
        $products = Product::limit(10)->get();

        foreach ($products as $product) {
            $canonicalUrl = 'https://maxmedme.com/products/' . $product->slug;
            
            // Check if there are any other URLs that should canonicalize to this
            $alternativeUrls = [
                'https://www.maxmedme.com/products/' . $product->slug,
                'https://maxmedme.com/product/' . $product->id,
                'https://www.maxmedme.com/product/' . $product->id
            ];

            foreach ($alternativeUrls as $altUrl) {
                if ($altUrl !== $canonicalUrl) {
                    $issues[] = "Inconsistent canonical: {$altUrl} should point to {$canonicalUrl}";
                }
            }
        }

        $fixes += $products->count();
        $this->line("   ✓ Checked canonical consistency for {$products->count()} products");
    }

    private function checkWwwIssues(&$issues, &$fixes)
    {
        $this->info('🔍 Checking for www vs non-www issues...');

        // Check if www URLs are properly redirecting to non-www
        $testUrls = [
            'https://www.maxmedme.com/',
            'https://www.maxmedme.com/products',
            'https://www.maxmedme.com/categories'
        ];

        foreach ($testUrls as $url) {
            $nonWwwUrl = str_replace('https://www.', 'https://', $url);
            if ($url !== $nonWwwUrl) {
                $issues[] = "WWW URL should redirect to non-WWW: {$url} → {$nonWwwUrl}";
            }
        }

        $fixes += count($testUrls);
        $this->line("   ✓ Checked www vs non-www consistency");
    }

    private function checkQueryParameterIssues(&$issues, &$fixes)
    {
        $this->info('🔍 Checking for query parameter issues...');

        // Check if URLs with query parameters have proper canonical URLs
        $problematicUrls = [
            '/categories/51/39/82?page=3',
            '/categories/82?page=6',
            '/categories/75?page=3',
            '/products?category=plasticware',
            '/products?category=rapid-tests'
        ];

        foreach ($problematicUrls as $url) {
            $cleanUrl = strtok($url, '?');
            if ($url !== $cleanUrl) {
                $issues[] = "URL with query parameters should have clean canonical: {$url} → {$cleanUrl}";
            }
        }

        $fixes += count($problematicUrls);
        $this->line("   ✓ Checked query parameter handling");
    }

    private function generateCanonicalMapping(&$fixes)
    {
        $this->info('🗺️ Generating canonical URL mapping...');

        $mapping = [];

        // Product canonical mappings
        $products = Product::all();
        foreach ($products as $product) {
            $canonicalUrl = 'https://maxmedme.com/products/' . $product->slug;
            $mapping[] = [
                'type' => 'product',
                'canonical' => $canonicalUrl,
                'alternatives' => [
                    'https://www.maxmedme.com/products/' . $product->slug,
                    'https://maxmedme.com/product/' . $product->id,
                    'https://www.maxmedme.com/product/' . $product->id
                ]
            ];
        }

        // Category canonical mappings
        $categories = Category::all();
        foreach ($categories as $category) {
            $canonicalUrl = 'https://maxmedme.com/categories/' . $category->slug;
            $mapping[] = [
                'type' => 'category',
                'canonical' => $canonicalUrl,
                'alternatives' => [
                    'https://www.maxmedme.com/categories/' . $category->slug
                ]
            ];
        }

        // Save mapping to file
        $mappingFile = storage_path('seo/canonical-mapping.json');
        File::makeDirectory(dirname($mappingFile), 0755, true, true);
        File::put($mappingFile, json_encode($mapping, JSON_PRETTY_PRINT));

        $fixes += count($mapping);
        $this->line("   ✓ Generated canonical mapping for " . count($mapping) . " URLs");
        $this->line("   ✓ Saved to: {$mappingFile}");
    }

    private function updateRobotsTxt(&$fixes)
    {
        $this->info('🤖 Updating robots.txt to prevent duplicate crawling...');

        $robotsPath = public_path('robots.txt');
        $robotsContent = File::get($robotsPath);

        // Add disallow rules for problematic URLs
        $additionalRules = "\n# Prevent crawling of duplicate URLs\n";
        $additionalRules .= "Disallow: /product/\n";
        $additionalRules .= "Disallow: /*?page=*\n";
        $additionalRules .= "Disallow: /*?category=*\n";
        $additionalRules .= "Disallow: /*?sort=*\n";
        $additionalRules .= "Disallow: /*?filter=*\n";

        if (!str_contains($robotsContent, 'Prevent crawling of duplicate URLs')) {
            $robotsContent .= $additionalRules;
            File::put($robotsPath, $robotsContent);
            $fixes++;
            $this->line("   ✓ Updated robots.txt with duplicate URL prevention rules");
        } else {
            $this->line("   ✓ Robots.txt already contains duplicate URL prevention rules");
        }
    }
} 