<?php

namespace App\Mail;

use App\Models\CashReceipt;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CashReceiptEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $cashReceipt;
    public $customer;
    public $ccEmails;

    /**
     * Create a new message instance.
     */
    public function __construct(CashReceipt $cashReceipt, Customer $customer = null, array $ccEmails = [])
    {
        $this->cashReceipt = $cashReceipt;
        $this->customer = $customer;
        $this->ccEmails = $ccEmails;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject('Cash Receipt ' . $this->cashReceipt->receipt_number . ' from MaxMed')
                      ->view('emails.cash-receipt')
                      ->with([
                          'cashReceipt' => $this->cashReceipt,
                          'customer' => $this->customer
                      ]);

        // Add CC emails if provided
        if (!empty($this->ccEmails)) {
            $email->cc($this->ccEmails);
        }

        // Generate and attach PDF
        $pdf = Pdf::loadView('admin.cash-receipts.pdf', ['cashReceipt' => $this->cashReceipt, 'customer' => $this->customer]);
        $email->attachData(
            $pdf->output(),
            $this->cashReceipt->receipt_number . '.pdf',
            [
                'mime' => 'application/pdf',
            ]
        );

        return $email;
    }
} 