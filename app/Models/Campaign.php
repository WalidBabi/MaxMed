<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject',
        'html_content',
        'text_content',
        'email_template_id',
        'type',
        'status',
        'scheduled_at',
        'sent_at',
        'recipients_criteria',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'bounced_count',
        'unsubscribed_count',
        'created_by',
    ];

    protected $casts = [
        'recipients_criteria' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(MarketingContact::class, 'campaign_contacts')
                    ->withPivot([
                        'status',
                        'sent_at',
                        'delivered_at',
                        'opened_at',
                        'clicked_at',
                        'open_count',
                        'click_count',
                        'bounce_reason',
                        'personalization_data'
                    ])
                    ->withTimestamps();
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    // Status checks
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // Campaign type checks
    public function isOneTime(): bool
    {
        return $this->type === 'one_time';
    }

    public function isRecurring(): bool
    {
        return $this->type === 'recurring';
    }

    public function isDrip(): bool
    {
        return $this->type === 'drip';
    }

    // Statistics calculations
    public function getOpenRateAttribute(): float
    {
        if (empty($this->delivered_count) || $this->delivered_count <= 0) {
            return 0;
        }
        
        $opened = $this->opened_count ?? 0;
        return round(($opened / $this->delivered_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if (empty($this->delivered_count) || $this->delivered_count <= 0) {
            return 0;
        }
        
        $clicked = $this->clicked_count ?? 0;
        return round(($clicked / $this->delivered_count) * 100, 2);
    }

    public function getBounceRateAttribute(): float
    {
        if (empty($this->total_recipients) || $this->total_recipients <= 0) {
            return 0;
        }
        
        $bounced = $this->bounced_count ?? 0;
        return round(($bounced / $this->total_recipients) * 100, 2);
    }

    public function getUnsubscribeRateAttribute(): float
    {
        if (empty($this->delivered_count) || $this->delivered_count <= 0) {
            return 0;
        }
        
        $unsubscribed = $this->unsubscribed_count ?? 0;
        return round(($unsubscribed / $this->delivered_count) * 100, 2);
    }

    public function getDeliveryRateAttribute(): float
    {
        if (empty($this->total_recipients) || $this->total_recipients <= 0) {
            return 0;
        }
        
        $delivered = $this->delivered_count ?? 0;
        return round(($delivered / $this->total_recipients) * 100, 2);
    }

    // Campaign actions
    public function schedule(\DateTimeInterface $scheduledAt): void
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    public function markAsSending(): void
    {
        $this->update([
            'status' => 'sending',
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function pause(): void
    {
        if ($this->isSending() || $this->isScheduled()) {
            $this->update(['status' => 'paused']);
        }
    }

    public function resume(): void
    {
        if ($this->isPaused()) {
            $status = $this->scheduled_at && $this->scheduled_at->isFuture() ? 'scheduled' : 'sending';
            $this->update(['status' => $status]);
        }
    }

    public function cancel(): void
    {
        if (!$this->isSent()) {
            $this->update(['status' => 'cancelled']);
        }
    }

    public function updateStatistics(): void
    {
        $stats = DB::table('campaign_contacts')
            ->where('campaign_id', $this->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened,
                SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked,
                SUM(CASE WHEN status = "bounced" THEN 1 ELSE 0 END) as bounced
            ')
            ->first();

        $this->update([
            'total_recipients' => $stats->total ?? 0,
            'sent_count' => $stats->sent ?? 0,
            'delivered_count' => $stats->delivered ?? 0,
            'opened_count' => $stats->opened ?? 0,
            'clicked_count' => $stats->clicked ?? 0,
            'bounced_count' => $stats->bounced ?? 0,
        ]);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'sending']);
    }

    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('scheduled_at', '<=', now());
    }
} 