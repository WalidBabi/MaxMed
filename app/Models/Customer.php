<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'company_name',
        'tax_id',
        'billing_street',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_country',
        'shipping_street',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'notes',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user associated with the customer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer's full billing address.
     */
    public function getBillingAddressAttribute(): ?string
    {
        if (!$this->billing_street) {
            return null;
        }

        return implode("\n", array_filter([
            $this->billing_street,
            $this->billing_city,
            trim(implode(' ', array_filter([$this->billing_state, $this->billing_zip]))),
            $this->billing_country,
        ]));
    }

    /**
     * Get the customer's full shipping address.
     */
    public function getShippingAddressAttribute(): ?string
    {
        if (!$this->shipping_street) {
            return $this->billing_address;
        }

        return implode("\n", array_filter([
            $this->shipping_street,
            $this->shipping_city,
            trim(implode(' ', array_filter([$this->shipping_state, $this->shipping_zip]))),
            $this->shipping_country,
        ]));
    }
}
