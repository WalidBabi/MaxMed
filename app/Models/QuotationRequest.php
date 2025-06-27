<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\CrmLead;
use App\Models\Product;
use App\Models\Quote;
use App\Models\SupplierQuotation;
use App\Models\ContactSubmission;
use App\Notifications\QuotationRequestNotification;

class QuotationRequest extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'lead_id',
        'quantity',
        'size',
        'requirements',
        'notes',
        'delivery_timeline',
        'status',
        'supplier_id',
        'forwarded_at',
        'supplier_responded_at',
        'internal_notes',
        'supplier_response',
        'supplier_notes',
        'generated_quote_id',
    ];

    protected $casts = [
        'forwarded_at' => 'datetime',
        'supplier_responded_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Send notification when a new quotation request is created
        static::created(function ($quotationRequest) {
            $quotationRequest->sendNewQuotationNotification();
        });
    }

    /**
     * Send notification to admin/CRM users about new quotation request
     */
    public function sendNewQuotationNotification()
    {
        try {
            // Get all admin users and CRM users
            $users = User::where(function($query) {
                $query->where('is_admin', true)
                      ->orWhereHas('role', function($roleQuery) {
                          $roleQuery->whereIn('name', ['admin', 'crm']);
                      });
            })
            ->whereNotNull('email')
            ->whereDoesntHave('role', function($query) {
                $query->where('name', 'supplier');
            })
            ->get();

            if ($users->count() > 0) {
                Notification::send($users, new QuotationRequestNotification($this));
                Log::info('Quotation request notification sent to ' . $users->count() . ' user(s) for request: ' . $this->id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send quotation request notification: ' . $e->getMessage());
        }
    }

    /**
     * Get the product for this quotation request
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer user for this quotation request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lead associated with this quotation request
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    /**
     * Get the supplier assigned to this request
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the generated quote for this request
     */
    public function generatedQuote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'generated_quote_id');
    }

    /**
     * Get the supplier quotations for this request
     */
    public function supplierQuotations(): HasMany
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    /**
     * Get the latest supplier quotation
     */
    public function latestSupplierQuotation(): HasOne
    {
        return $this->hasOne(SupplierQuotation::class)->latest();
    }

    /**
     * Get the related contact submission (if this quotation was created from one)
     */
    public function relatedContactSubmission(): BelongsTo
    {
        return $this->belongsTo(ContactSubmission::class, 'id', 'converted_to_inquiry_id');
    }

    /**
     * Get formatted status for display
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'forwarded' => 'Forwarded to Supplier',
            'supplier_responded' => 'Supplier Responded',
            'quote_created' => 'Quote Created',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'converted_to_lead' => 'Converted to Lead',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge CSS class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'forwarded' => 'bg-blue-100 text-blue-800',
            'supplier_responded' => 'bg-purple-100 text-purple-800',
            'quote_created' => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'converted_to_lead' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if the request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request has been forwarded
     */
    public function isForwarded(): bool
    {
        return in_array($this->status, ['forwarded', 'supplier_responded', 'quote_created', 'completed']);
    }

    /**
     * Check if supplier has responded
     */
    public function hasSupplierResponse(): bool
    {
        return $this->supplier_response !== 'pending';
    }

    /**
     * Get supplier response badge CSS class
     */
    public function getSupplierResponseBadgeClassAttribute(): string
    {
        return match($this->supplier_response) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'available' => 'bg-green-100 text-green-800',
            'not_available' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scope to get requests by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get requests for a specific supplier
     */
    public function scopeForSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Scope to get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get forwarded requests awaiting supplier response
     */
    public function scopeAwaitingSupplierResponse($query)
    {
        return $query->where('status', 'forwarded')
                    ->where('supplier_response', 'pending');
    }
}
