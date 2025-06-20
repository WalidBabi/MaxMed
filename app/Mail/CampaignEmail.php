<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\MarketingContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $htmlContent;
    public $textContent;
    public $campaign;
    public $contact;

    public function __construct(
        string $subject,
        string $textContent,
        Campaign $campaign,
        MarketingContact $contact,
        string $htmlContent = null
    ) {
        $this->subject = $subject;
        $this->htmlContent = $htmlContent;
        $this->textContent = $textContent;
        $this->campaign = $campaign;
        $this->contact = $contact;
    }

    public function build()
    {
        $mail = $this->subject($this->subject)
                     ->text('emails.campaign-text', [
                         'content' => $this->textContent,
                         'contact' => $this->contact,
                         'campaign' => $this->campaign
                     ])
                     ->with([
                         'contact' => $this->contact,
                         'campaign' => $this->campaign,
                         'unsubscribe_url' => route('marketing.unsubscribe', [
                             'token' => base64_encode($this->contact->id . '|' . $this->contact->email . '|' . time())
                         ])
                     ]);

        // Only add HTML content if it exists
        if ($this->htmlContent) {
            $mail->html($this->htmlContent);
        }

        return $mail;
    }
} 