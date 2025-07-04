<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\MarketingContact;
use App\Mail\CampaignEmail;
use App\Notifications\CampaignStatusNotification;
use App\Services\CampaignMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $batchSize = 100; // Increased from 50 for better performance
    
    // Queue optimization for huge campaigns
    public $timeout = 600;      // 10 minutes timeout for large batches
    public $tries = 3;          // Retry failed jobs 3 times
    public $maxExceptions = 3;  // Max exceptions before failing
    public $backoff = [60, 120, 300]; // Exponential backoff: 1min, 2min, 5min

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        
        // Use dedicated queue for campaigns
        $this->onQueue('campaigns');
    }

    public function handle()
    {
        try {
            Log::info("Starting campaign send", ['campaign_id' => $this->campaign->id]);

            // Validate campaign has required content - improved logic for A/B testing
            $hasContent = false;
            
            if ($this->campaign->isAbTest()) {
                // For A/B testing, check if variants have content or templates
                $variantData = $this->campaign->ab_test_variant_data;
                if (is_string($variantData)) {
                    $variantData = json_decode($variantData, true) ?? [];
                }
                
                if ($this->campaign->ab_test_type === 'template') {
                    // For template A/B tests, check if variants have valid templates
                    $variantA = $variantData['variant_a'] ?? [];
                    $variantB = $variantData['variant_b'] ?? [];
                    
                    $hasTemplateA = !empty($variantA['email_template_id']) || !empty($variantA['text_content']);
                    $hasTemplateB = !empty($variantB['email_template_id']) || !empty($variantB['text_content']);
                    
                    $hasContent = $hasTemplateA || $hasTemplateB;
                } else {
                    // For other A/B test types, campaign should have base content
                    $hasContent = !empty($this->campaign->text_content) || !empty($this->campaign->html_content) || $this->campaign->emailTemplate;
                }
            } else {
                // For regular campaigns, check standard content
                $hasContent = !empty($this->campaign->text_content) || !empty($this->campaign->html_content) || $this->campaign->emailTemplate;
            }
            
            if (!$hasContent) {
                throw new \Exception('Campaign has no content or template to send');
            }

            // Get all contacts for this campaign that haven't been sent yet
            $contacts = $this->campaign->contacts()
                ->wherePivot('status', 'pending')
                ->with(['campaigns'])
                ->get();

            Log::info("Campaign contact retrieval", [
                'campaign_id' => $this->campaign->id,
                'is_ab_test' => $this->campaign->isAbTest(),
                'ab_test_type' => $this->campaign->ab_test_type,
                'total_contacts_in_campaign' => $this->campaign->contacts()->count(),
                'pending_contacts_found' => $contacts->count(),
                'variant_data' => $this->campaign->ab_test_variant_data
            ]);

            if ($contacts->isEmpty()) {
                Log::warning("No pending contacts found for campaign", [
                    'campaign_id' => $this->campaign->id,
                    'total_contacts' => $this->campaign->contacts()->count(),
                    'contact_statuses' => $this->campaign->contacts()
                        ->selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status')
                        ->toArray()
                ]);
                $this->campaign->markAsSent();
                return;
            }

            $totalSent = 0;
            $totalFailed = 0;

            // For A/B testing, assign variants to contacts first
            if ($this->campaign->isAbTest()) {
                $this->assignAbTestVariants($contacts);
            }

            // Process contacts in batches with memory optimization
            $contacts->chunk($this->batchSize)->each(function ($batch) use (&$totalSent, &$totalFailed) {
                foreach ($batch as $contact) {
                    try {
                        $sent = $this->sendEmailToContact($contact);
                        if ($sent) {
                        $totalSent++;
                        } else {
                            $totalFailed++;
                        }
                    } catch (\Exception $e) {
                        $totalFailed++;
                        Log::error("Failed to send email to contact", [
                            'campaign_id' => $this->campaign->id,
                            'contact_id' => $contact->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                // Memory cleanup after each batch
                gc_collect_cycles();
            });

            // Update campaign statistics
            $this->campaign->updateStatistics();
            $this->campaign->markAsSent();

            // Notify campaign creator
            if ($this->campaign->creator) {
                $this->campaign->creator->notify(new CampaignStatusNotification($this->campaign, 'sent'));
            }

            Log::info("Campaign send completed", [
                'campaign_id' => $this->campaign->id,
                'total_sent' => $totalSent,
                'total_failed' => $totalFailed
            ]);

        } catch (\Throwable $e) {
            Log::error("Campaign send job failed", [
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage(),
                'exception_type' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Mark campaign as failed/draft so it can be retried
            $this->campaign->update(['status' => 'draft']);
            throw $e;
        }
    }

    private function sendEmailToContact(MarketingContact $contact)
    {
        // Prepare subject first - handle A/B testing
        $subject = $this->getSubjectForContact($contact);
        
        // If campaign doesn't have subject, get it from template or use default
        if (empty($subject)) {
            if ($this->campaign->emailTemplate) {
                $subject = $this->campaign->emailTemplate->subject;
            } else {
                $subject = 'MaxMed Campaign - ' . $this->campaign->name;
            }
        }
        
        // Create email log entry
        $emailLog = EmailLog::create([
            'campaign_id' => $this->campaign->id,
            'marketing_contact_id' => $contact->id,
            'email' => $contact->email,
            'subject' => $subject,
            'type' => 'campaign',
            'status' => 'pending'
        ]);

        // Update campaign contact pivot status
        $this->campaign->contacts()
                      ->updateExistingPivot($contact->id, [
                          'status' => 'pending',
                          'sent_at' => null,
                          'delivered_at' => null,
                      ]);

        try {
            // Build email content (HTML or text) with tracking
            $emailContent = $this->buildEmailContentWithTracking($contact, $emailLog);
            
            // Use campaign mail service to send email
            $campaignMailService = new CampaignMailService();
            $sent = $campaignMailService->sendCampaignEmail(
                $this->campaign,
                $contact,
                $subject,
                $emailContent
            );

            if ($sent) {
                // Mark as sent in email log
                $emailLog->markAsSent();
                
                // Update campaign contact pivot to sent status
                $this->campaign->contacts()
                              ->updateExistingPivot($contact->id, [
                                  'status' => 'sent',
                                  'sent_at' => now(),
                              ]);

                // Update campaign statistics to capture sent count
                $this->campaign->updateStatistics();

                // For development: Auto-mark as delivered since we don't have delivery webhooks
                // In production, this would be updated via webhooks from your email provider
                $emailLog->markAsDelivered();
                
                // Update campaign contact pivot to delivered (keeping sent_at timestamp)
                $this->campaign->contacts()
                              ->updateExistingPivot($contact->id, [
                                  'status' => 'delivered',
                                  'delivered_at' => now(),
                              ]);

                Log::info("Campaign email sent successfully", [
                    'campaign_id' => $this->campaign->id,
                    'contact_id' => $contact->id,
                    'email' => $contact->email
                ]);

                return true;
            } else {
                throw new \Exception('Campaign mail service failed to send email');
            }

        } catch (\Exception $e) {
            // Mark as failed
            $emailLog->markAsFailed($e->getMessage());
            
            $this->campaign->contacts()
                          ->updateExistingPivot($contact->id, [
                              'status' => 'failed'
                          ]);

            Log::error("Failed to send campaign email", [
                'campaign_id' => $this->campaign->id,
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    private function buildEmailContentWithTracking(MarketingContact $contact, EmailLog $emailLog): array
    {
        // Prepare personalization data
        $data = [
            'contact' => $contact,
            'campaign' => $this->campaign,
            'company_name' => config('app.name'),
            'current_date' => now()->format('Y-m-d'),
            'current_year' => now()->year,
            'unsubscribe_url' => route('marketing.unsubscribe', ['token' => $this->generateUnsubscribeToken($contact)]),
        ];

        // Get A/B test variant for this contact if applicable
        $variant = $this->getContactVariant($contact);
        
        // Get campaign content based on A/B test variant
        $campaignContent = $this->getCampaignContentForVariant($variant);

        // Render email content
        if ($campaignContent['template']) {
            $htmlContent = $campaignContent['template']->renderHtmlContent($data);
            $textContent = $campaignContent['template']->renderTextContent($data);
        } else {
            $template = new EmailTemplate([
                'subject' => $campaignContent['subject'],
                'html_content' => $campaignContent['html_content'],
                'text_content' => $campaignContent['text_content'],
            ]);
            $htmlContent = $template->renderHtmlContent($data);
            $textContent = $template->renderTextContent($data);
        }

        // Auto-generate HTML from text if no HTML content exists
        if (empty($htmlContent) && !empty($textContent)) {
            $bannerImage = $campaignContent['template']?->banner_image;
            $htmlContent = $this->generateHtmlFromText($textContent, $data, $bannerImage);
        }

        // Add A/B test specific content (like CTA buttons)
        if ($this->campaign->isAbTest() && !empty($campaignContent['cta_data'])) {
            $htmlContent = $this->addCtaToHtmlContent($htmlContent, $campaignContent['cta_data']);
        }

        // Add tracking to HTML content
        if ($htmlContent) {
            $trackingService = new \App\Services\EmailTrackingService();
            $htmlContent = $trackingService->processHtmlContentForTracking(
                $htmlContent, 
                $this->campaign, 
                $contact, 
                $emailLog
            );
        }

        Log::debug("Built email content for variant", [
            'campaign_id' => $this->campaign->id,
            'contact_id' => $contact->id,
            'variant' => $variant,
            'has_html' => !empty($htmlContent),
            'has_text' => !empty($textContent)
        ]);

        return [
            'html' => $htmlContent,
            'text' => $textContent,
        ];
    }
    
    private function buildEmailContent(MarketingContact $contact): array
    {
        // Prepare personalization data
        $data = [
            'contact' => $contact,
            'campaign' => $this->campaign,
            'company_name' => config('app.name'),
            'current_date' => now()->format('Y-m-d'),
            'current_year' => now()->year,
            'unsubscribe_url' => route('marketing.unsubscribe', ['token' => $this->generateUnsubscribeToken($contact)]),
        ];

        // Render email content
        if ($this->campaign->emailTemplate) {
            $htmlContent = $this->campaign->emailTemplate->renderHtmlContent($data);
            $textContent = $this->campaign->emailTemplate->renderTextContent($data);
        } else {
            $template = new EmailTemplate([
                'subject' => $this->campaign->subject,
                'html_content' => $this->campaign->html_content,
                'text_content' => $this->campaign->text_content,
            ]);
            $htmlContent = $template->renderHtmlContent($data);
            $textContent = $template->renderTextContent($data);
        }

        return [
            'html' => $htmlContent,
            'text' => $textContent,
        ];
    }

    private function generateUnsubscribeToken(MarketingContact $contact): string
    {
        return base64_encode($contact->id . '|' . $contact->email . '|' . time());
    }

    /**
     * Generate basic HTML from text content for tracking purposes
     */
    private function generateHtmlFromText(string $textContent, array $data, string $bannerImage = null): string
    {
        // Apply personalization to text content
        $processedText = $textContent;
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $processedText = str_replace('{{' . $key . '}}', $value, $processedText);
            }
        }

        // Convert text to basic HTML
        $htmlContent = '<html><body>';
        
        // Add banner image if provided
        if ($bannerImage) {
            $bannerUrl = asset('storage/' . $bannerImage);
            $htmlContent .= '<div style="text-align: center; margin-bottom: 30px;"><img src="' . $bannerUrl . '" alt="Company Banner" style="max-width: 100%; height: auto; border-radius: 8px;"></div>';
        }
        
        // Convert line breaks to paragraphs
        $paragraphs = explode("\n\n", $processedText);
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                // Auto-convert emails to trackable mailto links
                $paragraph = preg_replace(
                    '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/',
                    '<a href="mailto:$1">$1</a>',
                    $paragraph
                );
                
                // Auto-convert websites to trackable links
                $paragraph = preg_replace(
                    '/(?<!\w)(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?)\b/i',
                    '<a href="https://$1">$1</a>',
                    $paragraph
                );
                
                // Auto-convert phone numbers to trackable tel links
                $paragraph = preg_replace(
                    '/(\+?[\d\s\-\(\)]{10,})/',
                    '<a href="tel:$1">$1</a>',
                    $paragraph
                );
                
                // Convert single line breaks to <br> tags
                $paragraph = nl2br($paragraph);
                $htmlContent .= '<p>' . $paragraph . '</p>';
            }
        }
        
        // Add unsubscribe link
        if (isset($data['unsubscribe_url'])) {
            $htmlContent .= '<hr><p style="font-size: 12px; color: #666;">';
            $htmlContent .= '<a href="' . $data['unsubscribe_url'] . '">Unsubscribe from these emails</a>';
            $htmlContent .= '</p>';
        }
        
        $htmlContent .= '</body></html>';
        
        return $htmlContent;
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SendCampaignJob failed completely", [
            'campaign_id' => $this->campaign->id,
            'error' => $exception->getMessage(),
            'exception_type' => get_class($exception)
        ]);

        // Revert campaign status
        $this->campaign->update(['status' => 'draft']);
    }

    /**
     * Assign A/B test variants to contacts
     */
    private function assignAbTestVariants($contacts)
    {
        if (!$this->campaign->isAbTest()) {
            return;
        }

        Log::info("Assigning A/B test variants", [
            'campaign_id' => $this->campaign->id,
            'total_contacts' => $contacts->count(),
            'split_percentage' => $this->campaign->ab_test_split_percentage
        ]);

        $contacts->each(function ($contact, $index) {
            // Determine variant based on split percentage
            $splitPercentage = $this->campaign->ab_test_split_percentage;
            $totalContacts = $this->campaign->total_recipients;
            $variantASampleSize = (int) ceil(($splitPercentage / 100) * $totalContacts);
            
            // Assign variant A to first X contacts, variant B to the rest
            $variant = $index < $variantASampleSize ? 'variant_a' : 'variant_b';
            
            // Update the pivot table with the variant assignment
            $this->campaign->contacts()->updateExistingPivot($contact->id, [
                'ab_test_variant' => $variant
            ]);

            Log::debug("Assigned A/B test variant", [
                'campaign_id' => $this->campaign->id,
                'contact_id' => $contact->id,
                'contact_index' => $index,
                'variant' => $variant
            ]);
        });
    }

    /**
     * Improve subject line to avoid spam filters and promotions tab
     */
    private function getSubjectForContact(MarketingContact $contact): string
    {
        $originalSubject = '';
        
        // Handle A/B testing subjects
        if ($this->campaign->isAbTest() && $this->campaign->isSubjectLineTest()) {
            $variant = $this->getContactVariant($contact);
            $originalSubject = $variant === 'A' ? $this->campaign->subject : $this->campaign->subject_variant_b;
        } else {
            $originalSubject = $this->campaign->subject;
        }

        // If no subject, get from template or create default
        if (empty($originalSubject)) {
            if ($this->campaign->emailTemplate) {
                $originalSubject = $this->campaign->emailTemplate->subject;
            } else {
                $originalSubject = 'Business Update from ' . config('app.name');
            }
        }

        // Apply deliverability improvements to subject line
        return $this->optimizeSubjectForDeliverability($originalSubject, $contact);
    }

    /**
     * Optimize subject line for better deliverability
     */
    private function optimizeSubjectForDeliverability(string $subject, MarketingContact $contact): string
    {
        // Remove spam trigger words and replace with business-friendly alternatives
        $spamWords = [
            'FREE' => 'Complimentary',
            'URGENT' => 'Time-Sensitive',
            'LIMITED TIME' => 'Available Now',
            'SALE' => 'Offer',
            'DISCOUNT' => 'Special Pricing',
            'AMAZING' => 'Notable',
            'INCREDIBLE' => 'Remarkable',
            'SAVE MONEY' => 'Cost-Effective',
            'CLICK HERE' => 'Learn More',
            'ACT NOW' => 'Review Now',
            'WINNER' => 'Selected',
            'CONGRATULATIONS' => 'Notice',
        ];

        foreach ($spamWords as $spam => $replacement) {
            $subject = str_ireplace($spam, $replacement, $subject);
        }

        // Make subject more business-focused and personal
        $businessPrefixes = [
            'Business Update: ',
            'Important Notice: ',
            'Product Information: ',
            'Service Update: ',
            'Professional Notice: ',
        ];

        // If subject doesn't start with business language, make it more professional
        $startsWithBusiness = false;
        foreach ($businessPrefixes as $prefix) {
            if (stripos($subject, rtrim($prefix, ': ')) === 0) {
                $startsWithBusiness = true;
                break;
            }
        }

        if (!$startsWithBusiness && !empty($contact->company)) {
            // Personalize with company name for better engagement
            $subject = 'Business Communication for ' . $contact->company . ': ' . $subject;
        } elseif (!$startsWithBusiness) {
            // Add professional prefix
            $subject = 'Professional Update: ' . $subject;
        }

        // Ensure subject is not too long (50-60 characters is optimal)
        if (strlen($subject) > 60) {
            $subject = substr($subject, 0, 57) . '...';
        }

        // Personalize if possible
        if (!empty($contact->first_name) && strlen($subject) < 50) {
            $subject = $contact->first_name . ', ' . lcfirst($subject);
        }

        return trim($subject);
    }

    /**
     * Get the A/B test variant assigned to a contact
     */
    private function getContactVariant(MarketingContact $contact): string
    {
        if (!$this->campaign->isAbTest()) {
            return 'variant_a'; // Default variant for non-A/B campaigns
        }

        // Get the assigned variant for this contact
        $pivotData = $this->campaign->contacts()
            ->where('marketing_contacts.id', $contact->id)
            ->first();

        if ($pivotData && $pivotData->pivot->ab_test_variant) {
            return $pivotData->pivot->ab_test_variant;
        }

        // Fallback to variant A if no assignment found
        return 'variant_a';
    }

    /**
     * Get campaign content based on A/B test variant
     */
    private function getCampaignContentForVariant(string $variant): array
    {
        if (!$this->campaign->isAbTest()) {
            return [
                'template' => $this->campaign->emailTemplate,
                'subject' => $this->campaign->subject,
                'html_content' => $this->campaign->html_content,
                'text_content' => $this->campaign->text_content,
                'cta_data' => null
            ];
        }

        // Ensure variant data is properly decoded from JSON
        $variantData = $this->campaign->ab_test_variant_data;
        if (is_string($variantData)) {
            $variantData = json_decode($variantData, true) ?? [];
        } elseif (!is_array($variantData)) {
            $variantData = [];
        }
        
        $abTestType = $this->campaign->ab_test_type;

        Log::debug("Getting content for variant", [
            'campaign_id' => $this->campaign->id,
            'variant' => $variant,
            'ab_test_type' => $abTestType,
            'variant_data' => $variantData
        ]);

        switch ($abTestType) {
            case 'template':
                return $this->getTemplateVariantContent($variant, $variantData);
                
            case 'cta':
                return $this->getCtaVariantContent($variant, $variantData);
                
            case 'subject_line':
            case 'send_time':
            default:
                // For subject line and send time tests, content remains the same
                return [
                    'template' => $this->campaign->emailTemplate,
                    'subject' => $this->campaign->subject,
                    'html_content' => $this->campaign->html_content,
                    'text_content' => $this->campaign->text_content,
                    'cta_data' => null
                ];
        }
    }

    /**
     * Get template variant content
     */
    private function getTemplateVariantContent(string $variant, array $variantData): array
    {
        if ($variant === 'variant_b') {
            $variantBData = $variantData['variant_b'] ?? [];
            $templateId = $variantBData['email_template_id'] ?? null;
            $customContent = $variantBData['text_content'] ?? null;
            
            if ($templateId) {
                $template = EmailTemplate::find($templateId);
                if ($template) {
                    return [
                        'template' => $template,
                        'subject' => $template->subject,
                        'html_content' => $template->html_content,
                        'text_content' => $customContent ?: $template->text_content,
                        'cta_data' => null
                    ];
                }
            }
            
            // Fallback to custom content if template not found
            if ($customContent) {
                return [
                    'template' => null,
                    'subject' => $this->campaign->subject,
                    'html_content' => null,
                    'text_content' => $customContent,
                    'cta_data' => null
                ];
            }
        } else {
            // Variant A
            $variantAData = $variantData['variant_a'] ?? [];
            $templateId = $variantAData['email_template_id'] ?? null;
            $customContent = $variantAData['text_content'] ?? null;
            
            if ($templateId) {
                $template = EmailTemplate::find($templateId);
                if ($template) {
                    return [
                        'template' => $template,
                        'subject' => $template->subject,
                        'html_content' => $template->html_content,
                        'text_content' => $customContent ?: $template->text_content,
                        'cta_data' => null
                    ];
                }
            }
            
            // Fallback to custom content if template not found
            if ($customContent) {
                return [
                    'template' => null,
                    'subject' => $this->campaign->subject,
                    'html_content' => null,
                    'text_content' => $customContent,
                    'cta_data' => null
                ];
            }
        }

        // Final fallback to campaign default content
        return [
            'template' => $this->campaign->emailTemplate,
            'subject' => $this->campaign->subject,
            'html_content' => $this->campaign->html_content,
            'text_content' => $this->campaign->text_content,
            'cta_data' => null
        ];
    }

    /**
     * Get CTA variant content
     */
    private function getCtaVariantContent(string $variant, array $variantData): array
    {
        $ctaData = null;
        
        if ($variant === 'variant_b') {
            $variantBData = $variantData['variant_b'] ?? [];
            $ctaData = [
                'text' => $variantBData['cta_text'] ?? null,
                'url' => $variantBData['cta_url'] ?? null,
                'color' => $variantBData['cta_color'] ?? 'indigo'
            ];
        } else {
            // Variant A
            $variantAData = $variantData['variant_a'] ?? [];
            $ctaData = [
                'text' => $variantAData['cta_text'] ?? null,
                'url' => $variantAData['cta_url'] ?? null,
                'color' => $variantAData['cta_color'] ?? 'indigo'
            ];
        }

        return [
            'template' => $this->campaign->emailTemplate,
            'subject' => $this->campaign->subject,
            'html_content' => $this->campaign->html_content,
            'text_content' => $this->campaign->text_content,
            'cta_data' => $ctaData
        ];
    }

    /**
     * Add CTA button to HTML content
     */
    private function addCtaToHtmlContent(string $htmlContent, array $ctaData): string
    {
        if (empty($ctaData['text']) || empty($ctaData['url'])) {
            return $htmlContent;
        }

        $ctaButton = $this->generateCtaButton($ctaData);
        
        // Try to insert before closing body tag, or append if not found
        if (strpos($htmlContent, '</body>') !== false) {
            $htmlContent = str_replace('</body>', $ctaButton . '</body>', $htmlContent);
        } else {
            $htmlContent .= $ctaButton;
        }

        return $htmlContent;
    }

    /**
     * Generate CTA button HTML
     */
    private function generateCtaButton(array $ctaData): string
    {
        $colors = [
            'indigo' => ['bg' => '#4F46E5', 'hover' => '#4338CA'],
            'green' => ['bg' => '#059669', 'hover' => '#047857'],
            'orange' => ['bg' => '#EA580C', 'hover' => '#C2410C'],
            'red' => ['bg' => '#DC2626', 'hover' => '#B91C1C'],
            'purple' => ['bg' => '#7C3AED', 'hover' => '#6D28D9']
        ];

        $color = $colors[$ctaData['color']] ?? $colors['indigo'];

        return '
        <div style="text-align: center; margin: 30px 0;">
            <a href="' . htmlspecialchars($ctaData['url']) . '" 
               style="display: inline-block; 
                      background-color: ' . $color['bg'] . '; 
                      color: white; 
                      padding: 12px 24px; 
                      text-decoration: none; 
                      border-radius: 6px; 
                      font-weight: 600;
                      font-size: 16px;
                      border: none;
                      cursor: pointer;"
               onmouseover="this.style.backgroundColor=\'' . $color['hover'] . '\'"
               onmouseout="this.style.backgroundColor=\'' . $color['bg'] . '\'">
                ' . htmlspecialchars($ctaData['text']) . '
            </a>
        </div>';
    }
} 