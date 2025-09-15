<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, array $emailData = [])
    {
        $this->invoice = $invoice;
        $this->emailData = $emailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->emailData['subject'] ?? 
                   ($this->invoice->type === 'proforma' ? 'Proforma Invoice' : 'Invoice') . 
                   ' ' . $this->invoice->invoice_number;

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
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'customMessage' => $this->emailData['message'] ?? null
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Generate PDF and attach it
        $this->invoice->load(['items.product.specifications', 'delivery', 'parentInvoice', 'order.cashReceipts', 'order.delivery', 'payments']);
        
        // Get customer data for company name display
        $customer = \App\Models\Customer::where('name', $this->invoice->customer_name)->first();
        
        $pdf = Pdf::loadView('admin.invoices.pdf', ['invoice' => $this->invoice, 'customer' => $customer]);
        
        $pdfContent = $pdf->output();
        $filename = $this->invoice->invoice_number . '.pdf';

        return [
            Attachment::fromData(fn () => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
} 