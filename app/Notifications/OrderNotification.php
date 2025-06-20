<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $type)
    {
        $this->order = $order;
        $this->type = $type;
        
        // Use Redis notifications queue
        $this->onConnection('redis');
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
        $subject = $this->getSubject();
        $greeting = $this->getGreeting();
        $message = $this->getMessage();
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->line('**Order Details:**')
            ->line('- **Order Number:** #' . $this->order->order_number)
            ->line('- **Customer:** ' . $this->order->getCustomerName())
            ->line('- **Total Amount:** $' . number_format($this->order->total_amount, 2))
            ->line('- **Status:** ' . ucfirst($this->order->status))
            ->line('- **Date:** ' . $this->order->created_at->format('F j, Y \a\t g:i A'))
            ->action('View Order Details', url('/admin/orders/' . $this->order->id))
            ->line('Please take appropriate action based on the order status.')
            ->salutation('Best regards,  
' . config('app.name') . ' Admin System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->getCustomerName(),
            'total_amount' => $this->order->total_amount,
            'order_status' => $this->order->status,
            'event_type' => $this->type,
            'created_at' => $this->order->created_at->toISOString(),
            'title' => $this->getTitle(),
            'message' => $this->getShortMessage()
        ];
    }

    /**
     * Get the email subject based on notification type
     */
    private function getSubject(): string
    {
        $emoji = $this->getEmoji();
        
        return match($this->type) {
            'placed' => $emoji . ' New Order Placed - ' . config('app.name'),
            'status_changed' => $emoji . ' Order Status Updated - ' . config('app.name'),
            'cancelled' => $emoji . ' Order Cancelled - ' . config('app.name'),
            'shipped' => $emoji . ' Order Shipped - ' . config('app.name'),
            'delivered' => $emoji . ' Order Delivered - ' . config('app.name'),
            default => $emoji . ' Order Update - ' . config('app.name')
        };
    }

    /**
     * Get the email greeting based on notification type
     */
    private function getGreeting(): string
    {
        return match($this->type) {
            'placed' => 'New Order Received',
            'status_changed' => 'Order Status Update',
            'cancelled' => 'Order Cancellation Alert',
            'shipped' => 'Order Shipping Notification',
            'delivered' => 'Order Delivery Confirmation',
            default => 'Order Update'
        };
    }

    /**
     * Get the detailed message based on notification type
     */
    private function getMessage(): string
    {
        return match($this->type) {
            'placed' => 'A new order has been placed and requires processing. Please review the order details and take necessary action.',
            'status_changed' => 'An order status has been updated. Please review the current status and ensure proper workflow continuation.',
            'cancelled' => 'An order has been cancelled. Please review the cancellation and take any necessary follow-up actions.',
            'shipped' => 'An order has been shipped to the customer. Please monitor delivery progress and customer satisfaction.',
            'delivered' => 'An order has been successfully delivered to the customer. Please follow up for feedback if needed.',
            default => 'An order has been updated and may require your attention.'
        };
    }

    /**
     * Get the notification title for database storage
     */
    private function getTitle(): string
    {
        return match($this->type) {
            'placed' => 'New order placed',
            'status_changed' => 'Order status updated',
            'cancelled' => 'Order cancelled',
            'shipped' => 'Order shipped',
            'delivered' => 'Order delivered',
            default => 'Order update'
        };
    }

    /**
     * Get short message for notifications list
     */
    private function getShortMessage(): string
    {
        return match($this->type) {
            'placed' => 'Order #' . $this->order->order_number . ' placed by ' . $this->order->getCustomerName(),
            'status_changed' => 'Order #' . $this->order->order_number . ' status changed to ' . $this->order->status,
            'cancelled' => 'Order #' . $this->order->order_number . ' has been cancelled',
            'shipped' => 'Order #' . $this->order->order_number . ' has been shipped',
            'delivered' => 'Order #' . $this->order->order_number . ' has been delivered',
            default => 'Order #' . $this->order->order_number . ' has been updated'
        };
    }

    /**
     * Get emoji based on notification type
     */
    private function getEmoji(): string
    {
        return match($this->type) {
            'placed' => '🛒',
            'status_changed' => '📋',
            'cancelled' => '❌',
            'shipped' => '🚛',
            'delivered' => '✅',
            default => '📦'
        };
    }
} 