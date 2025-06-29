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
        Campaign $campaign,
        MarketingContact $contact,
        string $subject,
        array $emailContent
    ) {
        $this->campaign = $campaign;
        $this->contact = $contact;
        $this->subject = $subject;
        $this->htmlContent = $emailContent['html'] ?? null;
        $this->textContent = $emailContent['text'] ?? '';
    }

    public function build()
    {
        // Generate unsubscribe URL with campaign tracking
        $unsubscribeUrl = route('email.track.unsubscribe', [
            'token' => base64_encode($this->contact->id . '|' . $this->contact->email . '|' . $this->campaign->id)
        ]);

        $mail = $this->subject($this->subject)
                     ->text('emails.campaign-text', [
                         'content' => $this->textContent,
                         'contact' => $this->contact,
                         'campaign' => $this->campaign,
                         'unsubscribe_url' => $unsubscribeUrl
                     ])
                     ->with([
                         'contact' => $this->contact,
                         'campaign' => $this->campaign,
                         'unsubscribe_url' => $unsubscribeUrl
                     ]);

        // Use the HTML content that already has tracking applied
        if ($this->htmlContent) {
            $mail->html($this->htmlContent);
        }

        // Use campaign mailer and from address
        $mail->mailer('campaign')
             ->from(config('mail.campaign_from.address'), config('mail.campaign_from.name'));

        return $mail;
    }
} 