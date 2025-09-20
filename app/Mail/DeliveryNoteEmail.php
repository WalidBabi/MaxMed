<?php

namespace App\Mail;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryNoteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $delivery;
    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct(Delivery $delivery, array $emailData = [])
    {
        $this->delivery = $delivery;
        $this->emailData = $emailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->emailData['subject'] ?? 
                   'Delivery Note - ' . $this->delivery->delivery_number;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.delivery-note',
            with: [
                'delivery' => $this->delivery,
                'customMessage' => $this->emailData['message'] ?? null
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Load relationships for PDF generation
        $this->delivery->load(['order.items.product', 'order.user']);
        
        // Get customer data for company name display
        $customer = \App\Models\Customer::where('name', $this->delivery->order->customer_name ?? $this->delivery->order->user->name)->first();
        
        $pdf = Pdf::loadView('admin.deliveries.pdf', [
            'delivery' => $this->delivery,
            'customer' => $customer
        ]);
        
        $pdfContent = $pdf->output();
        $filename = $this->delivery->delivery_number . '_delivery_note.pdf';

        return [
            Attachment::fromData(fn () => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
