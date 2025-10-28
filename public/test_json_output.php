<?php
// Test @json directive output
// Access via: http://127.0.0.1:8000/test_json_output.php?v=<?php echo time(); ?>

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Models\Product;

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$product = Product::with('specifications')->find(617);

if (!$product) {
    die('Product not found!');
}

$specs = $product->specifications->map(function($spec) {
    return $spec->display_name . ': ' . $spec->formatted_value;
})->values()->toArray();

$jsonOutput = json_encode($specs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

?>
<!DOCTYPE html>
<html>
<head>
    <title>JSON Test</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>JSON Parsing Test</h1>
    
    <div class="section">
        <h2>Product: <?php echo $product->name; ?></h2>
        <p>ID: <?php echo $product->id; ?> | Specs Count: <?php echo $product->specifications->count(); ?></p>
    </div>

    <div class="section">
        <h2>JSON Output (from PHP):</h2>
        <pre><?php echo htmlspecialchars($jsonOutput); ?></pre>
    </div>

    <div class="section">
        <h2>JavaScript Parse Test:</h2>
        <div id="result"></div>
    </div>

    <script>
        const rawJson = <?php echo $jsonOutput; ?>;
        const resultDiv = document.getElementById('result');
        
        console.log('Raw JSON from PHP:', rawJson);
        
        try {
            if (Array.isArray(rawJson)) {
                resultDiv.innerHTML = '<p class="success">✅ JSON parsed successfully!</p>';
                resultDiv.innerHTML += '<p>Array length: ' + rawJson.length + '</p>';
                resultDiv.innerHTML += '<h3>Parsed Data:</h3>';
                rawJson.forEach((item, index) => {
                    resultDiv.innerHTML += '<pre>' + (index + 1) + '. ' + item.substring(0, 100) + '...</pre>';
                });
            } else {
                resultDiv.innerHTML = '<p class="error">❌ Not an array!</p>';
            }
        } catch (e) {
            resultDiv.innerHTML = '<p class="error">❌ Parse Error: ' + e.message + '</p>';
            console.error('Parse error:', e);
        }
    </script>

    <div class="section">
        <h2>Test in Quote Form:</h2>
        <p>The data-specifications attribute should contain:</p>
        <pre><?php echo htmlspecialchars($jsonOutput); ?></pre>
    </div>

    <hr>
    <p><a href="/admin/quotes/create">Go to Quote Create (may be cached)</a></p>
    <p><strong>To bypass cache:</strong> <a href="/admin/quotes/create?nocache=<?php echo time(); ?>">Quote Create with Cache Buster</a></p>
</body>
</html>

