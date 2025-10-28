<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'attachments',
        'expected_close_date',
        'last_contacted_at',
        'assigned_to',
        'email_history',
        'last_email_sent_at',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'expected_close_date' => 'date',
        'last_contacted_at' => 'datetime',
        'last_email_sent_at' => 'datetime',
        'attachments' => 'array',
        'email_history' => 'array',
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
            'quote_requested' => 'slate',
            'getting_price' => 'indigo',
            'price_submitted' => 'teal',
            'quote_sent' => 'cyan',
            'proforma_sent' => 'purple',
            'follow_up_1' => 'sky',
            'follow_up_2' => 'blue', 
            'follow_up_3' => 'indigo',
            'negotiating_price' => 'orange',
            'payment_pending' => 'emerald',
            'order_confirmed' => 'green',
            'deal_lost' => 'gray',
            'on_hold' => 'slate',
            'cancelled' => 'red',
            'pending_approval' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'in_progress' => 'blue',
            'completed' => 'green',
            'archived' => 'gray',
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

    /**
     * Get human readable age since creation, e.g., "1 day 2 hours" (no minutes)
     */
    public function getCreatedAgoAttribute()
    {
        $from = $this->created_at ?? now();
        $totalMinutes = $from->diffInMinutes(now());

        $days = intdiv($totalMinutes, 60 * 24);
        $remainingAfterDays = $totalMinutes - ($days * 60 * 24);
        $hours = intdiv($remainingAfterDays, 60);

        $parts = [];
        if ($days > 0) {
            $parts[] = $days . ' ' . ($days === 1 ? 'day' : 'days');
        }
        // Always show hours (even 0 hours when days shown) to avoid minutes
        $parts[] = $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');

        return implode(' ', $parts);
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
        // Check if a customer already exists with this email
        $existingCustomer = Customer::where('email', $this->email)->first();
        if ($existingCustomer) {
            // If the existing customer's name matches this lead, reuse it
            $leadFullName = trim($this->full_name);
            if (strcasecmp(trim($existingCustomer->name), $leadFullName) === 0) {
                return $existingCustomer;
            }

            // Names differ: create a separate customer record with same email
            // Also store the new name in the existing customer's alternate_names for suggestions
            try {
                $alts = is_array($existingCustomer->alternate_names) ? $existingCustomer->alternate_names : [];
                if ($leadFullName !== '' && !in_array($leadFullName, $alts, true)) {
                    $alts[] = $leadFullName;
                    $existingCustomer->alternate_names = array_values($alts);
                    $existingCustomer->save();
                }
            } catch (\Exception $e) {
                \Log::warning('Failed updating alternate_names on existing customer', [
                    'customer_id' => $existingCustomer->id,
                    'error' => $e->getMessage()
                ]);
            }
            // Continue to create a new customer entry below
        }

        // Parse company address into components
        $addressComponents = $this->parseAddress($this->company_address);

        // Create new customer (allows duplicate email)
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

    // Attachment helper methods
    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    public function getAttachmentCount()
    {
        return $this->attachments ? count($this->attachments) : 0;
    }

    public function getAttachmentsByType()
    {
        if (!$this->attachments) {
            return [];
        }

        $grouped = [];
        foreach ($this->attachments as $attachment) {
            $extension = strtolower(pathinfo($attachment['original_name'], PATHINFO_EXTENSION));
            
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $grouped['images'][] = $attachment;
            } elseif (in_array($extension, ['pdf'])) {
                $grouped['pdfs'][] = $attachment;
            } elseif (in_array($extension, ['doc', 'docx'])) {
                $grouped['documents'][] = $attachment;
            } else {
                $grouped['others'][] = $attachment;
            }
        }
        
        return $grouped;
    }

    public function addAttachment($filePath, $originalName, $size = null)
    {
        $attachments = $this->attachments ?: [];
        $attachments[] = [
            'path' => $filePath,
            'original_name' => $originalName,
            'size' => $size,
            'uploaded_at' => now()->toISOString(),
        ];
        
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * Get price submissions for this lead
     */
    public function priceSubmissions(): HasMany
    {
        return $this->hasMany(LeadPriceSubmission::class, 'crm_lead_id');
    }

    /**
     * Get the latest price submission
     */
    public function latestPriceSubmission(): HasOne
    {
        return $this->hasOne(LeadPriceSubmission::class, 'crm_lead_id')->latest();
    }

    /**
     * Check if lead has any price submissions
     */
    public function hasPriceSubmissions()
    {
        return $this->priceSubmissions()->exists();
    }
} 