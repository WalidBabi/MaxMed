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
        'order_number',
        'total_amount',
        'status',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'shipping_phone',
        'notes',
        'requires_quotation',
        'quotation_status'
    ];

    // Status constants - Comprehensive order lifecycle for supplier workflow and customer tracking
    const STATUS_PENDING = 'pending';                           // Initial state
    const STATUS_AWAITING_QUOTATIONS = 'awaiting_quotations';   // Supplier: Needs Quotation
    const STATUS_QUOTATIONS_RECEIVED = 'quotations_received';   // Supplier: Pending Approval
    const STATUS_APPROVED = 'approved';                         // Supplier: Approved - To Process
    const STATUS_PROCESSING = 'processing';                     // Supplier: Processing order
    const STATUS_PREPARED = 'prepared';                         // Customer: Order prepared for shipping
    const STATUS_SHIPPED = 'shipped';                          // Customer: Package shipped
    const STATUS_IN_TRANSIT = 'in_transit';                    // Customer: Out for delivery
    const STATUS_DELIVERED = 'delivered';                      // Customer: Package delivered
    const STATUS_COMPLETED = 'completed';                      // Order fully completed
    const STATUS_CANCELLED = 'cancelled';                      // Order cancelled

    // Quotation status constants
    const QUOTATION_STATUS_PENDING = 'pending';
    const QUOTATION_STATUS_PARTIAL = 'partial';
    const QUOTATION_STATUS_COMPLETE = 'complete';
    const QUOTATION_STATUS_APPROVED = 'approved';
    const QUOTATION_STATUS_REJECTED = 'rejected';

    // Add after the status constants
    private const ALLOWED_STATUS_TRANSITIONS = [
        self::STATUS_PENDING => [
            self::STATUS_AWAITING_QUOTATIONS,   // For quotation orders
            self::STATUS_PROCESSING,            // For direct orders
            self::STATUS_CANCELLED
        ],
        self::STATUS_AWAITING_QUOTATIONS => [
            self::STATUS_QUOTATIONS_RECEIVED,   // When quotations are submitted
            self::STATUS_CANCELLED
        ],
        self::STATUS_QUOTATIONS_RECEIVED => [
            self::STATUS_APPROVED,              // When quotation is approved
            self::STATUS_AWAITING_QUOTATIONS,   // If need more quotations
            self::STATUS_CANCELLED
        ],
        self::STATUS_APPROVED => [
            self::STATUS_PROCESSING,            // Supplier starts processing
            self::STATUS_CANCELLED
        ],
        self::STATUS_PROCESSING => [
            self::STATUS_PREPARED,              // Order prepared for shipping
            self::STATUS_CANCELLED
        ],
        self::STATUS_PREPARED => [
            self::STATUS_SHIPPED,               // Package handed to carrier
            self::STATUS_CANCELLED
        ],
        self::STATUS_SHIPPED => [
            self::STATUS_IN_TRANSIT,            // Package in transit
            self::STATUS_DELIVERED,             // Direct delivery (skip in_transit)
            self::STATUS_CANCELLED
        ],
        self::STATUS_IN_TRANSIT => [
            self::STATUS_DELIVERED,             // Package delivered to customer
            self::STATUS_CANCELLED
        ],
        self::STATUS_DELIVERED => [
            self::STATUS_COMPLETED              // Order fully completed
        ]
    ];

    protected $casts = [
        'requires_quotation' => 'boolean'
    ];

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
            
            // Set default values for new orders that require quotation
            if ($order->requires_quotation) {
                $order->status = self::STATUS_AWAITING_QUOTATIONS;
                $order->quotation_status = self::QUOTATION_STATUS_PENDING;
            } else {
                $order->status = self::STATUS_PENDING;
            }
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
        
        // Send notification when order is created
        static::created(function ($order) {
            $order->sendOrderPlacedNotification();
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
        // Auto-update delivery status based on order status
        if ($this->hasDelivery()) {
            $this->syncDeliveryStatus();
        }
    }

    /**
     * Automatically create delivery when order is in appropriate state
     */
    public function autoCreateDelivery()
    {
        try {
            // Check if delivery already exists
            if ($this->hasDelivery()) {
                Log::info("Delivery already exists for order {$this->id}");
                return;
            }

            // Only create delivery for orders in processing state
            if ($this->status !== self::STATUS_PROCESSING) {
                Log::info("Order {$this->id} not ready for delivery creation. Current status: {$this->status}");
                return;
            }

            // Ensure we have an approved quotation for orders requiring quotation
            if ($this->requires_quotation && 
                (!$this->approvedQuotation || $this->quotation_status !== self::QUOTATION_STATUS_APPROVED)) {
                Log::info("Order {$this->id} requires approved quotation before delivery creation");
                return;
            }

            $delivery = Delivery::create([
                'order_id' => $this->id,
                'status' => 'pending',
                'carrier' => 'TBD',
                'tracking_number' => 'TRK' . strtoupper(uniqid()),
                'shipping_address' => $this->getFullShippingAddress(),
                'billing_address' => $this->getFullShippingAddress(),
                'shipping_cost' => 0,
                'total_weight' => $this->calculateTotalWeight(),
                'notes' => 'Auto-created delivery for order ' . $this->order_number,
            ]);

            Log::info("Auto-created delivery {$delivery->id} for order {$this->id} with status 'pending'");

            // Notify supplier about new order
            $this->notifySupplierNewOrder($delivery);

        } catch (\Exception $e) {
            Log::error('Failed to auto-create delivery for order: ' . $e->getMessage());
        }
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
        
        $statusMapping = [
            'shipped' => 'in_transit',
            'delivered' => 'delivered',
            'completed' => 'delivered',
            'cancelled' => 'cancelled'
        ];

        $newStatus = $statusMapping[$this->status] ?? $delivery->status;
        
        if ($newStatus !== $delivery->status) {
            $delivery->update(['status' => $newStatus]);
            
            if ($newStatus === 'delivered' && !$delivery->delivered_at) {
                $delivery->update(['delivered_at' => now()]);
            }
            
            Log::info("Auto-updated delivery {$delivery->id} status to {$newStatus}");
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
        return $this->hasMany(OrderItem::class);
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
     * Get the proforma invoice that created this order.
     */
    public function proformaInvoice()
    {
        return $this->hasOne(Invoice::class, 'order_id', 'id')->where('type', 'proforma');
    }

    /**
     * Get the supplier quotations for the order.
     */
    public function supplierQuotations()
    {
        return $this->hasMany(SupplierQuotation::class);
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
            $pendingQuotations = $quotations->where('status', SupplierQuotation::STATUS_PENDING)->count();

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
                self::QUOTATION_STATUS_PARTIAL => self::STATUS_QUOTATIONS_RECEIVED, // Pending approval
                self::QUOTATION_STATUS_COMPLETE => self::STATUS_QUOTATIONS_RECEIVED, // Pending approval
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
                $query->where('is_admin', true)
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
                $query->where('is_admin', true)
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
                \Illuminate\Support\Facades\Log::info('Order status change notification sent to ' . $admins->count() . ' admin(s) for order: ' . $this->order_number . ' (new status: ' . $this->status . ')');
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
} 