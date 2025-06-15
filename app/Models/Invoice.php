<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'type',
        'quote_id',
        'order_id',
        'delivery_id',
        'parent_invoice_id',
        'customer_name',
        'billing_address',
        'shipping_address',
        'invoice_date',
        'due_date',
        'description',
        'terms_conditions',
        'notes',
        'sub_total',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'payment_status',
        'payment_terms',
        'paid_amount',
        'advance_percentage',
        'payment_due_date',
        'paid_at',
        'status',
        'is_proforma',
        'requires_advance_payment',
        'sent_at',
        'email_history',
        'attachments',
        'reference_number',
        'po_number',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_due_date' => 'datetime',
        'paid_at' => 'datetime',
        'sent_at' => 'datetime',
        'email_history' => 'array',
        'attachments' => 'array',
        'sub_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'advance_percentage' => 'decimal:2',
        'is_proforma' => 'boolean',
        'requires_advance_payment' => 'boolean'
    ];

    const PAYMENT_TERMS = [
        'advance_50' => '50% Advance Payment',
        'advance_100' => '100% Advance Payment',
        'on_delivery' => 'Payment on Delivery',
        'net_30' => 'Net 30 Days',
        'custom' => 'Custom Terms'
    ];

    const PAYMENT_STATUS = [
        'pending' => 'Pending',
        'partial' => 'Partially Paid',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled'
    ];

    const STATUS_OPTIONS = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'confirmed' => 'Confirmed',
        'in_production' => 'In Production',
        'ready_to_ship' => 'Ready to Ship',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber($invoice->type);
            }
        });

        // Automatic workflow triggers
        static::updated(function ($invoice) {
            $invoice->handleWorkflowAutomation();
        });
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber($type = 'proforma')
    {
        $prefix = $type === 'proforma' ? 'PF' : 'INV';
        $lastInvoice = static::where('invoice_number', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, strlen($prefix) + 1)) + 1 : 1;
        return $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function parentInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'parent_invoice_id');
    }

    public function childInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'parent_invoice_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Business Logic Methods
     */
    public function calculateTotals()
    {
        $subTotal = $this->items->sum('line_total');
        $this->update([
            'sub_total' => $subTotal,
            'total_amount' => $subTotal + $this->tax_amount - $this->discount_amount
        ]);
    }

    public function getAdvanceAmount()
    {
        if ($this->payment_terms === 'advance_50') {
            return $this->total_amount * 0.5;
        } elseif ($this->payment_terms === 'advance_100') {
            return $this->total_amount;
        } elseif ($this->payment_terms === 'custom' && $this->advance_percentage) {
            return ($this->total_amount * $this->advance_percentage) / 100;
        }
        return 0;
    }

    public function getRemainingAmount()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function isOverdue()
    {
        return $this->payment_status !== 'paid' && 
               $this->due_date && 
               Carbon::parse($this->due_date)->isPast();
    }

    public function canConvertToFinalInvoice()
    {
        return $this->type === 'proforma' && 
               $this->status === 'confirmed' && 
               !$this->childInvoices()->where('type', 'final')->exists();
    }

    public function convertToFinalInvoice($deliveryId = null, $userId = null)
    {
        if (!$this->canConvertToFinalInvoice()) {
            throw new \Exception('Cannot convert this proforma invoice to final invoice');
        }

        $finalInvoice = $this->replicate([
            'invoice_number',
            'created_at',
            'updated_at'
        ]);

        $finalInvoice->type = 'final';
        $finalInvoice->is_proforma = false;
        $finalInvoice->parent_invoice_id = $this->id;
        $finalInvoice->delivery_id = $deliveryId;
        $finalInvoice->status = 'sent';
        $finalInvoice->invoice_date = now();
        $finalInvoice->due_date = now()->addDays(30);
        $finalInvoice->created_by = auth()->id() ?? null;

        // Calculate remaining amount based on payment terms and paid amount
        $remainingAmount = $this->getRemainingAmount();
        
        if ($this->payment_terms === 'advance_50' && $this->paid_amount > 0) {
            // If 50% advance was paid, final invoice is for remaining 50%
            $finalInvoice->total_amount = $remainingAmount;
            $finalInvoice->sub_total = $remainingAmount;
            $finalInvoice->payment_status = 'pending';
            $finalInvoice->paid_amount = 0;
        } elseif ($this->payment_terms === 'advance_100' && $this->paid_amount >= $this->total_amount) {
            // If full payment was made on proforma, final invoice is for record keeping (0 amount)
            $finalInvoice->total_amount = 0;
            $finalInvoice->sub_total = 0;
            $finalInvoice->payment_status = 'paid';
            $finalInvoice->paid_amount = 0;
            $finalInvoice->paid_at = now();
        } elseif ($this->payment_terms === 'on_delivery') {
            // Full amount due on delivery
            $finalInvoice->total_amount = $this->total_amount;
            $finalInvoice->payment_status = 'pending';
            $finalInvoice->paid_amount = 0;
        } else {
            // Custom terms or other scenarios
            $finalInvoice->total_amount = $remainingAmount;
            $finalInvoice->payment_status = $remainingAmount > 0 ? 'pending' : 'paid';
            $finalInvoice->paid_amount = 0;
            if ($remainingAmount <= 0) {
                $finalInvoice->paid_at = now();
            }
        }

        $finalInvoice->save();

        // Copy items with potentially adjusted amounts
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $finalInvoice->id;
            
            // If this is a partial amount final invoice, adjust line totals proportionally
            if ($finalInvoice->total_amount != $this->total_amount && $this->total_amount > 0) {
                $ratio = $finalInvoice->total_amount / $this->total_amount;
                $newItem->line_total = $item->line_total * $ratio;
                $newItem->unit_price = $item->unit_price * $ratio;
            }
            
            $newItem->save();
        }

        // Recalculate totals to ensure accuracy
        $finalInvoice->calculateTotals();

        // Update proforma invoice status
        $this->update([
            'status' => 'completed',
            'updated_by' => auth()->id() ?? null
        ]);

        return $finalInvoice;
    }

    /**
     * Attribute Accessors
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2);
    }

    public function getFormattedPaidAmountAttribute()
    {
        return number_format($this->paid_amount, 2);
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return number_format($this->getRemainingAmount(), 2);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'badge bg-secondary',
            'sent' => 'badge bg-info',
            'confirmed' => 'badge bg-primary',
            'in_production' => 'badge bg-warning',
            'ready_to_ship' => 'badge bg-success',
            'shipped' => 'badge bg-success',
            'delivered' => 'badge bg-success',
            'completed' => 'badge bg-success',
            'cancelled' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'badge bg-warning',
            'partial' => 'badge bg-info',
            'paid' => 'badge bg-success',
            'overdue' => 'badge bg-danger',
            'cancelled' => 'badge bg-secondary',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Handle automatic workflow progression
     */
    public function handleWorkflowAutomation()
    {
        Log::info("Starting workflow automation for invoice {$this->id}: type={$this->type}, status={$this->status}, paid_amount={$this->paid_amount}, payment_terms={$this->payment_terms}");

        // Auto-confirm proforma invoice when payment is received
        if ($this->type === 'proforma' && 
            $this->status === 'sent' && 
            $this->paid_amount > 0) {
            
            Log::info("Auto-confirming proforma invoice {$this->id}");
            $this->update(['status' => 'confirmed']);
        }

        // Auto-create order when proforma invoice meets payment requirements
        if ($this->type === 'proforma' && 
            $this->status === 'confirmed' && 
            $this->shouldCreateOrder() && 
            !$this->order_id) {
            
            Log::info("Creating order for invoice {$this->id} - shouldCreateOrder: true, order_id: {$this->order_id}");
            $this->autoCreateOrder();
        } else {
            Log::info("Not creating order for invoice {$this->id} - type: {$this->type}, status: {$this->status}, shouldCreateOrder: " . ($this->shouldCreateOrder() ? 'true' : 'false') . ", existing_order_id: {$this->order_id}");
        }

        // Auto-update invoice status based on order status
        if ($this->order_id && $this->order) {
            $this->syncWithOrderStatus();
        }
    }

    /**
     * Determine if order should be created based on payment terms and amount paid
     */
    public function shouldCreateOrder(): bool
    {
        // Full payment always triggers order creation
        if ($this->payment_status === 'paid') {
            return true;
        }
        
        // Check advance payment requirements
        if ($this->payment_terms === 'advance_50') {
            $requiredAmount = $this->total_amount * 0.5;
            return $this->paid_amount >= $requiredAmount;
        }
        
        if ($this->payment_terms === 'advance_100') {
            // 100% advance requires full payment
            return $this->payment_status === 'paid';
        }
        
        if ($this->payment_terms === 'custom' && $this->advance_percentage) {
            $requiredAmount = ($this->total_amount * $this->advance_percentage) / 100;
            return $this->paid_amount >= $requiredAmount;
        }
        
        // For 'on_delivery' and 'net_30' terms, don't auto-create orders
        // These should be created manually when ready to fulfill
        return false;
    }

    /**
     * Automatically create order from confirmed and paid proforma invoice
     */
    public function autoCreateOrder()
    {
        try {
            DB::beginTransaction();

            // Create order from invoice
            $order = \App\Models\Order::create([
                'user_id' => $this->getCustomerUserId(),
                'order_number' => 'ORD-' . str_pad(\App\Models\Order::max('id') + 1, 6, '0', STR_PAD_LEFT),
                'total_amount' => $this->total_amount,
                'status' => 'processing', // Changed from 'confirmed' to match orders table enum
                'shipping_address' => $this->shipping_address ?: $this->billing_address,
                'shipping_city' => $this->extractCityFromAddress(),
                'shipping_state' => $this->extractStateFromAddress(),
                'shipping_zipcode' => $this->extractZipcodeFromAddress(),
                'shipping_phone' => $this->extractPhoneFromAddress(),
                'notes' => 'Auto-created from proforma invoice ' . $this->invoice_number
            ]);

            // Create order items from invoice items
            foreach ($this->items as $invoiceItem) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $invoiceItem->product_id,
                    'quantity' => $invoiceItem->quantity,
                    'price' => $invoiceItem->product ? $invoiceItem->product->price_aed : $invoiceItem->unit_price,
                    'variation' => $invoiceItem->specifications
                ]);
            }

            // Link invoice to order
            $this->update(['order_id' => $order->id, 'status' => 'in_production']);

            DB::commit();

            Log::info("Auto-created order {$order->id} from proforma invoice {$this->id}");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to auto-create order from invoice: ' . $e->getMessage());
        }
    }

    /**
     * Sync invoice status with order status
     */
    public function syncWithOrderStatus()
    {
        $statusMapping = [
            'pending' => 'confirmed',
            'processing' => 'in_production',
            'shipped' => 'shipped',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled'
        ];

        $newStatus = $statusMapping[$this->order->status] ?? $this->status;
        
        if ($newStatus !== $this->status) {
            $this->update(['status' => $newStatus]);
            Log::info("Auto-updated invoice {$this->id} status to {$newStatus} based on order status");
        }
    }

    /**
     * Get customer user ID (create if doesn't exist)
     */
    private function getCustomerUserId()
    {
        // Try to find existing customer by name
        $customer = \App\Models\Customer::where('name', $this->customer_name)->first();
        
        if ($customer && $customer->user_id) {
            return $customer->user_id;
        }

        // Create a basic user for the customer if none exists
        $user = \App\Models\User::firstOrCreate([
            'email' => strtolower(str_replace(' ', '.', $this->customer_name)) . '@customer.local'
        ], [
            'name' => $this->customer_name,
            'password' => bcrypt('temporary123'),
            'email_verified_at' => now()
        ]);

        return $user->id;
    }

    /**
     * Extract city from address
     */
    private function extractCityFromAddress()
    {
        $address = $this->shipping_address ?: $this->billing_address;
        $lines = explode("\n", $address);
        return count($lines) > 1 ? trim($lines[1]) : 'N/A';
    }

    /**
     * Extract state from address
     */
    private function extractStateFromAddress()
    {
        $address = $this->shipping_address ?: $this->billing_address;
        $lines = explode("\n", $address);
        return count($lines) > 2 ? trim($lines[2]) : 'N/A';
    }

    /**
     * Extract zipcode from address
     */
    private function extractZipcodeFromAddress()
    {
        $address = $this->shipping_address ?: $this->billing_address;
        preg_match('/\b\d{5}(?:-\d{4})?\b/', $address, $matches);
        return $matches[0] ?? 'N/A';
    }

    /**
     * Extract phone from address or notes
     */
    private function extractPhoneFromAddress()
    {
        $text = ($this->shipping_address ?: $this->billing_address) . ' ' . $this->notes;
        preg_match('/\b\d{3}[-.]?\d{3}[-.]?\d{4}\b/', $text, $matches);
        return $matches[0] ?? 'N/A';
    }
}
