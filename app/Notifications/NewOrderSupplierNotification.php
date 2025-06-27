<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class NewOrderSupplierNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        
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
        return (new MailMessage)
            ->subject('🛍️ New Order in Your Category - ' . config('app.name'))
            ->greeting('New Order Available')
            ->line('A new order has been placed in a category you are assigned to. Please review the order details below.')
            ->line('**Order Details:**')
            ->line('- **Order Number:** #' . $this->order->order_number)
            ->line('- **Total Amount:** AED ' . number_format($this->order->total_amount, 2))
            ->line('- **Status:** ' . ucfirst($this->order->status))
            ->line('- **Date:** ' . $this->order->created_at->format('F j, Y \a\t g:i A'))
            ->action('View Order Details', url('/supplier/orders/' . $this->order->id))
            ->line('Please review this order and take appropriate action.')
            ->salutation('Best regards,  
' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_order_supplier',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
            'order_status' => $this->order->status,
            'created_at' => $this->order->created_at->toISOString(),
            'title' => 'New order in your category',
            'message' => 'New order #' . $this->order->order_number . ' has been placed in your category'
        ];
    }
} 