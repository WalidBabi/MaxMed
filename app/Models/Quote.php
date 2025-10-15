<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    protected $fillable = [
        'quote_number',
        'customer_name',
        'customer_email',
        'reference_number',
        'quote_date',
        'expiry_date',
        'salesperson',
        'subject',
        'customer_notes',
        'terms_conditions',
        'status',
        'sub_total',
        'shipping_rate',
        'installation_fee',
        'vat_rate',
        'vat_amount',
        'customs_clearance_fee',
        'payment_terms',
        'total_amount',
        'currency',
        'attachments',
        'created_by'
    ];

    protected $casts = [
        'quote_date' => 'date',
        'expiry_date' => 'date',
        'sub_total' => 'decimal:2',
        'shipping_rate' => 'decimal:2',
        'installation_fee' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'customs_clearance_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'attachments' => 'array'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $quote->quote_number = static::generateQuoteNumber();
            }
        });
    }

    /**
     * Generate unique quote number
     */
    public static function generateQuoteNumber()
    {
        $lastQuote = static::orderBy('id', 'desc')->first();
        $number = $lastQuote ? intval(substr($lastQuote->quote_number, 3)) + 1 : 1;
        return 'QT-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the quote items
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('sort_order');
    }

    /**
     * Get the user who created the quote
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the invoices related to this quote
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Calculate totals
     */
    public function calculateTotals()
    {
        $subTotal = $this->items->sum('amount');
        $shippingRate = $this->shipping_rate ?? 0;
        $installationFee = $this->installation_fee ?? 0;
        $customsClearance = $this->customs_clearance_fee ?? 0;
        
        // Compute VAT amount; explicitly zero out when vat_rate is 0 or not set
        $vatRate = (float)($this->vat_rate ?? 0);
        $vatAmount = 0.0;
        if ($vatRate > 0) {
            $vatAmount = round(($subTotal + $shippingRate + $installationFee + $customsClearance) * ($vatRate / 100), 2);
        }
        
        $this->update([
            'sub_total' => $subTotal,
            'vat_amount' => $vatAmount,
            'total_amount' => $subTotal + $shippingRate + $installationFee + $customsClearance + $vatAmount
        ]);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'invoiced' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status);
    }
}
