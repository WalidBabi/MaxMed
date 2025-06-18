<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationRequestNotification extends Notification
{
    use Queueable;

    public $quotationRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($quotationRequest)
    {
        $this->quotationRequest = $quotationRequest;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        // Get customer info from user relationship or related contact submission
        $customerName = 'Guest Customer';
        $customerEmail = 'guest@quotation.request';
        
        if ($this->quotationRequest->user) {
            $customerName = $this->quotationRequest->user->name;
            $customerEmail = $this->quotationRequest->user->email;
        } elseif ($this->quotationRequest->relatedContactSubmission) {
            $customerName = $this->quotationRequest->relatedContactSubmission->name;
            $customerEmail = $this->quotationRequest->relatedContactSubmission->email;
        }

        return [
            'type' => 'quotation_request',
            'quotation_request_id' => $this->quotationRequest->id,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'product_name' => $this->quotationRequest->product->name ?? 'Unknown Product',
            'quantity' => $this->quotationRequest->quantity,
            'delivery_timeline' => $this->quotationRequest->delivery_timeline,
            'status' => $this->quotationRequest->status,
            'created_at' => $this->quotationRequest->created_at->toISOString(),
            'title' => 'New quotation request',
            'message' => 'New quotation request from ' . $customerName . ' for ' . ($this->quotationRequest->product->name ?? 'Unknown Product') . ' (Qty: ' . $this->quotationRequest->quantity . ')'
        ];
    }
} 