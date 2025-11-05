<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Call controller directly
$request = new \Illuminate\Http\Request([
    'from' => 'Sarah Ali <sarah@medix-uae.com>',
    'subject' => 'Need ENT catalog',
    'body' => 'Hi, please send ENT price list. My phone +971 50 111 2222. Thanks!'
]);
$request->setMethod('POST');

$controller = $app->make(\App\Http\Controllers\Webhook\InboundLeadWebhookController::class);
$response = $controller->outlook($request);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Body:\n" . $response->getContent() . "\n\n";

// Check if lead was created
$lead = \App\Models\CrmLead::where('email', 'sarah@medix-uae.com')
    ->where('source', 'email')
    ->latest()
    ->first();

if ($lead) {
    echo "✅ Lead created successfully!\n";
    echo "Lead ID: {$lead->id}\n";
    echo "Name: {$lead->first_name} {$lead->last_name}\n";
    echo "Email: {$lead->email}\n";
    echo "Phone: {$lead->phone}\n";
    echo "Company: {$lead->company_name}\n";
    echo "Source: {$lead->source}\n";
    echo "Status: {$lead->status}\n";
} else {
    echo "❌ Lead not found. Check logs for errors.\n";
}

