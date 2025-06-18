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
        return [
            'type' => 'quotation_request',
            'quotation_request_id' => $this->quotationRequest->id,
            'customer_name' => $this->quotationRequest->name,
            'customer_email' => $this->quotationRequest->email,
            'product_name' => $this->quotationRequest->product->name ?? 'Unknown Product',
            'quantity' => $this->quotationRequest->quantity,
            'created_at' => $this->quotationRequest->created_at->toISOString(),
            'title' => 'New quotation request',
            'message' => 'New quotation request from ' . $this->quotationRequest->name . ' for ' . ($this->quotationRequest->product->name ?? 'Unknown Product')
        ];
    }
} 