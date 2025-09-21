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
        // Load relationships for PDF generation with specifications
        $this->delivery->load([
            'order.items.product.specifications', 
            'order.user'
        ]);
        
        // Get customer data for company name display
        $customer = null;
        
        if ($this->delivery->order) {
            // First try to use the order's built-in customer relationship
            if ($this->delivery->order->customer_id) {
                $customer = \App\Models\Customer::find($this->delivery->order->customer_id);
            }
            
            // If no direct customer relationship, try the order's getCustomerInfo method
            if (!$customer && method_exists($this->delivery->order, 'getCustomerInfo')) {
                $customer = $this->delivery->order->getCustomerInfo();
            }
            
            // If still no customer found, try by user relationship
            if (!$customer && $this->delivery->order->user) {
                $customer = \App\Models\Customer::where('user_id', $this->delivery->order->user->id)->first();
                
                // If still not found, try by email
                if (!$customer && $this->delivery->order->user->email) {
                    $customer = \App\Models\Customer::where('email', $this->delivery->order->user->email)->first();
                }
                
                // Last resort: try by user name
                if (!$customer && $this->delivery->order->user->name) {
                    $customer = \App\Models\Customer::where('name', $this->delivery->order->user->name)->first();
                }
            }
        }
        
        $pdf = Pdf::loadView('admin.deliveries.pdf', [
            'delivery' => $this->delivery,
            'customer' => $customer,
            'authorizedUser' => auth()->check() ? auth()->user() : null
        ]);
        
        $pdfContent = $pdf->output();
        $filename = $this->delivery->delivery_number . '_delivery_note.pdf';

        return [
            Attachment::fromData(fn () => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
