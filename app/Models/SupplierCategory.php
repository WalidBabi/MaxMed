<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'supplier_id',
        'category_id',
        'status',
        'minimum_order_value',
        'lead_time_days',
        'notes',
        'commission_rate',
        'avg_response_time_hours',
        'quotation_win_rate',
        'total_quotations',
        'won_quotations',
        'avg_customer_rating',
        'assigned_by',
        'assigned_at',
        'last_quotation_at',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'minimum_order_value' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'avg_response_time_hours' => 'decimal:2',
        'quotation_win_rate' => 'decimal:2',
        'avg_customer_rating' => 'decimal:2',
        'assigned_at' => 'datetime',
        'last_quotation_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING_APPROVAL = 'pending_approval';

    public static $statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_PENDING_APPROVAL => 'Pending Approval',
    ];

    /**
     * Get the supplier that owns the category assignment.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the category that belongs to this assignment.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who approved the category assignment.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who assigned this category
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if the assignment is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get the status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUS_INACTIVE => 'bg-red-100 text-red-800',
            self::STATUS_PENDING_APPROVAL => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Update quotation statistics
     */
    public function updateQuotationStats(bool $won = false): void
    {
        $this->increment('total_quotations');
        
        if ($won) {
            $this->increment('won_quotations');
        }
        
        // Recalculate win rate
        $this->quotation_win_rate = $this->total_quotations > 0 
            ? ($this->won_quotations / $this->total_quotations) * 100 
            : 0;
        
        $this->last_quotation_at = now();
        $this->save();
    }

    /**
     * Update response time statistics
     */
    public function updateResponseTime(float $responseTimeHours): void
    {
        // Simple moving average
        $this->avg_response_time_hours = ($this->avg_response_time_hours + $responseTimeHours) / 2;
        $this->save();
    }

    /**
     * Scope to get active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to get assignments by category
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get assignments by supplier
     */
    public function scopeForSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }



    // Relationships
    public function suppliers()
    {
        return $this->belongsToMany(User::class, 'supplier_category_type_user')
            ->whereHas('roles', function($query) {
                $query->where('name', 'supplier');
            });
    }

    public function activeSuppliers()
    {
        return $this->suppliers();
    }

    // Scopes
    public function scopeWithSupplierCount($query)
    {
        return $query->withCount('suppliers');
    }

    public function scopeWithActiveSupplierCount($query)
    {
        return $query->withCount(['suppliers as active_suppliers_count' => function($query) {
            $query->where('status', 'active');
        }]);
    }
} 