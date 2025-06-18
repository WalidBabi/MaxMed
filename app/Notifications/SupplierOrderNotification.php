<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierOrderNotification extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
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
            'type' => 'supplier_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number ?? 'N/A',
            'customer_name' => $this->order->customer_name ?? 'Unknown Customer',
            'total_amount' => $this->order->total_amount ?? 0,
            'status' => $this->order->status ?? 'pending',
            'created_at' => $this->order->created_at->toISOString(),
            'title' => 'New supplier order',
            'message' => 'New order #' . ($this->order->order_number ?? 'N/A') . ' received from ' . ($this->order->customer_name ?? 'Unknown Customer')
        ];
    }
} 