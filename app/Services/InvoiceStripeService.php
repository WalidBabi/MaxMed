<?php

namespace App\Services;

use App\Models\Invoice;
use Stripe\Stripe;
use Stripe\PaymentLink;
use Illuminate\Support\Facades\Log;

class InvoiceStripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create or retrieve a Stripe Payment Link for an invoice
     * 
     * @param Invoice $invoice
     * @param bool $forceNew Force create a new payment link even if one exists
     * @return array ['id' => string, 'url' => string]
     */
    public function getOrCreatePaymentLink(Invoice $invoice, bool $forceNew = false): ?array
    {
        try {
            // Return existing payment link if it exists and we're not forcing new
            if (!$forceNew && $invoice->stripe_payment_link_id && $invoice->stripe_payment_link_url) {
                Log::info("Using existing Stripe payment link for invoice {$invoice->invoice_number}");
                return [
                    'id' => $invoice->stripe_payment_link_id,
                    'url' => $invoice->stripe_payment_link_url
                ];
            }

            // Calculate the amount to charge
            $amount = $this->calculatePaymentAmount($invoice);
            
            // If amount is 0 or less, don't create a payment link
            if ($amount <= 0) {
                Log::warning("Cannot create payment link for invoice {$invoice->invoice_number}: amount is {$amount}");
                return null;
            }

            // Create Stripe Payment Link
            $paymentLink = PaymentLink::create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => strtolower($invoice->currency ?? 'aed'),
                            'product_data' => [
                                'name' => $this->getPaymentDescription($invoice),
                                'description' => $this->getPaymentItemDescription($invoice),
                                'metadata' => [
                                    'invoice_id' => $invoice->id,
                                    'invoice_number' => $invoice->invoice_number,
                                    'customer_name' => $invoice->customer_name,
                                ]
                            ],
                            'unit_amount' => intval($amount * 100), // Convert to cents
                        ],
                        'quantity' => 1,
                    ]
                ],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => [
                        'url' => route('invoice.payment.success', ['invoice' => $invoice->id])
                    ]
                ],
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_type' => $invoice->type,
                    'customer_name' => $invoice->customer_name,
                ]
            ]);

            // Update invoice with payment link details
            $invoice->update([
                'stripe_payment_link_id' => $paymentLink->id,
                'stripe_payment_link_url' => $paymentLink->url
            ]);

            Log::info("Created Stripe payment link for invoice {$invoice->invoice_number}: {$paymentLink->url}");

            return [
                'id' => $paymentLink->id,
                'url' => $paymentLink->url
            ];

        } catch (\Exception $e) {
            Log::error("Failed to create Stripe payment link for invoice {$invoice->invoice_number}: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Calculate the payment amount for the invoice
     * For proforma invoices with advance payment, return the advance amount
     * For final invoices, return the remaining amount due
     */
    private function calculatePaymentAmount(Invoice $invoice): float
    {
        // For proforma invoices
        if ($invoice->type === 'proforma') {
            // If requires advance payment, calculate advance amount
            if ($invoice->requires_advance_payment || in_array($invoice->payment_terms, ['advance_50', 'advance_100', 'custom'])) {
                $advanceAmount = $invoice->getAdvanceAmount();
                // Subtract any amount already paid
                return max(0, $advanceAmount - ($invoice->paid_amount ?? 0));
            }
            
            // For on_delivery or net_30, return full amount
            return max(0, $invoice->total_amount - ($invoice->paid_amount ?? 0));
        }

        // For final invoices, return remaining amount
        if ($invoice->type === 'final') {
            return max(0, $invoice->total_amount - ($invoice->paid_amount ?? 0));
        }

        // Default to total remaining amount
        return max(0, $invoice->total_amount - ($invoice->paid_amount ?? 0));
    }

    /**
     * Get payment description for Stripe
     */
    private function getPaymentDescription(Invoice $invoice): string
    {
        $type = $invoice->type === 'proforma' ? 'Proforma Invoice' : 'Invoice';
        return "{$type} - {$invoice->invoice_number}";
    }

    /**
     * Get detailed payment item description
     */
    private function getPaymentItemDescription(Invoice $invoice): string
    {
        $description = "Payment for {$invoice->invoice_number}";
        
        if ($invoice->type === 'proforma') {
            switch ($invoice->payment_terms) {
                case 'advance_50':
                    $description .= " (50% Advance Payment)";
                    break;
                case 'advance_100':
                    $description .= " (100% Advance Payment)";
                    break;
                case 'custom':
                    if ($invoice->advance_percentage) {
                        $description .= " ({$invoice->advance_percentage}% Advance Payment)";
                    }
                    break;
            }
        } elseif ($invoice->type === 'final') {
            $description .= " (Final Invoice)";
        }

        return $description;
    }

    /**
     * Deactivate/archive a Stripe Payment Link
     */
    public function deactivatePaymentLink(Invoice $invoice): bool
    {
        try {
            if (!$invoice->stripe_payment_link_id) {
                return true;
            }

            $paymentLink = PaymentLink::update(
                $invoice->stripe_payment_link_id,
                ['active' => false]
            );

            Log::info("Deactivated Stripe payment link for invoice {$invoice->invoice_number}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to deactivate Stripe payment link for invoice {$invoice->invoice_number}: " . $e->getMessage());
            return false;
        }
    }
}

