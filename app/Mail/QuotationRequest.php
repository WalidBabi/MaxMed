<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuotationRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $quantity;
    public $size;
    public $requirements;
    public $notes;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param Product $product
     * @param int $quantity
     * @param string|null $size
     * @param string|null $requirements
     * @param string|null $notes
     * @param User|null $user
     * @return void
     */
    public function __construct(
        Product $product,
        int $quantity,
        ?string $size,
        ?string $requirements,
        ?string $notes,
        ?User $user
    ) {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->size = $size;
        $this->requirements = $requirements;
        $this->notes = $notes;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Quotation Request for ' . $this->product->name)
                    ->view('emails.quotation-request');
    }
} 