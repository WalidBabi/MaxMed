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
            $subject = $this->campaign->emailTemplate->renderSubject($data);
        } else {
            $template = new EmailTemplate([
                'subject' => $this->campaign->subject,
                'html_content' => $this->campaign->html_content,
                'text_content' => $this->campaign->text_content,
            ]);
            $htmlContent = $template->renderHtmlContent($data);
            $textContent = $template->renderTextContent($data);
            $subject = $template->renderSubject($data);
        }

        // Send the email
        Mail::to($contact->email)->send(new CampaignEmail(
            $subject,
            $textContent,
            $this->campaign,
            $contact,
            $htmlContent
        ));

        // Mark as sent in both email log and campaign contact pivot
        $emailLog->markAsSent();
        
        $this->campaign->contacts()->updateExistingPivot($contact->id, [
            'status' => 'sent',
            'sent_at' => now()
        ]);

        Log::info("Email sent successfully", [
            'campaign_id' => $this->campaign->id,
            'contact_id' => $contact->id,
            'email' => $contact->email
        ]);
    }

    private function generateUnsubscribeToken(MarketingContact $contact): string
    {
        return base64_encode($contact->id . '|' . $contact->email . '|' . time());
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