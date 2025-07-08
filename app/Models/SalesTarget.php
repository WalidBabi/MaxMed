<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class SalesTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'period_type',
        'start_date',
        'end_date',
        'target_amount',
        'achieved_amount',
        'target_type',
        'status',
        'target_breakdown',
        'created_by',
        'assigned_to'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_amount' => 'decimal:2',
        'achieved_amount' => 'decimal:2',
        'target_breakdown' => 'array'
    ];

    const PERIOD_TYPES = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly' => 'Yearly'
    ];

    const TARGET_TYPES = [
        'revenue' => 'Revenue',
        'orders' => 'Orders',
        'customers' => 'Customers',
        'products' => 'Products'
    ];

    const STATUS_OPTIONS = [
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($target) {
            if (empty($target->achieved_amount)) {
                $target->achieved_amount = 0;
            }
        });
    }

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Calculate progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        
        return round(($this->achieved_amount / $this->target_amount) * 100, 2);
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->achieved_amount);
    }

    /**
     * Check if target is completed
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->achieved_amount >= $this->target_amount;
    }

    /**
     * Check if target is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date->isPast() && !$this->is_completed;
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    /**
     * Get progress status
     */
    public function getProgressStatusAttribute(): string
    {
        if ($this->is_completed) {
            return 'completed';
        }
        
        if ($this->is_overdue) {
            return 'overdue';
        }
        
        $percentage = $this->progress_percentage;
        
        if ($percentage >= 80) {
            return 'on_track';
        } elseif ($percentage >= 60) {
            return 'moderate';
        } else {
            return 'at_risk';
        }
    }

    /**
     * Get progress badge class
     */
    public function getProgressBadgeClassAttribute(): string
    {
        switch ($this->progress_status) {
            case 'completed':
                return 'bg-green-100 text-green-800';
            case 'on_track':
                return 'bg-blue-100 text-blue-800';
            case 'moderate':
                return 'bg-yellow-100 text-yellow-800';
            case 'at_risk':
                return 'bg-red-100 text-red-800';
            case 'overdue':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Scope for active targets
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for current period targets
     */
    public function scopeCurrentPeriod($query)
    {
        return $query->where('start_date', '<=', Carbon::now())
                    ->where('end_date', '>=', Carbon::now());
    }

    /**
     * Scope for targets by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('target_type', $type);
    }

    /**
     * Update achieved amount based on actual sales data
     */
    public function updateAchievedAmount(): void
    {
        $achieved = $this->calculateAchievedAmount();
        $this->update(['achieved_amount' => $achieved]);
    }

    /**
     * Calculate achieved amount based on target type
     */
    private function calculateAchievedAmount(): float
    {
        $startDate = $this->start_date;
        $endDate = $this->end_date;

        switch ($this->target_type) {
            case 'revenue':
                return $this->calculateRevenueAchieved($startDate, $endDate);
            case 'orders':
                return $this->calculateOrdersAchieved($startDate, $endDate);
            case 'customers':
                return $this->calculateCustomersAchieved($startDate, $endDate);
            case 'products':
                return $this->calculateProductsAchieved($startDate, $endDate);
            default:
                return 0;
        }
    }

    /**
     * Calculate revenue achieved
     */
    private function calculateRevenueAchieved($startDate, $endDate): float
    {
        return Invoice::whereBetween('invoice_date', [$startDate, $endDate])
                     ->where('payment_status', '!=', 'cancelled')
                     ->sum('total_amount');
    }

    /**
     * Calculate orders achieved
     */
    private function calculateOrdersAchieved($startDate, $endDate): float
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
                   ->where('status', '!=', 'cancelled')
                   ->count();
    }

    /**
     * Calculate customers achieved
     */
    private function calculateCustomersAchieved($startDate, $endDate): float
    {
        return Customer::whereBetween('created_at', [$startDate, $endDate])
                      ->count();
    }

    /**
     * Calculate products achieved
     */
    private function calculateProductsAchieved($startDate, $endDate): float
    {
        return OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', '!=', 'cancelled');
        })->sum('quantity');
    }
} 