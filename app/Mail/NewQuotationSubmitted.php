<?php

namespace App\Mail;

use App\Models\SupplierQuotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewQuotationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;

    /**
     * Create a new message instance.
     */
    public function __construct(SupplierQuotation $quotation)
    {
        $this->quotation = $quotation;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Quotation Submitted - ' . $this->quotation->quotation_number)
                    ->markdown('emails.quotations.submitted');
    }
} 