<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject',
        'subject_variant_b',
        'html_content',
        'text_content',
        'email_template_id',
        // New A/B testing fields
        'cta_text_variant_b',
        'cta_url_variant_b',
        'cta_color_variant_b',
        'email_template_variant_b_id',
        'html_content_variant_b',
        'text_content_variant_b',
        'scheduled_at_variant_b',
        'ab_test_variant_data',
        'type',
        'status',
        'is_ab_test',
        'ab_test_type',
        'ab_test_split_percentage',
        'ab_test_winner_selected_at',
        'ab_test_winner',
        'ab_test_results',
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
        'ab_test_results' => 'array',
        'ab_test_variant_data' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'ab_test_winner_selected_at' => 'datetime',
        'is_ab_test' => 'boolean',
        'ab_test_split_percentage' => 'integer',
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
                        'personalization_data',
                        'ab_test_variant'
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

    // A/B Testing methods
    public function isAbTest(): bool
    {
        return $this->is_ab_test === true;
    }

    public function isSubjectLineTest(): bool
    {
        return $this->isAbTest() && $this->ab_test_type === 'subject_line';
    }

    public function isCtaTest(): bool
    {
        return $this->isAbTest() && $this->ab_test_type === 'cta';
    }

    public function isTemplateTest(): bool
    {
        return $this->isAbTest() && $this->ab_test_type === 'template';
    }

    public function isSendTimeTest(): bool
    {
        return $this->isAbTest() && $this->ab_test_type === 'send_time';
    }

    public function hasWinner(): bool
    {
        return !empty($this->ab_test_winner);
    }

    public function getWinningSubject(): string
    {
        if (!$this->isSubjectLineTest()) {
            return $this->subject;
        }

        if ($this->ab_test_winner === 'variant_b') {
            return $this->subject_variant_b ?? $this->subject;
        }

        return $this->subject;
    }

    public function getContentForVariant(string $variant): array
    {
        $content = [
            'subject' => $this->subject,
            'html_content' => $this->html_content,
            'text_content' => $this->text_content,
            'email_template_id' => $this->email_template_id,
            'cta_text' => null,
            'cta_url' => null,
            'cta_color' => 'indigo',
            'scheduled_at' => $this->scheduled_at,
        ];

        if ($variant === 'variant_b' && $this->isAbTest()) {
            switch ($this->ab_test_type) {
                case 'subject_line':
                    $content['subject'] = $this->subject_variant_b ?? $this->subject;
                    break;
                
                case 'cta':
                    $content['cta_text'] = $this->cta_text_variant_b;
                    $content['cta_url'] = $this->cta_url_variant_b;
                    $content['cta_color'] = $this->cta_color_variant_b ?? 'indigo';
                    break;
                
                case 'template':
                    $content['email_template_id'] = $this->email_template_variant_b_id ?? $this->email_template_id;
                    $content['html_content'] = $this->html_content_variant_b ?? $this->html_content;
                    $content['text_content'] = $this->text_content_variant_b ?? $this->text_content;
                    break;
                
                case 'send_time':
                    $content['scheduled_at'] = $this->scheduled_at_variant_b ?? $this->scheduled_at;
                    break;
            }
        }

        return $content;
    }

    public function getCtaForVariant(string $variant): array
    {
        $cta = [
            'text' => 'Learn More',
            'url' => config('app.url'),
            'color' => 'indigo',
        ];

        if ($variant === 'variant_b' && $this->isCtaTest()) {
            $cta['text'] = $this->cta_text_variant_b ?? $cta['text'];
            $cta['url'] = $this->cta_url_variant_b ?? $cta['url'];
            $cta['color'] = $this->cta_color_variant_b ?? $cta['color'];
        }

        return $cta;
    }

    public function selectWinner(string $winner): void
    {
        if (!in_array($winner, ['variant_a', 'variant_b'])) {
            throw new \InvalidArgumentException('Winner must be either "variant_a" or "variant_b"');
        }

        $this->update([
            'ab_test_winner' => $winner,
            'ab_test_winner_selected_at' => now(),
        ]);
    }

    public function getAbTestResults(): array
    {
        if (!$this->isAbTest()) {
            return [];
        }

        return $this->ab_test_results ?? [];
    }

    public function getVariantOpenRate(string $variant): float
    {
        if (!$this->isAbTest()) {
            return 0;
        }

        $results = $this->getAbTestResults();
        if (!isset($results[$variant])) {
            return 0;
        }

        $data = $results[$variant];
        if (empty($data['delivered_count']) || $data['delivered_count'] <= 0) {
            return 0;
        }

        return round(($data['opened_count'] / $data['delivered_count']) * 100, 1);
    }

    public function getVariantClickRate(string $variant): float
    {
        if (!$this->isAbTest()) {
            return 0;
        }

        $results = $this->getAbTestResults();
        if (!isset($results[$variant])) {
            return 0;
        }

        $data = $results[$variant];
        if (empty($data['opened_count']) || $data['opened_count'] <= 0) {
            return 0;
        }

        return round(($data['clicked_count'] / $data['opened_count']) * 100, 1);
    }

    public function updateAbTestResults(): void
    {
        if (!$this->isAbTest()) {
            return;
        }

        try {
            // Check if campaign_contacts table exists
            if (!Schema::hasTable('campaign_contacts')) {
                \Log::warning('campaign_contacts table does not exist for A/B test results', ['campaign_id' => $this->id]);
                return;
            }

            // Fix missing A/B test variant assignments for existing campaigns
            $this->fixMissingAbTestVariants();

            // Calculate variant statistics from campaign contacts using direct DB queries
            $variantAResults = \DB::table('campaign_contacts')
                ->where('campaign_id', $this->id)
                ->where('ab_test_variant', 'variant_a')
                ->selectRaw('
                    COUNT(*) as sent_count,
                    SUM(CASE WHEN delivered_at IS NOT NULL THEN 1 ELSE 0 END) as delivered_count,
                    SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened_count,
                    SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked_count
                ')
                ->first();

            $variantBResults = \DB::table('campaign_contacts')
                ->where('campaign_id', $this->id)
                ->where('ab_test_variant', 'variant_b')
                ->selectRaw('
                    COUNT(*) as sent_count,
                    SUM(CASE WHEN delivered_at IS NOT NULL THEN 1 ELSE 0 END) as delivered_count,
                    SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened_count,
                    SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked_count
                ')
                ->first();

            // Update A/B test results
            $abTestResults = [
                'variant_a' => [
                    'sent_count' => $variantAResults->sent_count ?? 0,
                    'delivered_count' => $variantAResults->delivered_count ?? 0,
                    'opened_count' => $variantAResults->opened_count ?? 0,
                    'clicked_count' => $variantAResults->clicked_count ?? 0,
                ],
                'variant_b' => [
                    'sent_count' => $variantBResults->sent_count ?? 0,
                    'delivered_count' => $variantBResults->delivered_count ?? 0,
                    'opened_count' => $variantBResults->opened_count ?? 0,
                    'clicked_count' => $variantBResults->clicked_count ?? 0,
                ],
            ];

            $this->update(['ab_test_results' => $abTestResults]);

        } catch (\Exception $e) {
            \Log::error('Failed to update A/B test results', [
                'campaign_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $results = [
            'variant_a' => [
                'sent_count' => $variantAResults ? ($variantAResults->sent_count ?? 0) : 0,
                'delivered_count' => $variantAResults ? ($variantAResults->delivered_count ?? 0) : 0,
                'opened_count' => $variantAResults ? ($variantAResults->opened_count ?? 0) : 0,
                'clicked_count' => $variantAResults ? ($variantAResults->clicked_count ?? 0) : 0,
            ],
            'variant_b' => [
                'sent_count' => $variantBResults ? ($variantBResults->sent_count ?? 0) : 0,
                'delivered_count' => $variantBResults ? ($variantBResults->delivered_count ?? 0) : 0,
                'opened_count' => $variantBResults ? ($variantBResults->opened_count ?? 0) : 0,
                'clicked_count' => $variantBResults ? ($variantBResults->clicked_count ?? 0) : 0,
            ],
        ];

        $this->update(['ab_test_results' => $results]);
    }

    /**
     * Fix missing A/B test variant assignments for existing campaigns
     */
    private function fixMissingAbTestVariants(): void
    {
        if (!$this->isAbTest()) {
            return;
        }

        try {
            // Check if campaign_contacts table exists
            if (!Schema::hasTable('campaign_contacts')) {
                \Log::warning('campaign_contacts table does not exist for fixing A/B test variants', ['campaign_id' => $this->id]);
                return;
            }

            // Check if any contacts are missing variant assignments
            $contactsWithoutVariants = $this->contacts()
                ->whereNull('campaign_contacts.ab_test_variant')
                ->get();

            if ($contactsWithoutVariants->isEmpty()) {
                return; // All contacts already have variants assigned
            }

            \Log::info('Fixing missing A/B test variants for campaign', [
                'campaign_id' => $this->id,
                'contacts_without_variants' => $contactsWithoutVariants->count()
            ]);

            // Assign variants to contacts without assignments
            $splitPercentage = $this->ab_test_split_percentage ?? 50;
            $totalContacts = $contactsWithoutVariants->count();
            $variantASampleSize = (int) ceil(($splitPercentage / 100) * $totalContacts);

            $contactsWithoutVariants->each(function ($contact, $index) use ($variantASampleSize) {
                $variant = $index < $variantASampleSize ? 'variant_a' : 'variant_b';
                
                $this->contacts()->updateExistingPivot($contact->id, [
                    'ab_test_variant' => $variant
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to fix missing A/B test variants', [
                'campaign_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
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
        try {
            // Check if campaign_contacts table exists
            if (!Schema::hasTable('campaign_contacts')) {
                \Log::warning('campaign_contacts table does not exist for campaign', ['campaign_id' => $this->id]);
                return;
            }

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

            // Check if email_logs table exists
            $sentFromLogs = 0;
            $deliveredFromLogs = 0;
            $bouncedFromLogs = 0;

            if (Schema::hasTable('email_logs')) {
                // Also get sent count from email_logs to capture emails that were sent but then marked as delivered
                $sentFromLogs = DB::table('email_logs')
                    ->where('campaign_id', $this->id)
                    ->whereNotNull('sent_at')
                    ->count();

                // Get delivered count from email_logs (more accurate than campaign_contacts)
                $deliveredFromLogs = DB::table('email_logs')
                    ->where('campaign_id', $this->id)
                    ->where('status', 'delivered')
                    ->count();

                // Also check bounced emails from email_logs table for more accurate bounced count
                $bouncedFromLogs = DB::table('email_logs')
                    ->where('campaign_id', $this->id)
                    ->where('status', 'bounced')
                    ->count();
            }

            // Check if marketing_contacts table exists
            $unsubscribedCount = 0;
            if (Schema::hasTable('marketing_contacts')) {
                // Get unsubscribed count from marketing_contacts
                $unsubscribedCount = DB::table('campaign_contacts')
                    ->join('marketing_contacts', 'campaign_contacts.marketing_contact_id', '=', 'marketing_contacts.id')
                    ->where('campaign_contacts.campaign_id', $this->id)
                    ->where('marketing_contacts.status', 'unsubscribed')
                    ->count();
            }

            // Use the higher bounced count (from campaign_contacts or email_logs)
            $finalBouncedCount = max($stats->bounced ?? 0, $bouncedFromLogs);

            // Use email_logs data for sent/delivered counts as it's more accurate
            $finalSentCount = max($stats->sent ?? 0, $sentFromLogs);
            $finalDeliveredCount = max($stats->delivered ?? 0, $deliveredFromLogs);

            $previousStats = [
                'total_recipients' => $this->total_recipients,
                'sent_count' => $this->sent_count,
                'delivered_count' => $this->delivered_count,
                'opened_count' => $this->opened_count,
                'clicked_count' => $this->clicked_count,
                'bounced_count' => $this->bounced_count,
                'unsubscribed_count' => $this->unsubscribed_count,
            ];

            $this->update([
                'total_recipients' => $stats->total ?? 0,
                'sent_count' => $finalSentCount,
                'delivered_count' => $finalDeliveredCount,
                'opened_count' => $stats->opened ?? 0,
                'clicked_count' => $stats->clicked ?? 0,
                'bounced_count' => $finalBouncedCount,
                'unsubscribed_count' => $unsubscribedCount,
            ]);

            // Log statistics changes for debugging
            \Log::debug('Campaign statistics updated', [
                'campaign_id' => $this->id,
                'previous' => $previousStats,
                'current' => [
                    'total_recipients' => $this->total_recipients,
                    'sent_count' => $this->sent_count,
                    'delivered_count' => $this->delivered_count,
                    'opened_count' => $this->opened_count,
                    'clicked_count' => $this->clicked_count,
                    'bounced_count' => $this->bounced_count,
                    'unsubscribed_count' => $this->unsubscribed_count,
                ],
                'source_data' => [
                    'campaign_contacts_sent' => $stats->sent ?? 0,
                    'email_logs_sent' => $sentFromLogs,
                    'campaign_contacts_delivered' => $stats->delivered ?? 0,
                    'email_logs_delivered' => $deliveredFromLogs,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update campaign statistics', [
                'campaign_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Set default values to prevent further errors
            $this->update([
                'total_recipients' => 0,
                'sent_count' => 0,
                'delivered_count' => 0,
                'opened_count' => 0,
                'clicked_count' => 0,
                'bounced_count' => 0,
                'unsubscribed_count' => 0,
            ]);
        }
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