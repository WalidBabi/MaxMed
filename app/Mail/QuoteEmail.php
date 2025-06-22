<?php

namespace App\Mail;

use App\Models\Quote;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    public $customer;
    public $ccEmails;

    /**
     * Create a new message instance.
     */
    public function __construct(Quote $quote, Customer $customer = null, array $ccEmails = [])
    {
        $this->quote = $quote;
        $this->customer = $customer;
        $this->ccEmails = $ccEmails;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject('Quote ' . $this->quote->quote_number . ' from MaxMed')
                      ->view('emails.quote')
                      ->with([
                          'quote' => $this->quote,
                          'customer' => $this->customer
                      ]);

        // Add CC emails if provided
        if (!empty($this->ccEmails)) {
            $email->cc($this->ccEmails);
        }

        // Generate and attach PDF
        $pdf = Pdf::loadView('admin.quotes.pdf', ['quote' => $this->quote, 'customer' => $this->customer]);
        $email->attachData(
            $pdf->output(),
            $this->quote->quote_number . '.pdf',
            [
                'mime' => 'application/pdf',
            ]
        );

        return $email;
    }
} 