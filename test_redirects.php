<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔗 Testing URL Redirects for Google Search Console SEO...\n\n";

// Get some real products to test with
$products = DB::select("SELECT id, name, slug FROM products WHERE slug IS NOT NULL LIMIT 5");
$categories = DB::select("SELECT id, name, slug FROM categories WHERE slug IS NOT NULL LIMIT 3");

echo "📋 Test Data Summary:\n";
echo "   Products found: " . count($products) . "\n";
echo "   Categories found: " . count($categories) . "\n\n";

// Test 1: Product URL redirects
echo "🔍 TEST 1: Product URL Redirects\n";
echo "=" . str_repeat("=", 50) . "\n";

foreach ($products as $product) {
    echo "Testing Product: {$product->name}\n";
    echo "   Old URL: /product/{$product->id}\n";
    echo "   New URL: /products/{$product->slug}\n";
    
    // Test the redirect using Laravel's routing
    try {
        // Simulate the old URL request
        $request = \Illuminate\Http\Request::create("/product/{$product->id}", 'GET');
        $response = app()->handle($request);
        
        if ($response->getStatusCode() === 302 || $response->getStatusCode() === 301) {
            $location = $response->headers->get('Location');
            $expectedUrl = url("/products/{$product->slug}");
            
            echo "   ✅ Redirect Status: {$response->getStatusCode()}\n";
            echo "   ✅ Redirects to: {$location}\n";
            
            if ($location === $expectedUrl) {
                echo "   ✅ Redirect target is CORRECT\n";
            } else {
                echo "   ❌ Redirect target is WRONG\n";
                echo "      Expected: {$expectedUrl}\n";
                echo "      Got: {$location}\n";
            }
        } else {
            echo "   ❌ No redirect! Status: {$response->getStatusCode()}\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error testing redirect: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 2: Check if new URLs work
echo "\n🔍 TEST 2: New Slug-Based URLs Work\n";
echo "=" . str_repeat("=", 50) . "\n";

foreach (array_slice($products, 0, 2) as $product) {
    echo "Testing New URL: /products/{$product->slug}\n";
    
    try {
        $request = \Illuminate\Http\Request::create("/products/{$product->slug}", 'GET');
        $response = app()->handle($request);
        
        if ($response->getStatusCode() === 200) {
            echo "   ✅ New URL works perfectly (200 OK)\n";
        } else {
            echo "   ❌ New URL failed! Status: {$response->getStatusCode()}\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Generate Google Search Console Migration List
echo "\n📊 GOOGLE SEARCH CONSOLE MIGRATION LIST\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "Use this data to set up URL redirects in Google Search Console:\n\n";

echo "PRODUCT URL MIGRATIONS:\n";
foreach ($products as $product) {
    $oldUrl = "https://maxmed.ae/product/{$product->id}";
    $newUrl = "https://maxmed.ae/products/{$product->slug}";
    echo "{$oldUrl} → {$newUrl}\n";
}

echo "\nCATEGORY URL MIGRATIONS:\n";
foreach ($categories as $category) {
    // Note: We need to update category routes too
    $oldUrl = "https://maxmed.ae/categories/{$category->id}";
    $newUrl = "https://maxmed.ae/categories/{$category->slug}";
    echo "{$oldUrl} → {$newUrl}\n";
}

// Test 3: Check route definitions
echo "\n\n🔍 TEST 3: Route Definitions Check\n";
echo "=" . str_repeat("=", 50) . "\n";

$routes = app('router')->getRoutes();
$productRoutes = [];
$categoryRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'product') !== false) {
        $productRoutes[] = $uri . ' → ' . $route->getName();
    }
    if (strpos($uri, 'categor') !== false && !strpos($uri, 'admin') && !strpos($uri, 'supplier')) {
        $categoryRoutes[] = $uri . ' → ' . $route->getName();
    }
}

echo "Product Routes Found:\n";
foreach ($productRoutes as $route) {
    echo "   {$route}\n";
}

echo "\nCategory Routes Found:\n";
foreach ($categoryRoutes as $route) {
    echo "   {$route}\n";
}

// Test 4: SEO Impact Assessment
echo "\n\n📈 SEO IMPACT ASSESSMENT\n";
echo "=" . str_repeat("=", 50) . "\n";

$totalProducts = DB::select("SELECT COUNT(*) as count FROM products")[0]->count;
$totalCategories = DB::select("SELECT COUNT(*) as count FROM categories")[0]->count;

echo "BEFORE vs AFTER URLs:\n\n";

echo "BEFORE (Bad for SEO):\n";
echo "   Products: /product/1, /product/2, /product/3...\n";
echo "   Categories: /categories/1, /categories/2, /categories/3...\n";
echo "   Total URLs affected: " . ($totalProducts + $totalCategories) . "\n\n";

echo "AFTER (SEO Optimized):\n";
echo "   Products: /products/sk-o330-pro-large-multifunctional-orbital-decolorizing-shaker...\n";
echo "   Categories: /categories/rapid-test-kits-rdt-womens-health-rapid-tests\n";
echo "   SEO Benefits: ✅ Keyword-rich ✅ Local targeting ✅ Clean structure\n\n";

echo "🎯 GOOGLE SEARCH CONSOLE ACTIONS NEEDED:\n";
echo "1. Submit new sitemap with slug-based URLs\n";
echo "2. Set up 301 redirects for old URLs\n";
echo "3. Monitor crawl errors for any missed redirects\n";
echo "4. Update internal links to use new URLs\n";
echo "5. Request reindexing of key product/category pages\n\n";

echo "🏆 Expected SEO Improvements:\n";
echo "   • Better keyword relevance in URLs\n";
echo "   • Improved local search rankings (dubai-uae)\n";
echo "   • Higher click-through rates from search results\n";
echo "   • Better user experience with descriptive URLs\n\n";

echo "✅ Redirect testing complete!\n"; 