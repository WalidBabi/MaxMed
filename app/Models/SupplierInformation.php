<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInformation extends Model
{
    use HasFactory;

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'supplier_information';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
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
        'website',
        'years_in_business',
        'company_description',
        'primary_contact_name',
        'primary_contact_position',
        'primary_contact_email',
        'primary_contact_phone',
        'documents',
        'specializations',
        'onboarding_completed',
        'onboarding_completed_at',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'documents' => 'array',
        'specializations' => 'array',
        'onboarding_completed' => 'boolean',
        'onboarding_completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the supplier information.
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