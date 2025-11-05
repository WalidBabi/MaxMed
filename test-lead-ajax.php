<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Lead AJAX Endpoint ===\n\n";

// Authenticate
$user = \App\Models\User::first();
if ($user) {
    \Illuminate\Support\Facades\Auth::login($user);
    echo "✅ Authenticated as: {$user->name}\n";
}

// Get lead
$lead = \App\Models\CrmLead::find(104);
if (!$lead) {
    echo "❌ Lead 104 not found\n";
    exit(1);
}

echo "Testing lead ID: {$lead->id}\n";
echo "Lead name: {$lead->full_name}\n\n";

// Test controller show method with AJAX request
echo "1. Testing show() method with AJAX header...\n";
try {
    $controller = $app->make(\App\Http\Controllers\CrmLeadController::class);
    
    // Create AJAX request
    $request = new \Illuminate\Http\Request();
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    $request->setMethod('GET');
    
    // Call show method
    $response = $controller->show($lead);
    
    echo "   ✅ show() method executed successfully\n";
    echo "   Response type: " . get_class($response) . "\n";
    
    // Try to render the view
    if ($response instanceof \Illuminate\View\View) {
        $html = $response->render();
        echo "   ✅ View rendered successfully\n";
        echo "   HTML length: " . strlen($html) . " bytes\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

// Check if partial view exists
echo "\n2. Checking partial view file...\n";
$partialPath = resource_path('views/crm/leads/partials/show-content.blade.php');
if (file_exists($partialPath)) {
    echo "   ✅ Partial view exists: {$partialPath}\n";
} else {
    echo "   ❌ Partial view not found: {$partialPath}\n";
}

echo "\n=== Test Complete ===\n";



