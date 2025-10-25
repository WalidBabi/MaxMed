<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class InvoicePaymentController extends Controller
{
    /**
     * Handle successful payment callback from Stripe
     */
    public function paymentSuccess(Request $request, Invoice $invoice)
    {
        try {
            Log::info("Payment success callback for invoice {$invoice->invoice_number}", [
                'session_id' => $request->get('session_id'),
                'invoice_id' => $invoice->id
            ]);

            // If we have a Stripe session ID, verify the payment
            if ($sessionId = $request->get('session_id')) {
                Stripe::setApiKey(config('services.stripe.secret'));
                
                try {
                    $session = Session::retrieve($sessionId);
                    
                    // Verify the payment was successful
                    if ($session->payment_status === 'paid') {
                        // Record the payment
                        $paymentAmount = $session->amount_total / 100; // Convert from cents
                        
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'payment_date' => now(),
                            'amount' => $paymentAmount,
                            'payment_method' => 'stripe',
                            'transaction_reference' => $session->payment_intent,
                            'notes' => 'Payment via Stripe Payment Link'
                        ]);

                        // Update invoice paid amount
                        $newPaidAmount = ($invoice->paid_amount ?? 0) + $paymentAmount;
                        $invoice->update([
                            'paid_amount' => $newPaidAmount,
                            'payment_status' => $newPaidAmount >= $invoice->total_amount ? 'paid' : 'partial',
                            'paid_at' => $newPaidAmount >= $invoice->total_amount ? now() : $invoice->paid_at
                        ]);

                        Log::info("Payment recorded for invoice {$invoice->invoice_number}: {$paymentAmount}");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to verify Stripe session: " . $e->getMessage());
                }
            }

            // Redirect to success page
            return view('invoice-payment-success', [
                'invoice' => $invoice,
                'message' => 'Thank you! Your payment has been received successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error("Payment success callback error: " . $e->getMessage());
            return view('invoice-payment-success', [
                'invoice' => $invoice,
                'message' => 'Payment received. We will process your payment shortly.',
                'error' => true
            ]);
        }
    }

    /**
     * View invoice details (public access for customers)
     */
    public function viewInvoice(Invoice $invoice)
    {
        $invoice->load(['items.product', 'payments']);
        
        return view('invoice-view', [
            'invoice' => $invoice
        ]);
    }
}

