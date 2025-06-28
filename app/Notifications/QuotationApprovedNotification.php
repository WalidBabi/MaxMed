<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SupplierQuotation;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class QuotationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $quotation;
    public $purchaseOrder;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupplierQuotation $quotation, PurchaseOrder $purchaseOrder = null)
    {
        $this->quotation = $quotation;
        $this->purchaseOrder = $purchaseOrder;
        
        // Use configured queue connection
        $this->onQueue('notifications');
        $this->delay(Carbon::now()->addSeconds(2));
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
            ->subject('✅ Quotation Approved - ' . Config::get('app.name'))
            ->greeting('Quotation Approved')
            ->line('Congratulations! Your quotation has been approved.');

        if ($this->purchaseOrder) {
            // Include purchase order details if available
            $message->line('**Purchase Order Details:**')
                ->line('- **PO Number:** #' . $this->purchaseOrder->po_number)
                ->line('- **Total Amount:** AED ' . number_format($this->purchaseOrder->total_amount, 2))
                ->line('- **Required Delivery Date:** ' . $this->purchaseOrder->delivery_date_requested->format('F j, Y'))
                ->line('')
                ->action('View Purchase Order', URL::to('/supplier/purchase-orders/' . $this->purchaseOrder->id));
        } 
        // Handle order-based quotations
        elseif ($this->quotation->order_id) {
            $message->line('**Order Details:**')
                ->line('- **Order Number:** #' . $this->quotation->order->order_number)
                ->line('- **Total Amount:** ' . $this->quotation->currency . ' ' . number_format($this->quotation->total_amount, 2))
                ->line('')
                ->action('View Order', URL::to('/supplier/orders/' . $this->quotation->order_id));
        }
        // Handle supplier inquiry quotations
        elseif ($this->quotation->supplier_inquiry_id) {
            $message->line('**Supplier Inquiry Details:**')
                ->line('- **Total Amount:** ' . $this->quotation->currency . ' ' . number_format($this->quotation->total_amount, 2))
                ->line('- **Source:** Supplier Inquiry')
                ->line('')
                ->action('View Inquiry', URL::to('/supplier/inquiries/' . $this->quotation->supplier_inquiry_id));
        }
        // Handle legacy quotation request quotations
        elseif ($this->quotation->quotation_request_id) {
            $message->line('**Quotation Request Details:**')
                ->line('- **Total Amount:** ' . $this->quotation->currency . ' ' . number_format($this->quotation->total_amount, 2))
                ->line('')
                ->action('View Quotation Request', URL::to('/supplier/quotation-requests/' . $this->quotation->quotation_request_id));
        }

        $message->line('Please proceed with order processing and keep us updated on the delivery status.')
            ->line('Thank you for your business!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $data = [
            'type' => 'quotation_approved',
            'quotation_id' => $this->quotation->id,
            'quotation_number' => $this->quotation->quotation_number,
            'total_amount' => $this->quotation->total_amount,
            'currency' => $this->quotation->currency,
            'message' => 'Your quotation has been approved.'
        ];

        // Handle order-based quotations
        if ($this->quotation->order_id) {
            $data['order_id'] = $this->quotation->order_id;
            $data['order_number'] = $this->quotation->order->order_number;
            $data['message'] = 'Your quotation has been approved for order #' . $this->quotation->order->order_number;
        } 
        // Handle supplier inquiry quotations
        elseif ($this->quotation->supplier_inquiry_id) {
            $data['supplier_inquiry_id'] = $this->quotation->supplier_inquiry_id;
            $data['message'] = 'Your quotation has been approved for the supplier inquiry.';
        }
        // Handle legacy quotation request quotations
        elseif ($this->quotation->quotation_request_id) {
            $data['quotation_request_id'] = $this->quotation->quotation_request_id;
            $data['message'] = 'Your quotation has been approved for the quotation request.';
        }

        if ($this->purchaseOrder) {
            $data['purchase_order_id'] = $this->purchaseOrder->id;
            $data['po_number'] = $this->purchaseOrder->po_number;
            $data['delivery_date'] = $this->purchaseOrder->delivery_date_requested->toISOString();
            $data['message'] = 'Your quotation has been approved. PO #' . $this->purchaseOrder->po_number . ' has been created.';
            
            // Add source type information
            if ($this->purchaseOrder->isFromSupplierInquiry()) {
                $data['source_type'] = 'supplier_inquiry';
                $data['message'] = 'Your quotation has been approved for supplier inquiry. PO #' . $this->purchaseOrder->po_number . ' has been created.';
            } elseif ($this->purchaseOrder->hasCustomerOrder()) {
                $data['source_type'] = 'customer_order';
                $data['message'] = 'Your quotation has been approved for customer order. PO #' . $this->purchaseOrder->po_number . ' has been created.';
            }
        }

        return $data;
    }
} 