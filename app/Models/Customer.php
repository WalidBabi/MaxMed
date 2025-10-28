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
        'alternate_names',
        'email',
        'phone',
        'company_name',
        'tax_id',
        'billing_street',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_country',
        'billing_google_maps_link',
        'shipping_street',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'shipping_google_maps_link',
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
        'alternate_names' => 'array',
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
        try {
            if (!$this->billing_street && !$this->billing_city && !$this->billing_state && !$this->billing_zip && !$this->billing_country) {
                return null;
            }

            $addressParts = array_filter([
                $this->billing_street,
                $this->billing_city,
                trim(implode(' ', array_filter([$this->billing_state, $this->billing_zip]))),
                $this->billing_country,
            ]);

            return !empty($addressParts) ? implode("\n", $addressParts) : null;
        } catch (\Exception $e) {
            \Log::error('Error getting billing address for customer ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the customer's full shipping address.
     */
    public function getShippingAddressAttribute(): ?string
    {
        try {
            // If no shipping address fields are set, return billing address
            if (!$this->shipping_street && !$this->shipping_city && !$this->shipping_state && !$this->shipping_zip && !$this->shipping_country) {
                return $this->getBillingAddressAttribute();
            }

            $addressParts = array_filter([
                $this->shipping_street,
                $this->shipping_city,
                trim(implode(' ', array_filter([$this->shipping_state, $this->shipping_zip]))),
                $this->shipping_country,
            ]);

            return !empty($addressParts) ? implode("\n", $addressParts) : $this->getBillingAddressAttribute();
        } catch (\Exception $e) {
            \Log::error('Error getting shipping address for customer ' . $this->id . ': ' . $e->getMessage());
            return $this->getBillingAddressAttribute();
        }
    }

    /**
     * Get the customer's full address (shipping or billing).
     */
    public function getFullAddress(): ?string
    {
        return $this->getShippingAddressAttribute();
    }
}
