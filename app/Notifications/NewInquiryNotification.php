<?php

namespace App\Notifications;

use App\Models\SupplierInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $inquiry;

    public function __construct(SupplierInquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('supplier.inquiries.show', $this->inquiry->id);

        return (new MailMessage)
            ->subject('New Product Inquiry - Action Required')
            ->view('emails.inquiries.new-inquiry', [
                'inquiry' => $this->inquiry,
                'supplier' => $notifiable,
                'url' => $url
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'inquiry_id' => $this->inquiry->id,
            'message' => 'New product inquiry requires your attention',
            'url' => route('supplier.inquiries.show', $this->inquiry->id)
        ];
    }
} 