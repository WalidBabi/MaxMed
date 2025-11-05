<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Lead Show Page ===\n\n";

// Find a test lead
$lead = \App\Models\CrmLead::where('source', 'email')->latest()->first();

if (!$lead) {
    echo "❌ No email leads found. Creating a test lead...\n";
    $lead = \App\Models\CrmLead::create([
        'first_name' => 'Test',
        'last_name' => 'Lead',
        'email' => 'test@example.com',
        'phone' => '+971501112222',
        'company_name' => 'Test Company',
        'source' => 'email',
        'status' => 'new_inquiry',
        'priority' => 'medium',
        'notes' => 'Subject: Test Email\n\nThis is a test email content.',
        'assigned_to' => 1,
    ]);
    echo "✅ Created test lead ID: {$lead->id}\n\n";
}

echo "Testing lead ID: {$lead->id}\n";
echo "Lead name: {$lead->full_name}\n";
echo "Source: {$lead->source}\n\n";

// Test controller show method
echo "1. Testing CrmLeadController::show() method...\n";
try {
    $controller = $app->make(\App\Http\Controllers\CrmLeadController::class);
    
    // Simulate auth user
    $user = \App\Models\User::first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        echo "   ✅ Authenticated as user: {$user->name}\n";
    }
    
    // Create request
    $request = new \Illuminate\Http\Request();
    $request->setMethod('GET');
    
    // Call show method
    $response = $controller->show($lead);
    
    echo "   ✅ show() method executed successfully\n";
    echo "   Response type: " . get_class($response) . "\n";
    
} catch (\Exception $e) {
    echo "   ❌ Error in show() method: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

// Test if services resolve correctly
echo "\n2. Testing service resolution (for dependency injection)...\n";
try {
    $parser = $app->make(\App\Services\LeadTextParser::class);
    $llmClient = $app->make(\App\Services\LeadLLMClient::class);
    $enricher = $app->make(\App\Services\LeadTextEnricher::class);
    echo "   ✅ All services resolve correctly\n";
} catch (\Exception $e) {
    echo "   ❌ Service resolution error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";
echo "✅ Lead show page should work correctly\n";



