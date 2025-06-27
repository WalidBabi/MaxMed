<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SupplierQuotation;

class QuotationRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $quotation;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupplierQuotation $quotation)
    {
        $this->quotation = $quotation;
        
        // Use configured queue connection
        $this->onQueue('notifications');
        $this->delay(now()->addSeconds(2));
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('❌ Quotation Not Accepted - ' . config('app.name'))
            ->greeting('Quotation Update')
            ->line('We regret to inform you that your quotation has not been accepted at this time.');

        if ($this->quotation->rejection_reason) {
            $message->line('**Reason:**')
                ->line($this->quotation->rejection_reason);
        }

        return $message
            ->line('You may submit a revised quotation if you would like to be reconsidered.')
            ->action('Submit New Quotation', url('/supplier/quotations/create/' . $this->quotation->order_id))
            ->line('If you have any questions, please contact our procurement team.')
            ->salutation('Best regards,  
' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'quotation_rejected',
            'quotation_id' => $this->quotation->id,
            'order_id' => $this->quotation->order_id,
            'rejection_reason' => $this->quotation->rejection_reason,
            'created_at' => now()->toISOString(),
            'title' => 'Quotation not accepted',
            'message' => 'Your quotation for order #' . $this->quotation->order->order_number . ' was not accepted'
        ];
    }
} 