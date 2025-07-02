<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\CashReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Show delivery tracking page for customers
     */
    public function track(Request $request)
    {
        $trackingNumber = $request->get('tracking');
        $delivery = null;
        
        if ($trackingNumber) {
            $delivery = Delivery::with(['order.items.product', 'order.customer'])
                ->where('tracking_number', $trackingNumber)
                ->first();
        }
        
        return view('delivery.track', compact('delivery', 'trackingNumber'));
    }

    /**
     * Show delivery signature page
     */
    public function signature($trackingNumber)
    {
        $delivery = Delivery::with(['order.items.product', 'order.customer'])
            ->where('tracking_number', $trackingNumber)
            ->first();
            
        if (!$delivery) {
            abort(404, 'Delivery not found');
        }
        
        if ($delivery->status !== 'in_transit') {
            return redirect()->route('delivery.track', ['tracking' => $trackingNumber])
                ->with('error', 'This delivery is not ready for signature.');
        }
        
        if ($delivery->status === 'delivered') {
            return redirect()->route('delivery.track', ['tracking' => $trackingNumber])
                ->with('info', 'This delivery has already been signed for.');
        }
        
        return view('delivery.signature', compact('delivery'));
    }

    /**
     * Process customer signature and handle invoice conversion
     */
    public function processSignature(Request $request, $trackingNumber)
    {
        $request->validate([
            'signature' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'delivery_conditions' => 'nullable|string|max:500'
        ]);

        $delivery = Delivery::where('tracking_number', $trackingNumber)->first();
        
        if (!$delivery) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }
        
        if ($delivery->status !== 'in_transit') {
            return response()->json(['error' => 'Delivery is not ready for signature'], 400);
        }

        try {
            DB::beginTransaction();

            // Update delivery with signature
            $delivery->update([
                'status' => 'delivered',
                'customer_signature' => $request->signature,
                'signature_ip_address' => $request->ip(),
                'signed_at' => now(),
                'delivered_at' => now(),
                'delivery_conditions' => $request->delivery_conditions
            ]);

            // Update order status
            $delivery->order->update(['status' => 'delivered']);

            Log::info("Delivery {$delivery->id} signed by customer. Order {$delivery->order->order_number} marked as delivered.");

            // Auto-convert proforma invoice to final invoice and create cash receipt
            $proformaInvoice = $this->findProformaInvoice($delivery->order);
            
            // Add comprehensive debugging
            if ($proformaInvoice) {
                Log::info("Found proforma invoice: {$proformaInvoice->invoice_number} (ID: {$proformaInvoice->id})");
                Log::info("Proforma invoice status: {$proformaInvoice->status}");
                Log::info("Proforma invoice type: {$proformaInvoice->type}");
                Log::info("Proforma invoice payment terms: {$proformaInvoice->payment_terms}");
                Log::info("Proforma invoice total amount: {$proformaInvoice->total_amount}");
                Log::info("Proforma invoice paid amount: {$proformaInvoice->paid_amount}");
                Log::info("Can convert to final invoice: " . ($proformaInvoice->canConvertToFinalInvoice() ? 'YES' : 'NO'));
                
                // Check if final invoice already exists
                $existingFinalInvoice = $proformaInvoice->childInvoices()->where('type', 'final')->first();
                if ($existingFinalInvoice) {
                    Log::info("Final invoice already exists: {$existingFinalInvoice->invoice_number}");
                }
                
                $results = $this->processInvoiceConversion($proformaInvoice, $delivery);
                
                if ($results['success']) {
                    Log::info("Successfully processed invoice conversion for delivery {$delivery->id}: {$results['message']}");
                } else {
                    Log::warning("Invoice conversion completed with warnings for delivery {$delivery->id}: {$results['message']}");
                }
            } else {
                Log::info("No proforma invoice found for order {$delivery->order->order_number}, skipping conversion.");
                
                // Debug: Check what invoices exist for this order
                $allInvoices = $delivery->order->invoices;
                Log::info("All invoices for order {$delivery->order->order_number}:");
                foreach ($allInvoices as $invoice) {
                    Log::info("  - Invoice {$invoice->invoice_number}: type={$invoice->type}, status={$invoice->status}");
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery signed successfully!',
                'redirect' => route('delivery.track', ['tracking' => $trackingNumber])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to process signature: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to process signature'], 500);
        }
    }

    /**
     * Find the proforma invoice for the given order
     */
    private function findProformaInvoice(Order $order): ?Invoice
    {
        // First try to find a proforma invoice with confirmed status
        $proformaInvoice = $order->proformaInvoice()
            ->where('status', '!=', 'cancelled')
            ->first();
            
        if ($proformaInvoice) {
            return $proformaInvoice;
        }
        
        // If no proforma invoice found via relationship, try a broader search
        $proformaInvoice = Invoice::where('order_id', $order->id)
            ->where('type', 'proforma')
            ->where('status', '!=', 'cancelled')
            ->first();
            
        return $proformaInvoice;
    }

    /**
     * Process the conversion of proforma invoice to final invoice and create cash receipt
     */
    private function processInvoiceConversion(Invoice $proformaInvoice, Delivery $delivery): array
    {
        try {
            // Check if conversion is possible
            if (!$proformaInvoice->canConvertToFinalInvoice()) {
                return [
                    'success' => false,
                    'message' => "Proforma invoice {$proformaInvoice->invoice_number} cannot be converted to final invoice."
                ];
            }

            // Check if final invoice already exists
            if ($proformaInvoice->childInvoices()->where('type', 'final')->exists()) {
                return [
                    'success' => false,
                    'message' => "Final invoice already exists for proforma invoice {$proformaInvoice->invoice_number}."
                ];
            }

            // Convert proforma to final invoice
            $finalInvoice = $proformaInvoice->convertToFinalInvoice($delivery->id, auth()->id());
            
            Log::info("Converted proforma invoice {$proformaInvoice->invoice_number} to final invoice {$finalInvoice->invoice_number}");

            // Determine if cash receipt should be created based on payment terms and amount
            $shouldCreateReceipt = $this->shouldCreateCashReceipt($finalInvoice, $proformaInvoice);
            
            if ($shouldCreateReceipt) {
                $cashReceiptData = $this->prepareCashReceiptData($finalInvoice, $proformaInvoice, $delivery);
                $cashReceipt = CashReceipt::createFromInvoice($finalInvoice, $cashReceiptData);
                
                Log::info("Created cash receipt {$cashReceipt->receipt_number} for final invoice {$finalInvoice->invoice_number} with amount {$cashReceipt->amount} {$cashReceipt->currency} (Payment terms: {$proformaInvoice->payment_terms})");
                
                return [
                    'success' => true,
                    'message' => "Converted proforma invoice {$proformaInvoice->invoice_number} to final invoice {$finalInvoice->invoice_number} and created cash receipt {$cashReceipt->receipt_number}."
                ];
            } else {
                $reason = $this->getCashReceiptSkipReason($finalInvoice, $proformaInvoice);
                Log::info("Converted proforma invoice {$proformaInvoice->invoice_number} to final invoice {$finalInvoice->invoice_number}. Cash receipt not created: {$reason}");
                
                return [
                    'success' => true,
                    'message' => "Converted proforma invoice {$proformaInvoice->invoice_number} to final invoice {$finalInvoice->invoice_number}. {$reason}"
                ];
            }

        } catch (\Exception $e) {
            Log::error("Failed to process invoice conversion: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Prepare cash receipt data based on final invoice and payment terms
     */
    private function prepareCashReceiptData(Invoice $finalInvoice, Invoice $proformaInvoice, Delivery $delivery): array
    {
        $description = $this->generateCashReceiptDescription($finalInvoice, $proformaInvoice);
        
        // Determine if cash receipt should be created and what payment method to use
        $paymentTermsConfig = $this->getPaymentTermsConfiguration($proformaInvoice, $finalInvoice);
        
        return [
            'amount' => $finalInvoice->total_amount,
            'currency' => $finalInvoice->currency ?? 'AED',
            'payment_method' => $paymentTermsConfig['payment_method'],
            'payment_date' => $paymentTermsConfig['payment_date'],
            'description' => $description,
            'reference_number' => $delivery->tracking_number,
            'notes' => $paymentTermsConfig['notes'],
            'status' => CashReceipt::STATUS_ISSUED,
        ];
    }

    /**
     * Get payment terms configuration for cash receipt creation
     */
    private function getPaymentTermsConfiguration(Invoice $proformaInvoice, Invoice $finalInvoice): array
    {
        switch ($proformaInvoice->payment_terms) {
            case 'advance_50':
                return [
                    'payment_method' => CashReceipt::METHOD_CASH,
                    'payment_date' => now()->format('Y-m-d'),
                    'notes' => 'Remaining 50% balance payment after advance payment received on proforma invoice'
                ];

            case 'advance_100':
                return [
                    'payment_method' => CashReceipt::METHOD_CASH,
                    'payment_date' => now()->format('Y-m-d'),
                    'notes' => 'Delivery confirmation receipt - Full payment already received on proforma invoice'
                ];

            case 'on_delivery':
                return [
                    'payment_method' => CashReceipt::METHOD_CASH,
                    'payment_date' => now()->format('Y-m-d'),
                    'notes' => 'Cash payment collected upon delivery as per payment terms'
                ];

            case 'net_30':
                return [
                    'payment_method' => CashReceipt::METHOD_BANK_TRANSFER,
                    'payment_date' => now()->format('Y-m-d'), // Receipt date, not payment due date
                    'notes' => 'Payment acknowledgment - Payment due within 30 days of delivery'
                ];

            case 'custom':
                $advancePercentage = $proformaInvoice->advance_percentage ?? 0;
                if ($advancePercentage > 0) {
                    return [
                        'payment_method' => CashReceipt::METHOD_CASH,
                        'payment_date' => now()->format('Y-m-d'),
                        'notes' => "Remaining balance payment after {$advancePercentage}% advance payment received"
                    ];
                } else {
                    return [
                        'payment_method' => CashReceipt::METHOD_CASH,
                        'payment_date' => now()->format('Y-m-d'),
                        'notes' => 'Payment collected as per custom payment terms'
                    ];
                }

            default:
                return [
                    'payment_method' => CashReceipt::METHOD_CASH,
                    'payment_date' => now()->format('Y-m-d'),
                    'notes' => 'Payment collected upon delivery'
                ];
        }
    }

    /**
     * Generate appropriate description for cash receipt
     */
    private function generateCashReceiptDescription(Invoice $finalInvoice, Invoice $proformaInvoice): string
    {
        $orderNumber = $finalInvoice->order->order_number ?? 'N/A';
        
        return match($proformaInvoice->payment_terms) {
            'advance_50' => "Remaining balance payment for Order #{$orderNumber} (Final Invoice {$finalInvoice->invoice_number})",
            'advance_100' => "Delivery confirmation for Order #{$orderNumber} (Final Invoice {$finalInvoice->invoice_number}) - Fully paid",
            'on_delivery' => "Cash payment on delivery for Order #{$orderNumber} (Final Invoice {$finalInvoice->invoice_number})",
            'net_30' => "Payment acknowledgment for Order #{$orderNumber} (Final Invoice {$finalInvoice->invoice_number}) - Due in 30 days",
            'custom' => $this->getCustomTermsDescription($proformaInvoice, $finalInvoice, $orderNumber),
            default => "Payment for Order #{$orderNumber} (Final Invoice {$finalInvoice->invoice_number})"
        };
    }

    /**
     * Generate description for custom payment terms
     */
    private function getCustomTermsDescription(Invoice $proformaInvoice, Invoice $finalInvoice, string $orderNumber): string
    {
        $advancePercentage = $proformaInvoice->advance_percentage ?? 0;
        
        if ($advancePercentage > 0) {
            if ($finalInvoice->total_amount <= 0) {
                return "Payment confirmation for Order #{$orderNumber} (Final Invoice {$finalInvoice->invoice_number}) - {$advancePercentage}% advance fully covers order";
            } else {
                return "Remaining balance payment for Order #{$orderNumber} (Final Invoice {$finalInvoice->invoice_number}) - After {$advancePercentage}% advance";
            }
        } else {
            return "Payment for Order #{$orderNumber} (Final Invoice {$finalInvoice->invoice_number}) - Custom payment terms";
        }
    }

    /**
     * Determine if a cash receipt should be created based on payment terms and invoice amount
     */
    private function shouldCreateCashReceipt(Invoice $finalInvoice, Invoice $proformaInvoice): bool
    {
        // Always create receipt if there's an amount to collect
        if ($finalInvoice->total_amount > 0) {
            return true;
        }

        // For zero-amount invoices, create receipt only for certain payment terms
        return match($proformaInvoice->payment_terms) {
            'advance_100' => true, // Confirmation receipt even with zero amount
            'advance_50' => $finalInvoice->total_amount <= 0, // Only if balance is zero (fully paid)
            'custom' => $this->shouldCreateReceiptForCustomTerms($proformaInvoice, $finalInvoice),
            default => false // Don't create zero-amount receipts for other terms
        };
    }

    /**
     * Determine if receipt should be created for custom payment terms
     */
    private function shouldCreateReceiptForCustomTerms(Invoice $proformaInvoice, Invoice $finalInvoice): bool
    {
        $advancePercentage = $proformaInvoice->advance_percentage ?? 0;
        
        // If there's an advance percentage and final amount is zero, create confirmation receipt
        if ($advancePercentage > 0 && $finalInvoice->total_amount <= 0) {
            return true;
        }
        
        // If there's an amount due, create receipt
        if ($finalInvoice->total_amount > 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Get reason why cash receipt was skipped
     */
    private function getCashReceiptSkipReason(Invoice $finalInvoice, Invoice $proformaInvoice): string
    {
        if ($finalInvoice->total_amount <= 0) {
            return match($proformaInvoice->payment_terms) {
                'net_30' => 'No cash receipt needed - payment due in 30 days via bank transfer',
                'on_delivery' => 'No cash receipt needed - zero amount due',
                default => 'No cash receipt needed - amount already fully paid'
            };
        }

        // This shouldn't happen if logic is correct, but just in case
        return 'Cash receipt creation skipped for unknown reason';
    }

    /**
     * Download delivery receipt
     */
    public function downloadReceipt($trackingNumber)
    {
        $delivery = Delivery::with(['order.items.product', 'order.customer'])
            ->where('tracking_number', $trackingNumber)
            ->first();
            
        if (!$delivery || $delivery->status !== 'delivered') {
            abort(404, 'Delivery receipt not found');
        }
        
        // Generate PDF receipt (you can implement PDF generation here)
        return view('delivery.receipt', compact('delivery'));
    }
} 