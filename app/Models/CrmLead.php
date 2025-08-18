<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CrmLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'phone',
        'company_name',
        'job_title',
        'company_address',
        'status',
        'source',
        'priority',
        'estimated_value',
        'notes',
        'expected_close_date',
        'last_contacted_at',
        'assigned_to',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'expected_close_date' => 'date',
        'last_contacted_at' => 'datetime',
    ];

    // Relationships
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(CrmActivity::class, 'lead_id')->orderBy('activity_date', 'desc');
    }

    public function deals()
    {
        return $this->hasMany(CrmDeal::class, 'lead_id');
    }

    public function quotationRequests()
    {
        return $this->hasMany(QuotationRequest::class, 'lead_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'email', 'email');
    }

    // Methods
    public function hasActiveQuotationRequest()
    {
        return $this->quotationRequests()
            ->whereIn('status', ['pending', 'forwarded', 'supplier_responded'])
            ->exists();
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'new_inquiry' => 'blue',
            'quote_requested' => 'purple',
            'follow_up_1' => 'amber',
            'follow_up_2' => 'orange', 
            'follow_up_3' => 'red',
            'quote_sent' => 'indigo',
            'negotiating_price' => 'yellow',
            'payment_pending' => 'emerald',
            'order_confirmed' => 'green',
            'deal_lost' => 'gray',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            default => 'gray'
        };
    }

    // Methods
    public function daysSinceLastContact()
    {
        if (!$this->last_contacted_at) {
            return $this->created_at->diffInDays(now());
        }
        return $this->last_contacted_at->diffInDays(now());
    }

    public function isOverdue()
    {
        return $this->daysSinceLastContact() > 7; // Consider overdue if no contact in 7 days
    }

    public function logActivity($type, $subject, $description = null, $activityDate = null)
    {
        return $this->activities()->create([
            'user_id' => auth()->id(),
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'activity_date' => $activityDate ?? now(),
        ]);
    }

    public function updateLastContacted()
    {
        $this->update(['last_contacted_at' => now()]);
    }

    /**
     * Create a customer from this lead if one doesn't exist
     */
    public function createCustomer()
    {
        // Check if customer already exists with this email
        $existingCustomer = Customer::where('email', $this->email)->first();
        
        if ($existingCustomer) {
            return $existingCustomer;
        }

        // Parse company address into components
        $addressComponents = $this->parseAddress($this->company_address);

        // Create new customer
        $customer = Customer::create([
            'name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->mobile ?: $this->phone,
            'company_name' => $this->company_name,
            'billing_street' => $addressComponents['street'],
            'billing_city' => $addressComponents['city'],
            'billing_state' => $addressComponents['state'],
            'billing_zip' => $addressComponents['zip'],
            'billing_country' => $addressComponents['country'],
            'shipping_street' => $addressComponents['street'],
            'shipping_city' => $addressComponents['city'],
            'shipping_state' => $addressComponents['state'],
            'shipping_zip' => $addressComponents['zip'],
            'shipping_country' => $addressComponents['country'],
            'notes' => "Auto-created from CRM Lead #{$this->id} - {$this->job_title}",
            'is_active' => true,
        ]);

        return $customer;
    }

    /**
     * Parse address string into components
     */
    private function parseAddress($address)
    {
        if (empty($address)) {
            return [
                'street' => null,
                'city' => null,
                'state' => null,
                'zip' => null,
                'country' => null,
            ];
        }

        // Basic address parsing - this can be enhanced based on your needs
        $lines = array_map('trim', explode("\n", $address));
        
        return [
            'street' => $lines[0] ?? null,
            'city' => $lines[1] ?? null,
            'state' => $lines[2] ?? null,
            'zip' => null,
            'country' => $lines[3] ?? null,
        ];
    }

    // Scopes
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where(function($q) {
            $q->where('last_contacted_at', '<', now()->subDays(7))
              ->orWhereNull('last_contacted_at');
        })->where('status', '!=', 'won')->where('status', '!=', 'lost');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }
} 