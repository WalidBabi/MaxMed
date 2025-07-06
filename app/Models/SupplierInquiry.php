<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplierInquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'product_category_id',
        'product_name',
        'product_description',
        'product_category',
        'product_brand',
        'product_specifications',
        'requirements',
        'notes',
        'internal_notes',
        'customer_reference',
        'reference_number',
        'broadcast_to_all_suppliers',
        'target_supplier_categories',
        'status',
        'broadcast_at',
        'expires_at',
        'supplier_id',
        'forwarded_at',
        'attachments'
    ];

    protected $casts = [
        'broadcast_to_all_suppliers' => 'boolean',
        'target_supplier_categories' => 'array',
        'product_specifications' => 'array',
        'broadcast_at' => 'datetime',
        'expires_at' => 'datetime',
        'attachments' => 'array'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_BROADCAST = 'broadcast';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_QUOTED = 'quoted';
    const STATUS_CONVERTED = 'converted';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            // Generate reference number if not set
            if (!$inquiry->reference_number) {
                $inquiry->reference_number = self::generateReferenceNumber();
            }

            // Set expiry date if not set (default 7 days after broadcast)
            if (!$inquiry->expires_at && $inquiry->broadcast_at) {
                $inquiry->expires_at = $inquiry->broadcast_at->addDays(7);
            }
        });
    }

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierInquiryItem::class)->orderBy('sort_order');
    }

    public function supplierResponses()
    {
        return $this->hasMany(SupplierInquiryResponse::class);
    }

    public function interestedSuppliers()
    {
        return $this->supplierResponses()
            ->where('status', 'interested')
            ->orWhere('status', 'quoted');
    }

    public function quotations()
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    // Helper Methods
    public static function generateReferenceNumber()
    {
        $year = date('Y');
        $prefix = "INQ-{$year}-";
        $lastNumber = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('reference_number', 'desc')
            ->value('reference_number');

        if ($lastNumber) {
            $sequence = (int) substr($lastNumber, -5);
            $sequence++;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public function broadcast()
    {
        $this->update([
            'status' => self::STATUS_BROADCAST,
            'broadcast_at' => now(),
            'expires_at' => now()->addDays(7)
        ]);

        // Create response records for targeted suppliers
        $suppliers = $this->getTargetedSuppliers();
        
        foreach ($suppliers as $supplier) {
            $response = $this->supplierResponses()->create([
                'user_id' => $supplier->id,
                'status' => 'pending'
            ]);

            // Try to notify supplier (email may fail in development)
            try {
                $supplier->notify(new \App\Notifications\NewInquiryNotification($this));
                \Log::info("Email notification sent successfully to supplier {$supplier->id} ({$supplier->email})");
                
                // Mark email as sent successfully
                $response->update([
                    'email_sent_at' => now(),
                    'email_sent_successfully' => true
                ]);
            } catch (\Exception $e) {
                \Log::error("Failed to send email notification to supplier {$supplier->id} ({$supplier->email}): " . $e->getMessage());
                
                // Mark email as failed
                $response->update([
                    'email_sent_at' => now(),
                    'email_sent_successfully' => false,
                    'email_error' => $e->getMessage(),
                    'notes' => 'Email sending failed: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function getTargetedSuppliers()
    {
        $query = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        });

        // Collect all product category IDs from inquiry items
        $productCategoryIds = collect();
        
        // Load items with their products
        $this->load('items.product');
        
        foreach ($this->items as $item) {
            if ($item->product_id && $item->product) {
                // For listed products, get category from product relationship
                if ($item->product->category_id) {
                    $productCategoryIds->push($item->product->category_id);
                }
            } elseif ($item->product_category) {
                // For unlisted products, try to find category by name
                $category = \App\Models\Category::where('name', 'like', '%' . $item->product_category . '%')->first();
                if ($category) {
                    $productCategoryIds->push($category->id);
                }
            }
        }
        
        // Remove duplicates
        $productCategoryIds = $productCategoryIds->unique();

        // Filter suppliers by category assignment - suppliers who can handle ANY of the categories
        if ($productCategoryIds->isNotEmpty()) {
            $query->whereHas('activeSupplierCategories', function ($q) use ($productCategoryIds) {
                $q->whereIn('category_id', $productCategoryIds->toArray());
            });
            
            \Log::info("Filtering suppliers by product categories", [
                'product_category_ids' => $productCategoryIds->toArray(),
                'inquiry_id' => $this->id
            ]);
        } else {
            \Log::warning("No product categories found for inquiry filtering", [
                'inquiry_id' => $this->id,
                'items_count' => $this->items->count()
            ]);
        }

        $suppliers = $query->get();
        
        \Log::info("Targeted suppliers found", [
            'inquiry_id' => $this->id,
            'product_category_ids' => $productCategoryIds->toArray(),
            'supplier_count' => $suppliers->count(),
            'supplier_ids' => $suppliers->pluck('id')->toArray()
        ]);

        return $suppliers;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_EXPIRED, self::STATUS_CONVERTED]);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeBroadcast($query)
    {
        return $query->where('status', self::STATUS_BROADCAST);
    }

    public function scopeQuoted($query)
    {
        return $query->where('status', self::STATUS_QUOTED);
    }
}
