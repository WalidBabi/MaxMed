<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInquiryResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_inquiry_id',
        'user_id',
        'status',
        'viewed_at',
        'notes',
        'email_sent_at',
        'email_sent_successfully',
        'email_error',
        'email_opened_at',
        'email_click_count'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'email_opened_at' => 'datetime',
        'email_sent_successfully' => 'boolean'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_VIEWED = 'viewed';
    const STATUS_QUOTED = 'quoted';
    const STATUS_NOT_AVAILABLE = 'not_available';
    const STATUS_ACCEPTED = 'accepted';  // Admin accepted quotation - proceed with order
    const STATUS_REJECTED = 'rejected';  // Admin rejected quotation

    // Relationships
    public function inquiry()
    {
        return $this->belongsTo(SupplierInquiry::class, 'supplier_inquiry_id');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quotation()
    {
        return $this->hasOne(SupplierQuotation::class);
    }

    // Helper Methods
    public function markAsViewed()
    {
        // If already quoted, don't allow any status changes
        if ($this->status === self::STATUS_QUOTED) {
            return;
        }

        if (!$this->viewed_at) {
            $this->update([
                'status' => self::STATUS_VIEWED,
                'viewed_at' => now()
            ]);
        }
    }

    public function markAsNotAvailable()
    {
        // If already quoted, don't allow any status changes
        if ($this->status === self::STATUS_QUOTED) {
            throw new \Exception('Cannot change status after submitting a quotation.');
        }

        $this->update(['status' => self::STATUS_NOT_AVAILABLE]);
    }

    public function markAsQuoted()
    {
        // If already quoted, don't allow any status changes
        if ($this->status === self::STATUS_QUOTED) {
            throw new \Exception('Quotation has already been submitted.');
        }

        $this->update(['status' => self::STATUS_QUOTED]);
    }

    public function markAsAccepted()
    {
        // Only allow accepting if currently quoted
        if ($this->status !== self::STATUS_QUOTED) {
            throw new \Exception('Can only accept quoted inquiries.');
        }

        $this->update(['status' => self::STATUS_ACCEPTED]);
    }

    // Override the update method to prevent status changes for quoted inquiries
    public function update(array $attributes = [], array $options = [])
    {
        // If trying to change status of a quoted inquiry
        if ($this->status === self::STATUS_QUOTED && isset($attributes['status']) && $attributes['status'] !== self::STATUS_ACCEPTED) {
            // Allow only non-status updates or transition to accepted
            if (isset($attributes['status'])) {
                unset($attributes['status']);
            }
        }

        return parent::update($attributes, $options);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeViewed($query)
    {
        return $query->where('status', self::STATUS_VIEWED);
    }

    public function scopeNotAvailable($query)
    {
        return $query->where('status', self::STATUS_NOT_AVAILABLE);
    }

    public function scopeQuoted($query)
    {
        return $query->where('status', self::STATUS_QUOTED);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }
} 