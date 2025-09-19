<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use App\Traits\DubaiDateFormat;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderNotification;
use App\Notifications\SupplierOrderNotification;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewOrderSupplierNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;
    use DubaiDateFormat;
    
    protected $table = 'orders';

    protected $observables = [
        'creating',
        'created',
        'updating',
        'updated'
    ];

    protected $fillable = [
        'user_id',
        'customer_id',
        'proforma_invoice_id',
        'order_number',
        'total_amount',
        'currency',
        'shipping_rate',
        'vat_rate',
        'vat_amount',
        'customs_clearance_fee',
        'status',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'shipping_phone',
        'notes'
    ];

    // Status constants - Comprehensive order lifecycle for supplier workflow and customer tracking
    const STATUS_PENDING = 'pending';           // Initial state
    const STATUS_PROCESSING = 'processing';     // Order is being processed
    const STATUS_SHIPPED = 'shipped';          // Order has been shipped
    const STATUS_CANCELLED = 'cancelled';      // Order cancelled
    
    // Additional status constants for quotation workflow
    const STATUS_AWAITING_QUOTATIONS = 'awaiting_quotations';  // Waiting for supplier quotations
    const STATUS_QUOTATIONS_RECEIVED = 'quotations_received';  // Quotations received, pending approval
    const STATUS_APPROVED = 'approved';        // Order approved after quotation review
    const STATUS_DELIVERED = 'delivered';      // Order delivered to customer
    const STATUS_COMPLETED = 'completed';      // Order completed (final state)
    
    // Quotation status constants
    const QUOTATION_STATUS_PENDING = 'pending';
    const QUOTATION_STATUS_APPROVED = 'approved';
    const QUOTATION_STATUS_REJECTED = 'rejected';
    const QUOTATION_STATUS_PARTIAL = 'partial';
    const QUOTATION_STATUS_COMPLETE = 'complete';

    // Add after the status constants
    private const ALLOWED_STATUS_TRANSITIONS = [
        self::STATUS_PENDING => [
            self::STATUS_AWAITING_QUOTATIONS,   // Move to quotation workflow
            self::STATUS_PROCESSING,            // Start processing (for non-quotation orders)
            self::STATUS_CANCELLED
        ],
        self::STATUS_AWAITING_QUOTATIONS => [
            self::STATUS_QUOTATIONS_RECEIVED,   // Quotations received
            self::STATUS_CANCELLED
        ],
        self::STATUS_QUOTATIONS_RECEIVED => [
            self::STATUS_APPROVED,              // Order approved
            self::STATUS_CANCELLED
        ],
        self::STATUS_APPROVED => [
            self::STATUS_PROCESSING,            // Start processing approved order
            self::STATUS_CANCELLED
        ],
        self::STATUS_PROCESSING => [
            self::STATUS_SHIPPED,               // Order shipped
            self::STATUS_CANCELLED
        ],
        self::STATUS_SHIPPED => [
            self::STATUS_DELIVERED,             // Order delivered
            self::STATUS_CANCELLED
        ],
        self::STATUS_DELIVERED => [
            self::STATUS_COMPLETED,             // Order completed
            self::STATUS_CANCELLED
        ],
        self::STATUS_COMPLETED => [
            self::STATUS_COMPLETED              // Final state
        ],
        self::STATUS_CANCELLED => [
            self::STATUS_CANCELLED              // Final state
        ]
    ];

    protected $casts = [];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
            
            // All orders start as pending
            $order->status = self::STATUS_PENDING;
        });

        static::updating(function ($order) {
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;
                
                if (!self::isValidStatusTransition($oldStatus, $newStatus)) {
                    throw new \InvalidArgumentException(
                        "Invalid status transition from {$oldStatus} to {$newStatus}"
                    );
                }
            }
        });
        
        // Auto-create delivery when order is created
        static::created(function ($order) {
            $order->autoCreateDelivery();
        });
        
        // Automatic workflow triggers
        static::updated(function ($order) {
            $order->handleWorkflowAutomation();
            $order->sendStatusChangeNotification();
        });
    }

    /**
     * Generate a unique order number in format ORD-000001
     */
    public static function generateOrderNumber(): string
    {
        $lastOrder = static::where('order_number', 'like', 'ORD-%')
            ->orderByRaw('CAST(SUBSTRING(order_number, 5) AS UNSIGNED) DESC')
            ->first();
        
        $nextNumber = 1;
        if ($lastOrder && $lastOrder->order_number) {
            // Extract the number part from the order number
            $numberPart = substr($lastOrder->order_number, 4); // Remove 'ORD-' prefix
            if (is_numeric($numberPart)) {
                $nextNumber = intval($numberPart) + 1;
            }
        }
        
        $orderNumber = 'ORD-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        // Safety check to ensure uniqueness
        $counter = 0;
        while (static::where('order_number', $orderNumber)->exists() && $counter < 1000) {
            $nextNumber++;
            $orderNumber = 'ORD-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $orderNumber;
    }

    /**
     * Handle automatic workflow progression
     */
    public function handleWorkflowAutomation()
    {
        // Auto-create delivery if it doesn't exist (for orders created before auto-creation was implemented)
        if (!$this->hasDelivery()) {
            $this->autoCreateDelivery();
        }
        
        // Auto-update delivery status based on order status
        if ($this->hasDelivery()) {
            $this->syncDeliveryStatus();
        }
    }

    /**
     * Automatically create delivery when order is created
     */
    public function autoCreateDelivery()
    {
        try {
            // Check if delivery already exists
            if ($this->hasDelivery()) {
                Log::info("Delivery already exists for order {$this->id}");
                return;
            }

            // Create delivery for all new orders, regardless of initial status
            // The delivery will track the fulfillment process from creation to completion
            
            // For orders requiring quotations, create delivery but keep it in pending until approved
            $deliveryStatus = 'pending';
            
            // Determine initial delivery status based on order requirements
            if ($this->requires_quotation) {
                if ($this->quotation_status === self::QUOTATION_STATUS_APPROVED) {
                    $deliveryStatus = 'processing';
                } else {
                    $deliveryStatus = 'pending'; // Wait for quotation approval
                }
            } else {
                // For orders not requiring quotations, delivery can start processing
                $deliveryStatus = ($this->status === self::STATUS_PROCESSING) ? 'processing' : 'pending';
            }

            $delivery = Delivery::create([
                'order_id' => $this->id,
                'status' => $deliveryStatus,
                'carrier' => 'TBD',
                'tracking_number' => $this->generateTrackingNumber(),
                'shipping_address' => $this->getFullShippingAddress(),
                'billing_address' => $this->getFullShippingAddress(),
                'shipping_cost' => 0,
                'total_weight' => $this->calculateTotalWeight(),
                'notes' => "Auto-created delivery for order {$this->order_number}",
            ]);

            Log::info("Auto-created delivery {$delivery->id} for order {$this->id} (Order: {$this->order_number}) with status '{$deliveryStatus}'");

            // Notify supplier about new order
            $this->notifySupplierNewOrder($delivery);

        } catch (\Exception $e) {
            Log::error("Failed to auto-create delivery for order {$this->id}: " . $e->getMessage());
        }
    }

    /**
     * Generate a unique tracking number
     */
    private function generateTrackingNumber(): string
    {
        $prefix = 'TRK';
        $timestamp = now()->format('ymd'); // YYMMDD
        $random = strtoupper(substr(uniqid(), -6)); // Last 6 chars of uniqid
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Notify supplier about new order
     */
    private function notifySupplierNewOrder($delivery)
    {
        try {
            // TODO: Implement supplier notification system
            // For now, we'll just log it
            Log::info("New order {$this->order_number} ready for supplier processing. Delivery ID: {$delivery->id}");
            
            // You can add email notifications to suppliers here
            // Mail::to('supplier@example.com')->send(new NewOrderNotification($this, $delivery));
            
        } catch (\Exception $e) {
            Log::error('Failed to notify supplier: ' . $e->getMessage());
        }
    }

    /**
     * Sync delivery status with order status
     */
    public function syncDeliveryStatus()
    {
        $delivery = $this->delivery;
        
        if (!$delivery) {
            Log::warning("Cannot sync delivery status - no delivery found for order {$this->id}");
            return;
        }
        
        $currentDeliveryStatus = $delivery->status;
        $newDeliveryStatus = $currentDeliveryStatus;
        
        // Enhanced status mapping with more granular control
        switch ($this->status) {
            case self::STATUS_PENDING:
                // Keep delivery as pending
                if ($currentDeliveryStatus !== 'pending') {
                    $newDeliveryStatus = 'pending';
                }
                break;
                
            case self::STATUS_AWAITING_QUOTATIONS:
            case self::STATUS_QUOTATIONS_RECEIVED:
                // Keep delivery pending while waiting for quotations
                $newDeliveryStatus = 'pending';
                break;
                
            case self::STATUS_APPROVED:
            case self::STATUS_PROCESSING:
                // Move to processing when order is being processed
                if (in_array($currentDeliveryStatus, ['pending'])) {
                    $newDeliveryStatus = 'processing';
                }
                break;
                
            case self::STATUS_SHIPPED:
                // Order shipped - delivery should be in transit
                if (in_array($currentDeliveryStatus, ['pending', 'processing'])) {
                    $newDeliveryStatus = 'in_transit';
                }
                break;
                
            case self::STATUS_DELIVERED:
                // Order delivered - delivery should be delivered
                if ($currentDeliveryStatus !== 'delivered') {
                    $newDeliveryStatus = 'delivered';
                }
                break;
                
            case self::STATUS_COMPLETED:
                // Order completed - ensure delivery is delivered
                if ($currentDeliveryStatus !== 'delivered') {
                    $newDeliveryStatus = 'delivered';
                }
                break;
                
            case self::STATUS_CANCELLED:
                // Order cancelled - cancel delivery unless already delivered
                if (!in_array($currentDeliveryStatus, ['delivered', 'cancelled'])) {
                    $newDeliveryStatus = 'cancelled';
                }
                break;
        }
        
        // Update delivery status if changed
        if ($newDeliveryStatus !== $currentDeliveryStatus) {
            $updateData = ['status' => $newDeliveryStatus];
            
            // Set timestamps for specific status changes
            if ($newDeliveryStatus === 'in_transit' && !$delivery->shipped_at) {
                $updateData['shipped_at'] = now();
            }
            
            if ($newDeliveryStatus === 'delivered' && !$delivery->delivered_at) {
                $updateData['delivered_at'] = now();
            }
            
            $delivery->update($updateData);
            
            Log::info("Auto-updated delivery {$delivery->id} status from '{$currentDeliveryStatus}' to '{$newDeliveryStatus}' for order {$this->id} (status: {$this->status})");
        }
    }

    /**
     * Get full shipping address
     */
    private function getFullShippingAddress()
    {
        return implode("\n", array_filter([
            $this->shipping_address,
            $this->shipping_city,
            $this->shipping_state . ' ' . $this->shipping_zipcode,
            'Phone: ' . $this->shipping_phone
        ]));
    }

    /**
     * Calculate total weight of order items
     */
    private function calculateTotalWeight()
    {
        $totalWeight = 0;
        
        foreach ($this->items as $item) {
            if ($item->product && $item->product->weight) {
                $totalWeight += $item->product->weight * $item->quantity;
            }
        }
        
        return $totalWeight > 0 ? $totalWeight : 1.0; // Default 1kg if no weight data
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    /**
     * Get customer information - works for both new orders (with customer_id) and legacy orders
     */
    public function getCustomerInfo()
    {
        // If we have a direct customer relationship, use it
        if ($this->customer_id && $this->customer) {
            return $this->customer;
        }

        // For legacy orders, try to find customer by user_id
        if ($this->user_id) {
            $customer = \App\Models\Customer::where('user_id', $this->user_id)->first();
            if ($customer) {
                return $customer;
            }
        }

        // Return null if no customer found
        return null;
    }

    /**
     * Get customer name - works for both new and legacy orders
     */
    public function getCustomerName()
    {
        $customer = $this->getCustomerInfo();
        if ($customer) {
            return $customer->name;
        }

        // Fallback to user name
        return $this->user ? $this->user->name : 'N/A';
    }

    /**
     * Get customer email - works for both new and legacy orders
     */
    public function getCustomerEmail()
    {
        $customer = $this->getCustomerInfo();
        if ($customer) {
            return $customer->email ?: ($customer->user ? $customer->user->email : 'N/A');
        }

        // Fallback to user email
        return $this->user ? $this->user->email : 'N/A';
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class)->with('product');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the delivery associated with the order.
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    /**
     * Check if the order has a delivery.
     */
    public function hasDelivery(): bool
    {
        return $this->delivery()->exists();
    }

    /**
     * Get the purchase order associated with the order.
     */
    public function purchaseOrder()
    {
        return $this->hasOne(\App\Models\PurchaseOrder::class);
    }

    /**
     * Check if the order has a purchase order.
     */
    public function hasPurchaseOrder(): bool
    {
        return $this->purchaseOrder()->exists();
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Get the invoice associated with the order (proforma invoice that created this order).
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id', 'id');
    }

    /**
     * Get all invoices associated with the order
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id', 'id');
    }

    /**
     * Get the proforma invoice that created this order.
     */
    public function proformaInvoice()
    {
        return $this->belongsTo(Invoice::class, 'proforma_invoice_id', 'id');
    }

    /**
     * Get the supplier quotations for the order.
     */
    public function supplierQuotations()
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    /**
     * Alias for supplierQuotations for easier use
     */
    public function quotations()
    {
        return $this->supplierQuotations();
    }

    /**
     * Get approved quotation
     */
    public function approvedQuotation()
    {
        return $this->supplierQuotations()
            ->where('status', SupplierQuotation::STATUS_APPROVED)
            ->first();
    }

    /**
     * Check if all required suppliers have submitted quotations
     */
    public function hasAllQuotations(): bool
    {
        // Get unique categories from order items
        $orderCategories = $this->items->pluck('product.category_id')->unique();
        
        // Get count of suppliers who have these categories
        $totalSuppliersNeeded = User::whereHas('categories', function($query) use ($orderCategories) {
            $query->whereIn('categories.id', $orderCategories);
        })->count();

        // Get count of quotations received
        $quotationsReceived = $this->supplierQuotations()->count();

        return $quotationsReceived >= $totalSuppliersNeeded;
    }

    /**
     * Update quotation status based on supplier quotations
     */
    public function updateQuotationStatus(): void
    {
        // Start transaction to ensure atomic operations
        DB::transaction(function () {
            // Lock the order for update
            $order = self::lockForUpdate()->find($this->id);
            
            if (!$order) {
                throw new \RuntimeException("Order not found");
            }

            $quotations = $order->supplierQuotations;
            $totalQuotations = $quotations->count();
            $approvedQuotations = $quotations->where('status', SupplierQuotation::STATUS_APPROVED)->count();
            $rejectedQuotations = $quotations->where('status', SupplierQuotation::STATUS_REJECTED)->count();
            $pendingQuotations = $quotations->where('status', 'submitted')->count();

            // Determine new quotation status
            $newQuotationStatus = match(true) {
                $approvedQuotations > 0 => self::QUOTATION_STATUS_APPROVED,
                $totalQuotations === 0 => self::QUOTATION_STATUS_PENDING,
                $totalQuotations === $rejectedQuotations => self::QUOTATION_STATUS_REJECTED,
                $pendingQuotations > 0 => self::QUOTATION_STATUS_PARTIAL,
                default => self::QUOTATION_STATUS_COMPLETE
            };

            // Determine new order status
            $newOrderStatus = match($newQuotationStatus) {
                self::QUOTATION_STATUS_APPROVED => self::STATUS_PROCESSING,
                self::QUOTATION_STATUS_REJECTED => self::STATUS_CANCELLED,
                self::QUOTATION_STATUS_PARTIAL => self::STATUS_PROCESSING, // Pending approval
                self::QUOTATION_STATUS_COMPLETE => self::STATUS_PROCESSING, // Pending approval
                default => $order->status
            };

            // Update order status and quotation status
            $order->update([
                'status' => $newOrderStatus,
                'quotation_status' => $newQuotationStatus
            ]);

            Log::info("Order {$order->order_number} status updated: {$order->status} -> {$newOrderStatus}, quotation_status: {$order->quotation_status} -> {$newQuotationStatus}");
        });
    }

    /**
     * Send notification when order is placed
     */
    public function sendOrderPlacedNotification()
    {
        try {
            // Get all admin users (excluding suppliers)
            $admins = User::where(function($query) {
                $query->whereHas('role', function($q) {
                    $q->where('name', 'admin');
                })
                      ->orWhereHas('role', function($roleQuery) {
                          $roleQuery->where('name', 'admin');
                      });
            })
            ->whereNotNull('email')
            ->whereDoesntHave('role', function($query) {
                $query->where('name', 'supplier');
            })
            ->get();

            if ($admins->count() > 0) {
                Notification::send($admins, new OrderNotification($this, 'placed'));
                Log::info('Order placed notification sent to admins');
            }

            // Get unique categories from order items
            $orderCategories = $this->items()
                ->with('product.category')
                ->get()
                ->pluck('product.category_id')
                ->unique()
                ->filter();

            if ($orderCategories->isEmpty()) {
                Log::warning("No valid categories found for order {$this->id}");
                return;
            }

            // Find suppliers with active assignments to these categories
            $suppliers = User::whereHas('role', function($q) {
                $q->where('name', 'supplier');
            })
            ->whereHas('supplierCategories', function($q) use ($orderCategories) {
                $q->whereIn('category_id', $orderCategories)
                  ->where('status', 'active');
            })
            ->get();

            if ($suppliers->isEmpty()) {
                Log::warning("No suppliers found for categories: " . $orderCategories->join(', '));
                return;
            }

            foreach ($suppliers as $supplier) {
                $supplier->notify(new NewOrderSupplierNotification($this));
                Log::info("Order notification sent to supplier: {$supplier->id} for order: {$this->id}");
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order notifications: ' . $e->getMessage());
        }
    }

    /**
     * Send notification when order status changes
     */
    public function sendStatusChangeNotification()
    {
        // Only send notification if status actually changed
        if (!$this->wasChanged('status')) {
            return;
        }

        try {
            // Get all admin users
            $admins = \App\Models\User::where(function($query) {
                $query->whereHas('role', function($q) {
                    $q->where('name', 'admin');
                })
                      ->orWhereHas('role', function($roleQuery) {
                          $roleQuery->where('name', 'admin');
                      });
            })
            ->whereNotNull('email')
            ->whereDoesntHave('role', function($query) {
                $query->where('name', 'supplier');
            })
            ->get();

            if ($admins->count() > 0) {
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\OrderNotification($this, 'status_changed'));
                \Illuminate\Support\Facades\Log::info('Order status change notification (database only) sent to ' . $admins->count() . ' admin(s) for order: ' . $this->order_number . ' (new status: ' . $this->status . ')');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send order status change notification: ' . $e->getMessage());
        }
    }

    /**
     * Check if a status transition is valid
     */
    public static function isValidStatusTransition(?string $from, string $to): bool
    {
        // Allow any initial status setting
        if ($from === null) {
            return true;
        }

        // If status isn't changing, it's valid
        if ($from === $to) {
            return true;
        }

        // Check if the transition is allowed
        return isset(self::ALLOWED_STATUS_TRANSITIONS[$from]) &&
            in_array($to, self::ALLOWED_STATUS_TRANSITIONS[$from]);
    }

    /**
     * Get the cash receipts associated with the order.
     */
    public function cashReceipts()
    {
        return $this->hasMany(\App\Models\CashReceipt::class);
    }

    /**
     * Check if the order has any cash receipts.
     */
    public function hasCashReceipts(): bool
    {
        return $this->cashReceipts()->exists();
    }

    /**
     * Get the total amount of cash receipts for this order.
     */
    public function getTotalCashReceiptsAmount(): float
    {
        return $this->cashReceipts()
            ->where('status', \App\Models\CashReceipt::STATUS_ISSUED)
            ->sum('amount');
    }
} 