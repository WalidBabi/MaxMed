<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseOrder;
    public $ccEmails;

    /**
     * Create a new message instance.
     */
    public function __construct(PurchaseOrder $purchaseOrder, array $ccEmails = [])
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->ccEmails = $ccEmails;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject('Purchase Order ' . $this->purchaseOrder->po_number . ' from MaxMed')
                      ->view('emails.purchase-order')
                      ->with([
                          'purchaseOrder' => $this->purchaseOrder,
                          'supplierName' => $this->purchaseOrder->supplier_name
                      ]);

        // Add CC emails if provided
        if (!empty($this->ccEmails)) {
            $email->cc($this->ccEmails);
        }

        // Generate and attach PDF
        $pdf = Pdf::loadView('admin.purchase-orders.pdf', ['purchaseOrder' => $this->purchaseOrder]);
        $email->attachData(
            $pdf->output(),
            $this->purchaseOrder->po_number . '.pdf',
            [
                'mime' => 'application/pdf',
            ]
        );

        // Attach all uploaded attachments if available
        if ($this->purchaseOrder->attachments) {
            $attachments = is_array($this->purchaseOrder->attachments) 
                ? $this->purchaseOrder->attachments 
                : json_decode($this->purchaseOrder->attachments, true);
                
            if (is_array($attachments)) {
                foreach ($attachments as $attachment) {
                    // Check if the attachment file exists and attach it
                    if (isset($attachment['path']) && Storage::disk('public')->exists($attachment['path'])) {
                        $filename = $attachment['filename'] ?? basename($attachment['path']);
                        $email->attachFromStorageDisk('public', $attachment['path'], $filename);
                    }
                }
            }
        }

        return $email;
    }
} 