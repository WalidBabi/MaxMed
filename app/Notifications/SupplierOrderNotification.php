<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\View;

class SupplierOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
            ->subject('New Order Requires Your Quotation - ' . $this->order->order_number)
            ->view('emails.supplier-order', [
                'order' => $this->order,
                'notifiable' => $notifiable
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'message' => 'New order requires your quotation',
            'action_url' => route('supplier.orders.show', $this->order->id)
        ];
    }

    /**
     * Get formatted list of products
     */
    private function getProductsList(): string
    {
        return $this->order->items->map(function($item) {
            return "- {$item->product->name} (Quantity: {$item->quantity})";
        })->implode("\n");
    }
} 