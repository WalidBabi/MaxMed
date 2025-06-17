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
    public $deliveryTimeline;
    public $contactName;
    public $contactEmail;
    public $contactPhone;
    public $contactCompany;

    /**
     * Create a new message instance.
     *
     * @param Product $product
     * @param int $quantity
     * @param string|null $size
     * @param string|null $requirements
     * @param string|null $notes
     * @param User|null $user
     * @param string|null $deliveryTimeline
     * @param string|null $contactName
     * @param string|null $contactEmail
     * @param string|null $contactPhone
     * @param string|null $contactCompany
     * @return void
     */
    public function __construct(
        Product $product,
        int $quantity,
        ?string $size,
        ?string $requirements,
        ?string $notes,
        ?User $user,
        ?string $deliveryTimeline = null,
        ?string $contactName = null,
        ?string $contactEmail = null,
        ?string $contactPhone = null,
        ?string $contactCompany = null
    ) {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->size = $size;
        $this->requirements = $requirements;
        $this->notes = $notes;
        $this->user = $user;
        $this->deliveryTimeline = $deliveryTimeline;
        $this->contactName = $contactName;
        $this->contactEmail = $contactEmail;
        $this->contactPhone = $contactPhone;
        $this->contactCompany = $contactCompany;
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