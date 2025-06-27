<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $supplierEmail;
    public $supplierName;
    public $invitationToken;
    public $companyName;
    public $invitedByName;
    public $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $supplierEmail,
        string $supplierName,
        string $invitationToken,
        string $companyName = '',
        string $invitedByName = '',
        string $customMessage = ''
    ) {
        $this->supplierEmail = $supplierEmail;
        $this->supplierName = $supplierName;
        $this->invitationToken = $invitationToken;
        $this->companyName = $companyName;
        $this->invitedByName = $invitedByName;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation to Join MaxMed as a Supplier Partner',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supplier-invitation',
            with: [
                'supplierEmail' => $this->supplierEmail,
                'supplierName' => $this->supplierName,
                'invitationToken' => $this->invitationToken,
                'companyName' => $this->companyName,
                'invitedByName' => $this->invitedByName,
                'customMessage' => $this->customMessage,
                'registrationUrl' => route('supplier.register', ['token' => $this->invitationToken]),
                'expiresAt' => now()->addDays(7)->format('M j, Y'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 