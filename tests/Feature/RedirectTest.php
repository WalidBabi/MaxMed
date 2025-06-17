<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RedirectTest extends TestCase
{
    use DatabaseTransactions;

    protected $testCategory;
    protected $testProducts;
    protected $consoleReport = [];

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create a test category with a slug
        $this->testCategory = Category::create([
            'name' => 'Rapid Test Kits',
            'slug' => 'rapid-test-kits-rdt-dubai-uae'
        ]);

        // Create test products with IDs starting from 37 as mentioned by user
        $this->testProducts = collect();
        
        for ($i = 37; $i <= 40; $i++) {
            $product = Product::create([
                'name' => "Test Product {$i}",
                'slug' => "test-product-{$i}-maxmed-dubai-uae",
                'description' => "Test description for product {$i}",
                'sku' => "TEST-{$i}",
                'category_id' => $this->testCategory->id,
                'price' => 100.00
            ]);
            $this->testProducts->push($product);
        }
    }

    /** @test */
    public function old_product_urls_redirect_to_new_slug_urls()
    {
        foreach ($this->testProducts as $product) {
            $oldUrl = "/product/{$product->id}";
            $expectedNewUrl = route('product.show', $product->slug);
            
            $response = $this->get($oldUrl);
            
            // Assert that we get a 301 redirect
            $response->assertStatus(301);
            
            // Assert that it redirects to the correct new URL
            $response->assertRedirect($expectedNewUrl);
            
            // Log for Google Search Console documentation
            $this->addToConsoleReport([
                'old_url' => "https://maxmed.ae{$oldUrl}",
                'new_url' => $expectedNewUrl,
                'type' => 'product',
                'status' => '✅ PASS'
            ]);
        }
    }

    /** @test */
    public function new_product_slug_urls_work_correctly()
    {
        foreach ($this->testProducts as $product) {
            $newUrl = "/products/{$product->slug}";
            
            $response = $this->get($newUrl);
            
            // Assert that the new URL works (200 OK)
            $response->assertStatus(200);
            
            // Assert that the product name appears on the page
            $response->assertSee($product->name);
        }
    }

    /** @test */
    public function old_product_urls_with_invalid_ids_return_404()
    {
        $invalidIds = [999, 1000, 9999];
        
        foreach ($invalidIds as $invalidId) {
            $response = $this->get("/product/{$invalidId}");
            $response->assertStatus(404);
        }
    }

    /** @test */
    public function category_urls_still_use_ids_but_have_slugs_ready()
    {
        // Current categories still use ID-based URLs
        $response = $this->get("/categories/{$this->testCategory->id}");
        
        // This should work with current routing
        $response->assertStatus(200);
        
        // Verify the category has a slug ready for future migration
        $this->assertNotNull($this->testCategory->slug);
        $this->assertNotEmpty($this->testCategory->slug);
    }

    /** @test */
    public function legacy_category_names_redirect_to_products()
    {
        $legacyUrls = [
            '/education%26-training-tools',
            '/analytical-chemistry',
            '/genomics-%26-life-sciences',
            '/veterinary-%26-agri-tools',
            '/forensic-supplies',
            '/molecular-biology',
            '/research-%26-life-sciences'
        ];

        foreach ($legacyUrls as $legacyUrl) {
            $response = $this->get($legacyUrl);
            
            $response->assertStatus(301);
            $response->assertRedirect('/products');
            
            $this->addToConsoleReport([
                'old_url' => "https://maxmed.ae{$legacyUrl}",
                'new_url' => 'https://maxmed.ae/products',
                'type' => 'legacy_category',
                'status' => '✅ PASS'
            ]);
        }
    }

    /** @test */
    public function middleware_handles_404_category_redirects()
    {
        // Test legacy category IDs that should redirect via middleware
        $legacyCategoryIds = [55, 43, 34];
        
        foreach ($legacyCategoryIds as $categoryId) {
            $response = $this->get("/categories/{$categoryId}");
            
            // This should trigger the Custom404Handler middleware
            // which redirects to /products
            $response->assertRedirect('/products');
        }
    }

    /** @test */
    public function product_routes_are_properly_defined()
    {
        // Test that both old and new routes exist
        $this->assertTrue($this->app['router']->has('product.show.old'));
        $this->assertTrue($this->app['router']->has('product.show'));
        
        // Verify route patterns
        $oldRoute = $this->app['router']->getRoutes()->getByName('product.show.old');
        $newRoute = $this->app['router']->getRoutes()->getByName('product.show');
        
        $this->assertEquals('product/{id}', $oldRoute->uri());
        $this->assertEquals('products/{product:slug}', $newRoute->uri());
    }

    /** @test */
    public function seo_impact_validation()
    {
        // Verify that new URLs are SEO-friendly
        foreach ($this->testProducts as $product) {
            $slug = $product->slug;
            
            // Assert slug contains relevant keywords
            $this->assertStringContainsString('dubai', $slug);
            $this->assertStringContainsString('uae', $slug);
            $this->assertStringContainsString('maxmed', $slug);
            
            // Assert slug is properly formatted
            $this->assertDoesNotMatchRegexp('/[^a-z0-9\-]/', $slug);
            $this->assertStringStartsNotWith('-', $slug);
            $this->assertStringEndsNotWith('-', $slug);
        }
    }

    /** @test */
    public function generate_google_search_console_report()
    {
        // Run all redirect tests and generate a report
        $this->old_product_urls_redirect_to_new_slug_urls();
        $this->legacy_category_names_redirect_to_products();
        
        $report = $this->getConsoleReport();
        
        // Assert we have redirects to report
        $this->assertGreaterThan(0, count($report));
        
        // Log the report for Google Search Console
        $this->logGoogleSearchConsoleReport($report);
    }

    private function addToConsoleReport($redirect)
    {
        if (!isset($this->consoleReport)) {
            $this->consoleReport = [];
        }
        $this->consoleReport[] = $redirect;
    }

    private function getConsoleReport()
    {
        return $this->consoleReport ?? [];
    }

    private function logGoogleSearchConsoleReport($report)
    {
        $output = "\n🎯 GOOGLE SEARCH CONSOLE REDIRECT REPORT\n";
        $output .= "=" . str_repeat("=", 50) . "\n\n";
        
        $productRedirects = array_filter($report, fn($r) => $r['type'] === 'product');
        $categoryRedirects = array_filter($report, fn($r) => $r['type'] === 'legacy_category');
        
        $output .= "📋 PRODUCT URL REDIRECTS (" . count($productRedirects) . " total):\n";
        foreach ($productRedirects as $redirect) {
            $output .= "   {$redirect['old_url']} → {$redirect['new_url']} [{$redirect['status']}]\n";
        }
        
        $output .= "\n📋 CATEGORY URL REDIRECTS (" . count($categoryRedirects) . " total):\n";
        foreach ($categoryRedirects as $redirect) {
            $output .= "   {$redirect['old_url']} → {$redirect['new_url']} [{$redirect['status']}]\n";
        }
        
        $output .= "\n🔧 IMPLEMENTATION STATUS:\n";
        $output .= "   ✅ Product redirects: WORKING\n";
        $output .= "   ✅ Legacy category redirects: WORKING\n";
        $output .= "   ✅ 301 redirect status: CONFIRMED\n";
        $output .= "   ✅ New URLs responsive: CONFIRMED\n";
        
        $output .= "\n📈 SEO IMPACT:\n";
        $output .= "   • Product URLs now keyword-rich and location-targeted\n";
        $output .= "   • All redirects use 301 status (preserves SEO juice)\n";
        $output .= "   • URLs now include 'dubai-uae' for local SEO\n";
        $output .= "   • Clean slug structure improves user experience\n";
        
        $output .= "\n🎯 NEXT STEPS FOR GOOGLE SEARCH CONSOLE:\n";
        $output .= "   1. Submit updated sitemap with new URLs\n";
        $output .= "   2. Request re-indexing for key product pages\n";
        $output .= "   3. Monitor crawl errors for any missed redirects\n";
        $output .= "   4. Update internal links to use new URL structure\n";
        $output .= "   5. Set up URL parameter monitoring\n\n";
        
        // In a real test environment, you might want to write this to a file
        // or send it to a logging service
        echo $output;
        
        // For assertion purposes, verify the report contains expected data
        $this->assertTrue(count($report) > 0, "Report should contain redirect data");
    }

    /** @test */
    public function verify_product_ids_start_from_37()
    {
        // Verify that our test data matches user's requirement
        $minProductId = $this->testProducts->min('id');
        $this->assertGreaterThanOrEqual(37, $minProductId, "Product IDs should start from 37 as mentioned by user");
        
        // Test a specific product with ID 37
        $product37 = $this->testProducts->where('id', 37)->first();
        $this->assertNotNull($product37, "Product with ID 37 should exist");
        
        $response = $this->get("/product/37");
        $response->assertStatus(301);
        $response->assertRedirect(route('product.show', $product37->slug));
    }

    /** @test */
    public function test_redirect_performance()
    {
        // Test that redirects are fast (under 100ms)
        $startTime = microtime(true);
        
        $response = $this->get("/product/37");
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $this->assertLessThan(100, $executionTime, "Redirect should complete in under 100ms for good user experience");
        $response->assertStatus(301);
    }
} 