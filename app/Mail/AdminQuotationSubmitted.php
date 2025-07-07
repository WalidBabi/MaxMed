<?php

namespace App\Mail;

use App\Models\SupplierQuotation;
use App\Models\SupplierInquiry;
use App\Models\QuotationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminQuotationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $inquiry;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct(SupplierQuotation $quotation, $inquiry)
    {
        $this->quotation = $quotation;
        $this->inquiry = $inquiry;
        
        // Generate URL based on inquiry type
        $this->url = $inquiry instanceof SupplierInquiry 
            ? route('admin.inquiries.show', $inquiry->id)
            : route('crm.quotation-requests.show', $inquiry->id);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $referenceNumber = $this->inquiry instanceof SupplierInquiry 
            ? ($this->inquiry->reference_number ?? 'INQ-' . str_pad($this->inquiry->id, 6, '0', STR_PAD_LEFT))
            : 'QR-' . str_pad($this->inquiry->id, 6, '0', STR_PAD_LEFT);

        // Check if this is a PDF-only quotation
        $hasAttachments = $this->quotation->attachments && is_array($this->quotation->attachments) && count($this->quotation->attachments) > 0;
        $hasProductInfo = ($this->quotation->product_id && $this->quotation->product && $this->quotation->product->name) || 
                         ($this->inquiry && $this->inquiry->product_name) || 
                         ($this->inquiry && $this->inquiry->product_description);
        $isPdfOnly = $hasAttachments && !$hasProductInfo && $this->quotation->unit_price == 0;

        $subject = $isPdfOnly 
            ? '📄 PDF Quotation Submitted for ' . $referenceNumber
            : '🚨 Admin Alert: New Quotation Submitted for ' . $referenceNumber;

        return $this->subject($subject)
                    ->view('emails.admin.quotation-submitted')
                    ->with([
                        'inquiry' => $this->inquiry,
                        'quotation' => $this->quotation,
                        'url' => $this->url
                    ]);
    }
} 