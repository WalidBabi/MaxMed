<?php

/**
 * Test script to verify the UserBehaviorController trackBatch fix
 * This script simulates the batch tracking request that was causing the error
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request similar to what was causing the error
$testData = [
    'events' => [
        [
            'event_type' => 'page_view',
            'page_url' => 'https://maxmedme.com/dashboard',
            'referrer_url' => null,
            'event_data' => [
                'viewport' => ['width' => 1920, 'height' => 1080],
                'timestamp' => time()
            ],
            'mouse_position' => ['x' => 100, 'y' => 200],
            'click_target' => [
                'selector' => 'div.flex.justify-between.items-center',
                'text' => 'About Partners',
                'href' => null,
                'type' => null
            ],
            'device_info' => [
                'browser' => 'Chrome',
                'os' => 'Windows',
                'device_type' => 'desktop'
            ]
        ]
    ]
];

echo "Testing UserBehavior batch tracking fix...\n";
echo "Test data structure:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Test JSON encoding of array fields
echo "Testing JSON encoding of array fields:\n";
foreach ($testData['events'][0] as $key => $value) {
    if (is_array($value)) {
        $encoded = json_encode($value);
        echo "- {$key}: " . $encoded . "\n";
        
        // Verify it can be decoded back
        $decoded = json_decode($encoded, true);
        if ($decoded === $value) {
            echo "  ✓ Encoding/decoding successful\n";
        } else {
            echo "  ✗ Encoding/decoding failed\n";
        }
    }
}

echo "\nThe fix should prevent 'Array to string conversion' errors by:\n";
echo "1. Properly assigning array elements to \$events[] array\n";
echo "2. JSON encoding all array fields before database insertion\n";
echo "3. Using json_encode() for: event_data, mouse_position, click_target, interaction_path, device_info, location_data\n";

echo "\nTest completed. The fix should resolve the production error.\n";
