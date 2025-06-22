<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInformation extends Model
{
    protected $table = 'supplier_information';
    
    protected $fillable = [
        'user_id',
        'company_name',
        'business_registration_number',
        'tax_registration_number',
        'trade_license_number',
        'business_address',
        'city',
        'state_province',
        'postal_code',
        'country',
        'phone_primary',
        'phone_secondary',
        'fax',
        'website',
        'primary_contact_name',
        'primary_contact_email',
        'primary_contact_phone',
        'primary_contact_position',
        'secondary_contact_name',
        'secondary_contact_email',
        'secondary_contact_phone',
        'secondary_contact_position',
        'bank_name',
        'bank_branch',
        'account_number',
        'iban',
        'swift_code',
        'beneficiary_name',
        'payment_terms_days',
        'currency_preference',
        'minimum_order_value',
        'standard_lead_time_days',
        'terms_conditions',
        'certifications',
        'specializations',
        'company_description',
        'years_in_business',
        'number_of_employees',
        'overall_rating',
        'total_orders_fulfilled',
        'on_time_delivery_rate',
        'quality_rating',
        'last_order_date',
        'status',
        'accepts_rush_orders',
        'international_shipping',
        'shipping_methods',
        'trade_license_file',
        'tax_certificate_file',
        'company_profile_file',
        'certification_files',
        'created_by',
        'updated_by',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'certifications' => 'array',
        'specializations' => 'array',
        'shipping_methods' => 'array',
        'certification_files' => 'array',
        'minimum_order_value' => 'decimal:2',
        'overall_rating' => 'decimal:2',
        'on_time_delivery_rate' => 'decimal:2',
        'quality_rating' => 'decimal:2',
        'last_order_date' => 'datetime',
        'approved_at' => 'datetime',
        'accepts_rush_orders' => 'boolean',
        'international_shipping' => 'boolean',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_SUSPENDED = 'suspended';

    public static $statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_PENDING_APPROVAL => 'Pending Approval',
        self::STATUS_SUSPENDED => 'Suspended',
    ];

    /**
     * Get the user/supplier
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the supplier (alias for user)
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created this record
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved this supplier
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if supplier is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if supplier is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->business_address,
            $this->city,
            $this->state_province,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUS_INACTIVE => 'bg-gray-100 text-gray-800',
            self::STATUS_PENDING_APPROVAL => 'bg-yellow-100 text-yellow-800',
            self::STATUS_SUSPENDED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get performance score
     */
    public function getPerformanceScoreAttribute(): float
    {
        // Weighted performance calculation
        $ratingWeight = 0.4;
        $deliveryWeight = 0.3;
        $qualityWeight = 0.3;
        
        return ($this->overall_rating * $ratingWeight) + 
               (($this->on_time_delivery_rate / 100) * 5 * $deliveryWeight) + 
               ($this->quality_rating * $qualityWeight);
    }

    /**
     * Approve supplier
     */
    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
    }

    /**
     * Suspend supplier
     */
    public function suspend(): void
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
        ]);
    }

    /**
     * Scope for active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }
} 