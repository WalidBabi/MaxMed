<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING DYNAMIC DATE RANGE ===\n";

// Create controller instance
$controller = new \App\Http\Controllers\Admin\DashboardController();

// Use reflection to access private methods
$reflection = new ReflectionClass($controller);

// Test getEarliestTransactionDate method
$method = $reflection->getMethod('getEarliestTransactionDate');
$method->setAccessible(true);

echo "Getting earliest transaction date...\n";
$earliestDate = $method->invoke($controller);
echo "Earliest transaction date: " . $earliestDate->format('Y-m-d') . "\n";

// Test getSalesChartData method
$method = $reflection->getMethod('getSalesChartData');
$method->setAccessible(true);

echo "\nGetting sales chart data with dynamic range...\n";
$salesData = $method->invoke($controller);

echo "Number of months: " . count($salesData['labels']) . "\n";
echo "Date range: " . $salesData['labels'][0] . " to " . end($salesData['labels']) . "\n";
echo "Labels: " . implode(', ', $salesData['labels']) . "\n";
echo "Total AED: " . $salesData['total_aed'] . "\n";
echo "Zero months: " . implode(', ', $salesData['zero_months']) . "\n";

