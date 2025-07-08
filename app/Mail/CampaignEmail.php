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

        // Add comprehensive deliverability headers to avoid promotions tab
        $this->addDeliverabilityHeaders($mail);

        return $mail;
    }

    /**
     * Add comprehensive headers that improve deliverability and mark emails as important
     * This helps emails go to the inbox instead of promotions tab
     */
    private function addDeliverabilityHeaders($mail)
    {
        $mail->withSwiftMessage(function ($message) {
            $headers = $message->getHeaders();
            
            // ===== PRIORITY AND IMPORTANCE HEADERS =====
            // Mark as high priority business communication
            $headers->addTextHeader('X-Priority', '1'); // High priority (1=High, 3=Normal, 5=Low)
            $headers->addTextHeader('X-MSMail-Priority', 'High');
            $headers->addTextHeader('Importance', 'High');
            $headers->addTextHeader('X-Importance', 'High');
            
            // ===== BUSINESS COMMUNICATION IDENTIFICATION =====
            // Identify as important business communication, not promotional
            $headers->addTextHeader('X-Mailer', config('app.name') . ' Business Communication System');
            $headers->addTextHeader('X-Entity-Type', 'Business');
            $headers->addTextHeader('X-Message-Type', 'Business-Important');
            $headers->addTextHeader('X-Business-Communication', 'true');
            $headers->addTextHeader('X-Healthcare-Supplies', 'true');
            
            // ===== CONTENT CATEGORIZATION =====
            // Categorize as business notification, not marketing
            $headers->addTextHeader('X-Auto-Category', 'business-important');
            $headers->addTextHeader('X-Content-Type', 'business-notification');
            $headers->addTextHeader('X-Message-Source', 'MaxMed Business Communication');
            $headers->addTextHeader('X-Business-Type', 'Healthcare-Supplies');
            
            // ===== ENGAGEMENT SIGNALS =====
            // Add reply-to header for better engagement signals
            $headers->addMailboxHeader('Reply-To', config('mail.campaign_from.address'));
            
            // ===== RFC COMPLIANT UNSUBSCRIBE HEADERS =====
            // Add proper List-Unsubscribe header (RFC compliant)
            $unsubscribeUrl = route('email.track.unsubscribe', [
                'token' => base64_encode($this->contact->id . '|' . $this->contact->email . '|' . $this->campaign->id)
            ]);
            $headers->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>');
            $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            
            // ===== ORGANIZATION AND IDENTIFICATION =====
            // Add organization header
            $headers->addTextHeader('Organization', config('app.name'));
            
            // Add Message-ID for better tracking and deliverability
            $messageId = sprintf('<%s.%s.%s@%s>', 
                $this->campaign->id,
                $this->contact->id,
                time(),
                parse_url(config('app.url'), PHP_URL_HOST)
            );
            $headers->addIdHeader('Message-ID', $messageId);
            
            // ===== ANTI-SPAM AND DELIVERABILITY HEADERS =====
            // Suppress auto-responses to avoid spam triggers
            $headers->addTextHeader('X-Auto-Response-Suppress', 'All');
            
            // Add Precedence header to indicate business communication
            $headers->addTextHeader('Precedence', 'business');
            
            // Add X-Report-Abuse header for better reputation
            $headers->addTextHeader('X-Report-Abuse', 'Please report abuse to ' . config('mail.campaign_from.address'));
            
            // ===== GMAIL SPECIFIC HEADERS =====
            // Headers that help with Gmail categorization
            $headers->addTextHeader('X-Gmail-Labels', 'Important,Business');
            $headers->addTextHeader('X-Gmail-Category', 'Primary');
            
            // ===== OUTLOOK SPECIFIC HEADERS =====
            // Headers that help with Outlook categorization
            $headers->addTextHeader('X-Message-Delivery', 'Business');
            $headers->addTextHeader('X-Message-Info', 'Business Communication');
            
            // ===== ADDITIONAL BUSINESS SIGNALS =====
            // Signal that this is a legitimate business transaction
            $headers->addTextHeader('X-Business-Transaction', 'true');
            $headers->addTextHeader('X-Legitimate-Business', 'true');
            $headers->addTextHeader('X-Healthcare-Industry', 'true');
            
            // ===== TECHNICAL DELIVERABILITY HEADERS =====
            // Add DKIM and SPF friendly headers
            $headers->addTextHeader('X-Mailer-Version', '1.0');
            $headers->addTextHeader('X-Sender', config('mail.campaign_from.address'));
            $headers->addTextHeader('X-Originating-IP', request()->ip());
            
            // ===== CONTENT FILTERING HEADERS =====
            // Indicate this is not bulk marketing
            $headers->addTextHeader('X-Bulk-Mail', 'false');
            $headers->addTextHeader('X-Marketing-Mail', 'false');
            $headers->addTextHeader('X-Promotional-Mail', 'false');
            
            // ===== CUSTOM BUSINESS HEADERS =====
            // Add custom headers specific to healthcare supplies business
            $headers->addTextHeader('X-MaxMed-Business-Type', 'Healthcare-Supplies');
            $headers->addTextHeader('X-MaxMed-Communication-Type', 'Business-Important');
            $headers->addTextHeader('X-MaxMed-Industry', 'Medical-Equipment');
        });
    }
}