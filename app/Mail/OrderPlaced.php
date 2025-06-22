<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderPlaced extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
        
        // Use configured queue connection
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(1));
    }

    public function build()
    {
        return $this->markdown('emails.orders.placed')
                    ->subject('New Order Placed - ' . $this->order->order_number);
    }
} 