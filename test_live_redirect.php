<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🌐 Testing Live HTTP Redirects...\n\n";

// Get a real product to test with
$product = DB::select("SELECT id, name, slug FROM products WHERE slug IS NOT NULL LIMIT 1")[0];

if (!$product) {
    echo "❌ No products with slugs found!\n";
    exit(1);
}

echo "Testing Product: {$product->name}\n";
echo "ID: {$product->id}\n";
echo "Slug: {$product->slug}\n\n";

// Test 1: Check old URL redirect using cURL
$oldUrl = "http://127.0.0.1:8000/product/{$product->id}";
$newUrl = "http://127.0.0.1:8000/products/{$product->slug}";

echo "🔍 TEST 1: Old URL Redirect\n";
echo "=" . str_repeat("=", 40) . "\n";
echo "Testing: {$oldUrl}\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $oldUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

if (curl_error($ch)) {
    echo "❌ cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Status: {$httpCode}\n";
    if ($httpCode == 301 || $httpCode == 302) {
        echo "✅ Redirect detected!\n";
        echo "Redirect URL: {$redirectUrl}\n";
        
        if (strpos($redirectUrl, $product->slug) !== false) {
            echo "✅ Redirect target contains slug - CORRECT!\n";
        } else {
            echo "❌ Redirect target doesn't contain slug\n";
        }
    } else {
        echo "❌ No redirect! Expected 301/302\n";
    }
}

curl_close($ch);

echo "\n🔍 TEST 2: New URL Works\n";
echo "=" . str_repeat("=", 40) . "\n";
echo "Testing: {$newUrl}\n";

// Test new URL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $newUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "❌ cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Status: {$httpCode}\n";
    if ($httpCode == 200) {
        echo "✅ New URL works perfectly!\n";
    } else {
        echo "❌ New URL failed! Status: {$httpCode}\n";
    }
}

curl_close($ch);

echo "\n📝 INSTRUCTIONS FOR MANUAL TESTING:\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "1. Start Laravel server: php artisan serve\n";
echo "2. Test old URL: {$oldUrl}\n";
echo "3. Test new URL: {$newUrl}\n";
echo "4. Use browser dev tools to check redirect status\n\n";

echo "🎯 FOR GOOGLE SEARCH CONSOLE:\n";
echo "Old URL: https://maxmed.ae/product/{$product->id}\n";
echo "New URL: https://maxmed.ae/products/{$product->slug}\n";
echo "Status: 301 Permanent Redirect\n\n";

echo "✅ Testing complete!\n"; 