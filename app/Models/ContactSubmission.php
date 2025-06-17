<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'phone',
        'company',
        'status',
        'converted_to_inquiry_id',
        'assigned_to',
        'admin_notes',
        'responded_at',
        'lead_potential',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the quotation request this was converted to
     */
    public function convertedToInquiry(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class, 'converted_to_inquiry_id');
    }

    /**
     * Get the admin user assigned to handle this submission
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Check if this is a sales inquiry
     */
    public function isSalesInquiry(): bool
    {
        return strtolower($this->subject) === 'sales inquiry' || 
               str_contains(strtolower($this->subject), 'sales') ||
               str_contains(strtolower($this->message), 'quote') ||
               str_contains(strtolower($this->message), 'price');
    }

    /**
     * Check if this can be converted to inquiry
     */
    public function canConvertToInquiry(): bool
    {
        return $this->status === 'new' || $this->status === 'in_review';
    }

    /**
     * Check if this can be converted to lead
     */
    public function canConvertToLead(): bool
    {
        return in_array($this->status, ['new', 'in_review']);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'new' => 'bg-blue-100 text-blue-800',
            'in_review' => 'bg-yellow-100 text-yellow-800',
            'converted_to_lead' => 'bg-indigo-100 text-indigo-800',
            'converted_to_inquiry' => 'bg-green-100 text-green-800',
            'responded' => 'bg-purple-100 text-purple-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'new' => 'New',
            'in_review' => 'In Review',
            'converted_to_lead' => 'Converted to Lead',
            'converted_to_inquiry' => 'Converted to Inquiry',
            'responded' => 'Responded',
            'closed' => 'Closed',
            default => ucfirst($this->status)
        };
    }

    /**
     * Scope to get new submissions
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope to get sales inquiries
     */
    public function scopeSalesInquiries($query)
    {
        return $query->where('subject', 'sales inquiry')
                    ->orWhere('subject', 'like', '%sales%')
                    ->orWhere('message', 'like', '%quote%')
                    ->orWhere('message', 'like', '%price%');
    }

    /**
     * Scope to get submissions by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
} 