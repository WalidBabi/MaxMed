<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'marketing_contact_id',
        'email',
        'subject',
        'type',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'message_id',
        'bounce_reason',
        'error_message',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(MarketingContact::class, 'marketing_contact_id');
    }

    // Status checks
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isBounced(): bool
    {
        return $this->status === 'bounced';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isSpam(): bool
    {
        return $this->status === 'spam';
    }

    // Event tracking
    public function markAsSent(string $messageId = null): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'message_id' => $messageId,
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsOpened(string $ipAddress = null, string $userAgent = null): void
    {
        $this->update([
            'opened_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        // Update campaign contact pivot table
        if ($this->campaign_id) {
            $campaignContact = $this->campaign
                                    ->contacts()
                                    ->where('marketing_contact_id', $this->marketing_contact_id)
                                    ->first();
            
            if ($campaignContact && !$campaignContact->pivot->opened_at) {
                $campaignContact->pivot->update([
                    'opened_at' => now(),
                    'open_count' => $campaignContact->pivot->open_count + 1,
                ]);
            }
        }
    }

    public function markAsClicked(string $ipAddress = null, string $userAgent = null): void
    {
        $this->update([
            'clicked_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        // Update campaign contact pivot table
        if ($this->campaign_id) {
            $campaignContact = $this->campaign
                                    ->contacts()
                                    ->where('marketing_contact_id', $this->marketing_contact_id)
                                    ->first();
            
            if ($campaignContact) {
                $campaignContact->pivot->update([
                    'clicked_at' => now(),
                    'click_count' => $campaignContact->pivot->click_count + 1,
                ]);
            }
        }
    }

    public function markAsBounced(string $reason = null): void
    {
        $this->update([
            'status' => 'bounced',
            'bounce_reason' => $reason,
        ]);

        // Update campaign contact pivot table
        if ($this->campaign_id) {
            $campaignContact = $this->campaign
                                    ->contacts()
                                    ->where('marketing_contact_id', $this->marketing_contact_id)
                                    ->first();
            
            if ($campaignContact) {
                $campaignContact->pivot->update([
                    'status' => 'bounced',
                    'bounce_reason' => $reason,
                ]);
            }
        }
    }

    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);

        // Update campaign contact pivot table
        if ($this->campaign_id) {
            $campaignContact = $this->campaign
                                    ->contacts()
                                    ->where('marketing_contact_id', $this->marketing_contact_id)
                                    ->first();
            
            if ($campaignContact) {
                $campaignContact->pivot->update([
                    'status' => 'failed',
                ]);
            }
        }
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeBounced($query)
    {
        return $query->where('status', 'bounced');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeOpened($query)
    {
        return $query->whereNotNull('opened_at');
    }

    public function scopeClicked($query)
    {
        return $query->whereNotNull('clicked_at');
    }

    public function scopeCampaignType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }
} 