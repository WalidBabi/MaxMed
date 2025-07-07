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

        // Add deliverability headers to avoid promotions tab
        $this->addDeliverabilityHeaders($mail);

        return $mail;
    }

    /**
     * Add headers that improve deliverability and mark emails as important
     */
    private function addDeliverabilityHeaders($mail)
    {
        // Add headers that make email appear as important business communication
        $mail->withSwiftMessage(function ($message) {
            $headers = $message->getHeaders();
            
            // Mark as important business communication
            $headers->addTextHeader('X-Mailer', config('app.name') . ' Business Communication System');
            $headers->addTextHeader('X-Priority', '1'); // High priority (1=High, 3=Normal, 5=Low)
            $headers->addTextHeader('X-MSMail-Priority', 'High');
            $headers->addTextHeader('Importance', 'High');
            
            // Mark as important business communication (not promotional)
            $headers->addTextHeader('X-Auto-Response-Suppress', 'All');
            $headers->addTextHeader('X-Entity-Type', 'Business');
            $headers->addTextHeader('X-Message-Type', 'Business-Important');
            
            // Add proper message categorization for business communications
            $headers->addTextHeader('X-Message-Source', 'MaxMed Business Communication');
            $headers->addTextHeader('X-Business-Type', 'Healthcare-Supplies');
            
            // Add reply-to header for better engagement signals
            $headers->addMailboxHeader('Reply-To', config('mail.campaign_from.address'));
            
            // Add proper List-Unsubscribe header (RFC compliant)
            $unsubscribeUrl = route('email.track.unsubscribe', [
                'token' => base64_encode($this->contact->id . '|' . $this->contact->email . '|' . $this->campaign->id)
            ]);
            $headers->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>');
            $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            
            // Add organization header
            $headers->addTextHeader('Organization', config('app.name'));
            
            // Indicate this is an important business communication
            $headers->addTextHeader('X-Auto-Category', 'business-important');
            $headers->addTextHeader('X-Content-Type', 'business-notification');
            
            // Add Message-ID for better tracking
            $messageId = sprintf('<%s.%s.%s@%s>', 
                $this->campaign->id,
                $this->contact->id,
                time(),
                parse_url(config('app.url'), PHP_URL_HOST)
            );
            $headers->addIdHeader('Message-ID', $messageId);
            
            // Add additional headers to signal importance
            $headers->addTextHeader('X-Importance', 'High');
            $headers->addTextHeader('X-Business-Communication', 'true');
            $headers->addTextHeader('X-Healthcare-Supplies', 'true');
        });
    }
}