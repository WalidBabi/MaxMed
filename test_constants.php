<?php

require_once 'vendor/autoload.php';

use App\Models\Order;

echo "Testing Order model constants:\n";
echo "STATUS_AWAITING_QUOTATIONS: " . Order::STATUS_AWAITING_QUOTATIONS . "\n";
echo "STATUS_QUOTATIONS_RECEIVED: " . Order::STATUS_QUOTATIONS_RECEIVED . "\n";
echo "STATUS_APPROVED: " . Order::STATUS_APPROVED . "\n";
echo "STATUS_DELIVERED: " . Order::STATUS_DELIVERED . "\n";
echo "STATUS_COMPLETED: " . Order::STATUS_COMPLETED . "\n";
echo "QUOTATION_STATUS_APPROVED: " . Order::QUOTATION_STATUS_APPROVED . "\n";
echo "QUOTATION_STATUS_PENDING: " . Order::QUOTATION_STATUS_PENDING . "\n";
echo "QUOTATION_STATUS_REJECTED: " . Order::QUOTATION_STATUS_REJECTED . "\n";
echo "QUOTATION_STATUS_PARTIAL: " . Order::QUOTATION_STATUS_PARTIAL . "\n";
echo "QUOTATION_STATUS_COMPLETE: " . Order::QUOTATION_STATUS_COMPLETE . "\n";
echo "All constants are properly defined!\n"; 