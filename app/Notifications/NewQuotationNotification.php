<?php

namespace App\Notifications;

use App\Models\SupplierQuotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewQuotationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quotation;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupplierQuotation $quotation)
    {
        $this->quotation = $quotation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->quotation->order;
        $supplier = $this->quotation->supplier;

        return (new MailMessage)
            ->subject("New Quotation Received - Order #{$order->order_number}")
            ->greeting('Hello!')
            ->line("A new quotation has been submitted for Order #{$order->order_number}.")
            ->line("Supplier: {$supplier->name}")
            ->line("Amount: {$this->quotation->currency} " . number_format($this->quotation->total_amount, 2))
            ->action('Review Quotation', route('admin.orders.quotations.index', $order))
            ->line('Please review the quotation and take appropriate action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $order = $this->quotation->order;
        $supplier = $this->quotation->supplier;

        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'quotation_id' => $this->quotation->id,
            'amount' => $this->quotation->total_amount,
            'currency' => $this->quotation->currency,
            'message' => "New quotation received from {$supplier->name} for Order #{$order->order_number}",
            'action_url' => route('admin.orders.quotations.index', $order)
        ];
    }
} 