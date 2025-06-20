<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\MarketingContact;
use App\Mail\CampaignEmail;
use App\Notifications\CampaignStatusNotification;
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

            // Get all contacts for this campaign that haven't been sent yet
            $contacts = $this->campaign->contacts()
                ->wherePivot('status', 'pending')
                ->with(['campaigns'])
                ->get();

            if ($contacts->isEmpty()) {
                Log::warning("No pending contacts found for campaign", ['campaign_id' => $this->campaign->id]);
                $this->campaign->markAsSent();
                return;
            }

            $totalSent = 0;
            $totalFailed = 0;

            // Process contacts in batches with memory optimization
            $contacts->chunk($this->batchSize)->each(function ($batch) use (&$totalSent, &$totalFailed) {
                foreach ($batch as $contact) {
                    try {
                        $this->sendEmailToContact($contact);
                        $totalSent++;
                        
                        // Memory cleanup for large campaigns
                        if ($totalSent % 500 === 0) {
                            gc_collect_cycles();
                            Log::info("Memory check", [
                                'campaign_id' => $this->campaign->id,
                                'sent_so_far' => $totalSent,
                                'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
                            ]);
                        }
                        
                    } catch (\Exception $e) {
                        Log::error("Failed to send email to contact", [
                            'campaign_id' => $this->campaign->id,
                            'contact_id' => $contact->id,
                            'error' => $e->getMessage()
                        ]);
                        $totalFailed++;
                        
                        // Mark contact as failed
                        $this->campaign->contacts()->updateExistingPivot($contact->id, [
                            'status' => 'failed'
                        ]);
                    }
                }
                
                // Adaptive delay based on batch size for huge campaigns
                $delay = $this->campaign->total_recipients > 1000 ? 1 : 2;
                if ($batch->count() == $this->batchSize) {
                    sleep($delay);
                }
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

        } catch (\Exception $e) {
            Log::error("Campaign send job failed", [
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage()
            ]);
            
            // Mark campaign as failed/draft so it can be retried
            $this->campaign->update(['status' => 'draft']);
            throw $e;
        }
    }

    private function sendEmailToContact(MarketingContact $contact)
    {
        // Prepare subject first
        $subject = $this->campaign->subject;
        
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
            
            // Send the email using Laravel's Mail facade
            Mail::to($contact->email)->send(new CampaignEmail(
                $this->campaign,
                $contact,
                $subject,
                $emailContent
            ));

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

        // Auto-generate HTML from text if no HTML content exists
        if (empty($htmlContent) && !empty($textContent)) {
            $bannerImage = $this->campaign->emailTemplate?->banner_image;
            $htmlContent = $this->generateHtmlFromText($textContent, $data, $bannerImage);
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

    public function failed(\Exception $exception)
    {
        Log::error("SendCampaignJob failed completely", [
            'campaign_id' => $this->campaign->id,
            'error' => $exception->getMessage()
        ]);

        // Revert campaign status
        $this->campaign->update(['status' => 'draft']);
    }
} 