<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_number',
        'payment_method',
        'amount',
        'currency',
        'payment_date',
        'transaction_reference',
        'payment_notes',
        'status',
        'payment_details',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'payment_details' => 'array',
        'processed_at' => 'datetime'
    ];

    const PAYMENT_METHODS = [
        'bank_transfer' => 'Bank Transfer',
        'credit_card' => 'Credit Card',
        'check' => 'Check',
        'cash' => 'Cash',
        'online' => 'Online Payment',
        'other' => 'Other'
    ];

    const STATUS_OPTIONS = [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'refunded' => 'Refunded'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = static::generatePaymentNumber();
            }
        });

        static::saved(function ($payment) {
            // Update invoice payment status when payment is saved
            $payment->updateInvoicePaymentStatus();
        });
    }

    /**
     * Generate unique payment number
     */
    public static function generatePaymentNumber()
    {
        $lastPayment = static::orderBy('id', 'desc')->first();
        $number = $lastPayment ? intval(substr($lastPayment->payment_number, 4)) + 1 : 1;
        return 'PAY-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Business Logic
     */
    public function updateInvoicePaymentStatus()
    {
        if ($this->status !== 'completed') {
            Log::info("Payment {$this->id} status is not completed: {$this->status}");
            return;
        }

        $invoice = $this->invoice;
        
        // Always recalculate totals before checking payment status to ensure total_amount is correct
        $invoice->calculateTotals();
        $invoice->refresh();
        
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        
        // Verify that total_amount is post-discount amount
        $calculatedSubtotal = $invoice->items->sum(function($item) {
            return $item->quantity * $item->unit_price;
        });
        $itemDiscounts = $invoice->items->sum('calculated_discount_amount');
        $invoiceDiscount = $invoice->discount_amount ?? 0;
        $totalDiscount = $itemDiscounts + $invoiceDiscount;
        $expectedTotal = $calculatedSubtotal - $totalDiscount + ($invoice->tax_amount ?? 0);
        
        // Log if there's a discrepancy in totals
        if (abs($invoice->total_amount - $expectedTotal) > 0.01) {
            Log::warning("Invoice {$invoice->id} total amount discrepancy: stored={$invoice->total_amount}, calculated={$expectedTotal}, forcing recalculation");
            $invoice->calculateTotals();
            $invoice->refresh();
        }
        
        Log::info("Processing payment for invoice {$invoice->id}: total_amount={$invoice->total_amount}, total_paid={$totalPaid}, payment_terms={$invoice->payment_terms}, discount_amount={$invoice->discount_amount}");
        
        $oldPaymentStatus = $invoice->payment_status;
        $newPaymentStatus = $this->determinePaymentStatus($invoice->total_amount, $totalPaid);
        
        $invoice->update([
            'paid_amount' => $totalPaid,
            'payment_status' => $newPaymentStatus,
            'paid_at' => $totalPaid >= $invoice->total_amount ? now() : null
        ]);

        Log::info("Updated invoice {$invoice->id} payment status from '{$oldPaymentStatus}' to '{$newPaymentStatus}', paid_amount={$totalPaid}");

        // Check conditions before triggering automation
        $freshInvoice = $invoice->fresh();
        Log::info("Invoice {$freshInvoice->id} before automation: type={$freshInvoice->type}, status={$freshInvoice->status}, payment_status={$freshInvoice->payment_status}, order_id={$freshInvoice->order_id}");
        
        // Trigger workflow automation after payment status is updated
        $freshInvoice->handleWorkflowAutomation();
        
        // Check if order was created
        $finalInvoice = $invoice->fresh();
        Log::info("Invoice {$finalInvoice->id} after automation: order_id={$finalInvoice->order_id}, status={$finalInvoice->status}");
        if ($finalInvoice->order_id && !$invoice->order_id) {
            Log::info("SUCCESS: Order {$finalInvoice->order_id} was created automatically for invoice {$finalInvoice->id}");
        } elseif (!$finalInvoice->order_id) {
            Log::warning("No order was created for invoice {$finalInvoice->id} after payment processing");
        }
    }

    private function determinePaymentStatus($totalAmount, $paidAmount)
    {
        if ($paidAmount <= 0) {
            return 'pending';
        } elseif ($paidAmount >= $totalAmount) {
            return 'paid';
        } else {
            return 'partial';
        }
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge bg-warning',
            'completed' => 'badge bg-success',
            'failed' => 'badge bg-danger',
            'refunded' => 'badge bg-info',
            default => 'badge bg-secondary'
        };
    }
} 