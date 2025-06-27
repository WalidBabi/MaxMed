<?php

namespace App\Notifications;

use App\Models\SupplierInquiry;
use App\Models\QuotationRequest;
use App\Models\SupplierQuotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $inquiry;
    protected $quotation;

    public function __construct($inquiry, SupplierQuotation $quotation)
    {
        $this->inquiry = $inquiry;
        $this->quotation = $quotation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $inquiryId = $this->inquiry->id;
        $referenceNumber = $this->inquiry instanceof SupplierInquiry 
            ? ($this->inquiry->reference_number ?? 'INQ-' . str_pad($this->inquiry->id, 6, '0', STR_PAD_LEFT))
            : 'QR-' . str_pad($this->inquiry->id, 6, '0', STR_PAD_LEFT);
        
        $url = $this->inquiry instanceof SupplierInquiry 
            ? route('admin.inquiries.show', $inquiryId)
            : route('crm.quotation-requests.show', $inquiryId);

        return (new MailMessage)
            ->subject('🚨 Admin Alert: New Quotation Submitted for Inquiry #' . $referenceNumber)
            ->view('emails.admin.quotation-submitted', [
                'inquiry' => $this->inquiry,
                'quotation' => $this->quotation,
                'url' => $url
            ]);
    }

    public function toArray($notifiable)
    {
        $inquiryId = $this->inquiry->id;
        $referenceNumber = $this->inquiry instanceof SupplierInquiry 
            ? ($this->inquiry->reference_number ?? 'INQ-' . str_pad($this->inquiry->id, 6, '0', STR_PAD_LEFT))
            : 'QR-' . str_pad($this->inquiry->id, 6, '0', STR_PAD_LEFT);
        
        $url = $this->inquiry instanceof SupplierInquiry 
            ? route('admin.inquiries.show', $inquiryId)
            : route('crm.quotation-requests.show', $inquiryId);

        return [
            'inquiry_id' => $inquiryId,
            'quotation_id' => $this->quotation->id,
            'message' => 'New quotation submitted for inquiry #' . $referenceNumber,
            'url' => $url
        ];
    }
} 