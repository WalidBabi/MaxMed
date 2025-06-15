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

        Log::info("Converting proforma invoice {$this->id} to final invoice. Payment terms: {$this->payment_terms}, Paid amount: {$this->paid_amount}, Total: {$this->total_amount}");

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
        $finalInvoice->due_date = $this->calculateFinalInvoiceDueDate();
        $finalInvoice->created_by = auth()->id() ?? $userId ?? null;

        // Calculate remaining amount based on payment terms and paid amount
        $remainingAmount = $this->getRemainingAmount();
        
        Log::info("Remaining amount calculation: {$remainingAmount} (Total: {$this->total_amount}, Paid: {$this->paid_amount})");

        switch ($this->payment_terms) {
            case 'advance_50':
                if ($this->paid_amount >= ($this->total_amount * 0.5)) {
                    // 50% advance was paid, final invoice is for remaining 50%
                    $finalInvoice->total_amount = $remainingAmount;
                    $finalInvoice->sub_total = $remainingAmount;
                    $finalInvoice->payment_status = $remainingAmount > 0 ? 'pending' : 'paid';
                    $finalInvoice->paid_amount = 0;
                    $finalInvoice->description = 'Final Invoice - Remaining Balance (50% advance payment received)';
                    
                    if ($remainingAmount <= 0) {
                        $finalInvoice->paid_at = now();
                    }
                } else {
                    throw new \Exception('50% advance payment not received. Cannot convert to final invoice.');
                }
                break;

            case 'advance_100':
                if ($this->paid_amount >= $this->total_amount) {
                    // Full payment was made on proforma, final invoice is for record keeping
                    $finalInvoice->total_amount = 0;
                    $finalInvoice->sub_total = 0;
                    $finalInvoice->payment_status = 'paid';
                    $finalInvoice->paid_amount = 0;
                    $finalInvoice->paid_at = now();
                    $finalInvoice->description = 'Final Invoice - Delivery Completed (Full payment received on proforma)';
                } else {
                    throw new \Exception('Full advance payment not received. Cannot convert to final invoice.');
                }
                break;

            case 'on_delivery':
                // Full amount due on delivery
                $finalInvoice->total_amount = $this->total_amount;
                $finalInvoice->sub_total = $this->total_amount;
                $finalInvoice->payment_status = 'pending';
                $finalInvoice->paid_amount = 0;
                $finalInvoice->due_date = now(); // Payment due immediately upon delivery
                $finalInvoice->description = 'Final Invoice - Payment Due on Delivery';
                break;

            case 'net_30':
                // Full amount due within 30 days
                $finalInvoice->total_amount = $this->total_amount;
                $finalInvoice->sub_total = $this->total_amount;
                $finalInvoice->payment_status = 'pending';
                $finalInvoice->paid_amount = 0;
                $finalInvoice->due_date = now()->addDays(30);
                $finalInvoice->description = 'Final Invoice - Payment Due in 30 Days';
                break;

            case 'custom':
                $advancePercentage = $this->advance_percentage ?? 0;
                if ($advancePercentage > 0) {
                    $requiredAdvance = $this->total_amount * ($advancePercentage / 100);
                    if ($this->paid_amount >= $requiredAdvance) {
                        // Custom advance was paid, final invoice is for remaining amount
                        $finalInvoice->total_amount = $remainingAmount;
                        $finalInvoice->sub_total = $remainingAmount;
                        $finalInvoice->payment_status = $remainingAmount > 0 ? 'pending' : 'paid';
                        $finalInvoice->paid_amount = 0;
                        $finalInvoice->description = "Final Invoice - Remaining Balance ({$advancePercentage}% advance payment received)";
                        
                        if ($remainingAmount <= 0) {
                            $finalInvoice->paid_at = now();
                        }
                    } else {
                        throw new \Exception("{$advancePercentage}% advance payment not received. Cannot convert to final invoice.");
                    }
                } else {
                    // No advance required, full amount due
                    $finalInvoice->total_amount = $this->total_amount;
                    $finalInvoice->sub_total = $this->total_amount;
                    $finalInvoice->payment_status = 'pending';
                    $finalInvoice->paid_amount = 0;
                    $finalInvoice->description = 'Final Invoice - Custom Payment Terms';
                }
                break;

            default:
                // Default case - remaining amount or full amount if no payment
                $finalInvoice->total_amount = $remainingAmount > 0 ? $remainingAmount : $this->total_amount;
                $finalInvoice->sub_total = $finalInvoice->total_amount;
                $finalInvoice->payment_status = $finalInvoice->total_amount > 0 ? 'pending' : 'paid';
                $finalInvoice->paid_amount = 0;
                $finalInvoice->description = 'Final Invoice';
                
                if ($finalInvoice->total_amount <= 0) {
                    $finalInvoice->paid_at = now();
                }
                break;
        }

        $finalInvoice->save();

        Log::info("Created final invoice {$finalInvoice->id} with amount {$finalInvoice->total_amount} and status {$finalInvoice->payment_status}");

        // Copy items with potentially adjusted amounts
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $finalInvoice->id;
            
            // If this is a partial amount final invoice, adjust line totals proportionally
            if ($finalInvoice->total_amount != $this->total_amount && $this->total_amount > 0) {
                $ratio = $finalInvoice->total_amount / $this->total_amount;
                $newItem->line_total = $item->line_total * $ratio;
                $newItem->unit_price = $item->unit_price * $ratio;
                
                Log::info("Adjusted invoice item {$item->id} by ratio {$ratio}: new line total {$newItem->line_total}");
            }
            
            $newItem->save();
        }

        // Recalculate totals to ensure accuracy
        $finalInvoice->calculateTotals();

        // Update proforma invoice status
        $this->update([
            'status' => 'completed',
            'updated_by' => auth()->id() ?? $userId ?? null
        ]);

        Log::info("Successfully converted proforma invoice {$this->id} to final invoice {$finalInvoice->id}");

        return $finalInvoice;
    }

    /**
     * Calculate due date for final invoice based on payment terms
     */
    private function calculateFinalInvoiceDueDate()
    {
        switch ($this->payment_terms) {
            case 'on_delivery':
                return now(); // Due immediately
            case 'net_30':
                return now()->addDays(30);
            case 'advance_50':
            case 'advance_100':
                return now()->addDays(15); // Give reasonable time for remaining balance
            case 'custom':
                return now()->addDays(15); // Default to 15 days for custom terms
            default:
                return now()->addDays(30); // Default to 30 days
        }
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

        // Auto-advance draft invoices to 'sent' status for payment terms that allow order creation
        $this->autoAdvanceStatusForOrderCreation();

        // Auto-confirm proforma invoice when payment is received
        if ($this->type === 'proforma' && 
            $this->status === 'sent' && 
            $this->paid_amount > 0) {
            
            Log::info("Auto-confirming proforma invoice {$this->id}");
            $this->update(['status' => 'confirmed']);
        }

        // Auto-create order when proforma invoice meets payment requirements
        // For certain payment terms (on_delivery, net_30), allow order creation even from 'sent' status
        $canCreateFromSentStatus = in_array($this->payment_terms, ['on_delivery', 'net_30']);
        $statusAllowsOrderCreation = ($this->status === 'confirmed') || 
                                   ($canCreateFromSentStatus && $this->status === 'sent');
        
        if ($this->type === 'proforma' && 
            $statusAllowsOrderCreation && 
            $this->shouldCreateOrder() && 
            !$this->order_id) {
            
            Log::info("Creating order for invoice {$this->id} - shouldCreateOrder: true, order_id: {$this->order_id}, status: {$this->status}, payment_terms: {$this->payment_terms}");
            $this->autoCreateOrder();
        } else {
            Log::info("Not creating order for invoice {$this->id} - type: {$this->type}, status: {$this->status}, statusAllowsOrderCreation: " . ($statusAllowsOrderCreation ? 'true' : 'false') . ", shouldCreateOrder: " . ($this->shouldCreateOrder() ? 'true' : 'false') . ", existing_order_id: {$this->order_id}");
        }

        // Auto-update invoice status based on order status
        if ($this->order_id && $this->order) {
            $this->syncWithOrderStatus();
        }
    }

    /**
     * Auto-advance draft invoices to 'sent' status for payment terms that allow order creation
     */
    private function autoAdvanceStatusForOrderCreation()
    {
        // Only apply to proforma invoices in draft status
        if ($this->type !== 'proforma' || $this->status !== 'draft') {
            return;
        }

        // Payment terms that can trigger order creation without payment
        $autoAdvancePaymentTerms = ['on_delivery', 'net_30'];
        
        if (in_array($this->payment_terms, $autoAdvancePaymentTerms)) {
            Log::info("Auto-advancing invoice {$this->id} from 'draft' to 'sent' status due to payment terms: {$this->payment_terms}");
            
            // Update status to 'sent' and set sent_at timestamp
            $this->update([
                'status' => 'sent',
                'sent_at' => now(),
                'updated_by' => auth()->id() ?? null
            ]);
        }
    }

    /**
     * Determine if order should be created based on payment terms and amount paid
     */
    public function shouldCreateOrder(): bool
    {
        // Prevent duplicate orders
        if ($this->order_id) {
            return false;
        }

        // Only create orders for proforma invoices
        if ($this->type !== 'proforma') {
            return false;
        }

        // Invoice must be at least 'sent' to create orders
        if ($this->status === 'draft') {
            return false;
        }

        // Full payment always triggers order creation
        if ($this->payment_status === 'paid') {
            Log::info("Order creation approved for invoice {$this->id}: Full payment received");
            return true;
        }
        
        // Handle each payment term case
        switch ($this->payment_terms) {
            case 'advance_50':
                $requiredAmount = $this->total_amount * 0.5;
                $canCreate = $this->paid_amount >= $requiredAmount;
                Log::info("Order creation for invoice {$this->id} with 50% advance: required={$requiredAmount}, paid={$this->paid_amount}, approved=" . ($canCreate ? 'yes' : 'no'));
                return $canCreate;
                
            case 'advance_100':
                // 100% advance requires full payment
                $canCreate = $this->payment_status === 'paid';
                Log::info("Order creation for invoice {$this->id} with 100% advance: payment_status={$this->payment_status}, approved=" . ($canCreate ? 'yes' : 'no'));
                return $canCreate;
                
            case 'custom':
                if ($this->advance_percentage && $this->advance_percentage > 0) {
                    $requiredAmount = ($this->total_amount * $this->advance_percentage) / 100;
                    $canCreate = $this->paid_amount >= $requiredAmount;
                    Log::info("Order creation for invoice {$this->id} with custom {$this->advance_percentage}% advance: required={$requiredAmount}, paid={$this->paid_amount}, approved=" . ($canCreate ? 'yes' : 'no'));
                    return $canCreate;
                }
                // If no advance percentage set, treat as manual approval needed
                return false;
                
            case 'on_delivery':
                // For Payment on Delivery, create order when invoice is confirmed
                // This allows manufacturing/preparation to start while payment is collected on delivery
                $canCreate = in_array($this->status, ['confirmed', 'sent']) && $this->hasValidDeliveryAddress();
                Log::info("Order creation for invoice {$this->id} with payment on delivery: status={$this->status}, has_delivery_address=" . ($this->hasValidDeliveryAddress() ? 'yes' : 'no') . ", approved=" . ($canCreate ? 'yes' : 'no'));
                return $canCreate;
                
            case 'net_30':
                // For Net 30 terms, check customer trust level
                $trustLevel = $this->getCustomerTrustLevel();
                if ($trustLevel === 'high') {
                    // High trust customers can get orders created on confirmation
                    $canCreate = in_array($this->status, ['confirmed', 'sent']);
                    Log::info("Order creation for invoice {$this->id} with Net 30 (high trust customer): status={$this->status}, approved=" . ($canCreate ? 'yes' : 'no'));
                    return $canCreate;
                } elseif ($trustLevel === 'medium') {
                    // Medium trust customers need at least 25% payment
                    $requiredAmount = $this->total_amount * 0.25;
                    $canCreate = $this->paid_amount >= $requiredAmount;
                    Log::info("Order creation for invoice {$this->id} with Net 30 (medium trust customer): required={$requiredAmount}, paid={$this->paid_amount}, approved=" . ($canCreate ? 'yes' : 'no'));
                    return $canCreate;
                } else {
                    // Low trust or new customers need full payment for Net 30
                    $canCreate = $this->payment_status === 'paid';
                    Log::info("Order creation for invoice {$this->id} with Net 30 (low trust customer): payment_status={$this->payment_status}, approved=" . ($canCreate ? 'yes' : 'no'));
                    return $canCreate;
                }
                
            default:
                Log::warning("Unknown payment terms '{$this->payment_terms}' for invoice {$this->id}");
                return false;
        }
    }

    /**
     * Check if invoice has valid delivery address for POD orders
     */
    private function hasValidDeliveryAddress(): bool
    {
        $address = $this->shipping_address ?: $this->billing_address;
        return !empty($address) && strlen(trim($address)) > 10; // Basic validation
    }

    /**
     * Get customer trust level based on payment history and relationship
     */
    public function getCustomerTrustLevel(): string
    {
        // Check if customer has previous successful orders
        $customerInvoices = Invoice::where('customer_name', $this->customer_name)
            ->where('payment_status', 'paid')
            ->where('id', '!=', $this->id)
            ->count();
            
        // Check total value of previous successful transactions
        $totalPaidValue = Invoice::where('customer_name', $this->customer_name)
            ->where('payment_status', 'paid')
            ->where('id', '!=', $this->id)
            ->sum('total_amount');
            
        // Check for any overdue invoices
        $overdueInvoices = Invoice::where('customer_name', $this->customer_name)
            ->where('payment_status', 'overdue')
            ->count();
            
        // Calculate trust level
        if ($overdueInvoices > 0) {
            return 'low'; // Customer has overdue invoices
        }
        
        if ($customerInvoices >= 5 && $totalPaidValue >= 50000) {
            return 'high'; // Long-term customer with high value
        }
        
        if ($customerInvoices >= 2 && $totalPaidValue >= 10000) {
            return 'medium'; // Established customer with moderate value
        }
        
        return 'low'; // New or low-value customer
    }

    /**
     * Enhanced order creation with additional business logic
     */
    public function autoCreateOrder()
    {
        try {
            DB::beginTransaction();
            
            // Get or create customer record
            $customerId = $this->getOrCreateCustomerId();
            Log::info("Got customer_id {$customerId} for invoice {$this->id}");
            
            // Get customer trust level for additional order settings
            $trustLevel = $this->getCustomerTrustLevel();
            
            // Determine initial order status based on payment terms and trust
            $orderStatus = $this->determineInitialOrderStatus($trustLevel);
            
            // Prepare order data
            $orderData = [
                'user_id' => $this->getCustomerUserId(),
                'customer_id' => $customerId,
                'order_number' => 'ORD-' . str_pad(\App\Models\Order::max('id') + 1, 6, '0', STR_PAD_LEFT),
                'total_amount' => $this->total_amount,
                'status' => $orderStatus,
                'shipping_address' => $this->shipping_address ?: $this->billing_address,
                'shipping_city' => $this->extractCityFromAddress(),
                'shipping_state' => $this->extractStateFromAddress(),
                'shipping_zipcode' => $this->extractZipcodeFromAddress(),
                'shipping_phone' => $this->extractPhoneFromAddress(),
                'notes' => $this->generateOrderNotes($trustLevel)
            ];
            
            Log::info("Creating order with data: " . json_encode($orderData));
            
            // Create order from invoice
            $order = \App\Models\Order::create($orderData);

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

            // Link invoice to order and update status
            $newInvoiceStatus = $this->determineNewInvoiceStatus($orderStatus);
            $this->update([
                'order_id' => $order->id, 
                'status' => $newInvoiceStatus
            ]);

            DB::commit();

            Log::info("Auto-created order {$order->id} from proforma invoice {$this->id} with status '{$orderStatus}' for {$trustLevel} trust customer");
            
            // Send notifications based on payment terms
            $this->sendOrderCreationNotifications($order, $trustLevel);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to auto-create order from invoice: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Get or create customer record from invoice information
     */
    private function getOrCreateCustomerId(): int
    {
        Log::info("Starting getOrCreateCustomerId for invoice {$this->id} with customer_name: '{$this->customer_name}'");
        
        // Try to find existing customer by name first
        $customer = \App\Models\Customer::where('name', $this->customer_name)->first();
        
        if ($customer) {
            Log::info("Found existing customer {$customer->id} for invoice {$this->id}");
            return $customer->id;
        }
        
        // Extract email from billing address if available
        $customerEmail = $this->extractEmailFromAddress();
        Log::info("Extracted email from address: " . ($customerEmail ?: 'none'));
        
        // Try to find customer by email if we extracted one
        if ($customerEmail) {
            $customer = \App\Models\Customer::where('email', $customerEmail)->first();
            if ($customer) {
                Log::info("Found existing customer {$customer->id} by email for invoice {$this->id}");
                return $customer->id;
            }
        }
        
        // Create new customer
        Log::info("Creating new customer for invoice {$this->id}");
        try {
            $customer = \App\Models\Customer::create([
                'name' => $this->customer_name,
                'email' => $customerEmail,
                'billing_street' => $this->billing_address,
                'shipping_street' => $this->shipping_address ?: $this->billing_address,
                'billing_city' => $this->extractCityFromAddress(),
                'billing_state' => $this->extractStateFromAddress(),
                'billing_zip' => $this->extractZipcodeFromAddress(),
                'shipping_city' => $this->extractCityFromAddress(),
                'shipping_state' => $this->extractStateFromAddress(),
                'shipping_zip' => $this->extractZipcodeFromAddress(),
                'phone' => $this->extractPhoneFromAddress(),
                'notes' => "Auto-created from invoice {$this->invoice_number}",
                'is_active' => true
            ]);
            
            Log::info("Successfully created new customer {$customer->id} for invoice {$this->id}");
            return $customer->id;
        } catch (\Exception $e) {
            Log::error("Failed to create customer for invoice {$this->id}: " . $e->getMessage());
            Log::error("Customer data: " . json_encode([
                'name' => $this->customer_name,
                'email' => $customerEmail,
                'billing_street' => $this->billing_address,
                'shipping_street' => $this->shipping_address ?: $this->billing_address,
            ]));
            throw $e;
        }
    }

    /**
     * Extract email from billing/shipping address if present
     */
    private function extractEmailFromAddress(): ?string
    {
        $address = $this->billing_address . ' ' . ($this->shipping_address ?: '');
        
        // Simple email extraction using regex
        if (preg_match('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $address, $matches)) {
            return $matches[0];
        }
        
        return null;
    }

    /**
     * Determine initial order status based on payment terms and customer trust
     */
    private function determineInitialOrderStatus(string $trustLevel): string
    {
        switch ($this->payment_terms) {
            case 'advance_50':
            case 'advance_100':
            case 'custom':
                // Advance payments allow immediate processing
                return 'processing';
                
            case 'on_delivery':
                // POD orders start processing but with special handling
                return 'processing';
                
            case 'net_30':
                // Net 30 depends on trust level
                if ($trustLevel === 'high') {
                    return 'processing';
                } else {
                    return 'pending'; // Pending until payment confirmation
                }
                
            default:
                return 'pending';
        }
    }

    /**
     * Determine new invoice status after order creation
     */
    private function determineNewInvoiceStatus(string $orderStatus): string
    {
        if ($orderStatus === 'processing') {
            return 'in_production';
        }
        
        return 'confirmed'; // Order created but not yet processing
    }

    /**
     * Generate order notes based on payment terms and trust level
     */
    private function generateOrderNotes(string $trustLevel): string
    {
        $notes = "Auto-created from proforma invoice {$this->invoice_number}";
        
        switch ($this->payment_terms) {
            case 'on_delivery':
                $notes .= " | Payment on Delivery - Collect payment before releasing goods";
                break;
                
            case 'net_30':
                if ($trustLevel === 'low') {
                    $notes .= " | Net 30 terms - New customer, monitor payment closely";
                } else {
                    $notes .= " | Net 30 terms - Established customer";
                }
                break;
                
            case 'advance_50':
                $notes .= " | 50% advance payment received";
                break;
                
            case 'advance_100':
                $notes .= " | Full advance payment received";
                break;
                
            case 'custom':
                $notes .= " | Custom advance payment ({$this->advance_percentage}%) received";
                break;
        }
        
        return $notes;
    }

    /**
     * Send notifications when order is created
     */
    private function sendOrderCreationNotifications(\App\Models\Order $order, string $trustLevel): void
    {
        // Log the order creation
        Log::info("Order {$order->order_number} created for invoice {$this->invoice_number} - Payment terms: {$this->payment_terms}, Trust level: {$trustLevel}");
        
        // Here you could add email notifications, SMS, webhook calls, etc.
        // For example:
        // - Notify production team for manufacturing orders
        // - Notify finance team for payment collection
        // - Notify customer with order confirmation
        
        // Example notification logic:
        if ($this->payment_terms === 'on_delivery') {
            // Notify delivery team about payment collection requirement
            Log::info("NOTIFICATION: Order {$order->order_number} requires payment collection on delivery");
        }
        
        if ($trustLevel === 'low' && $this->payment_terms === 'net_30') {
            // Notify finance team about payment monitoring
            Log::info("NOTIFICATION: Order {$order->order_number} from new customer with Net 30 terms - monitor payment");
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
