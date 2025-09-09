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
            // Handle the attachments data - it might be double-encoded JSON
            $attachments = $this->purchaseOrder->attachments;
            
            // If it's a string (double-encoded), decode it
            if (is_string($attachments)) {
                $attachments = json_decode($attachments, true);
            }
            
            // If it's still a string after first decode, decode again
            if (is_string($attachments)) {
                $attachments = json_decode($attachments, true);
            }
                
            if (is_array($attachments) && !empty($attachments)) {
                foreach ($attachments as $attachment) {
                    // Check if the attachment file exists and attach it using the same method as PO PDF
                    if (isset($attachment['path']) && Storage::disk('public')->exists($attachment['path'])) {
                        $filename = $attachment['filename'] ?? basename($attachment['path']);
                        $fileContent = Storage::disk('public')->get($attachment['path']);
                        $mimeType = $attachment['mime_type'] ?? Storage::disk('public')->mimeType($attachment['path']);
                        
                        $email->attachData(
                            $fileContent,
                            $filename,
                            [
                                'mime' => $mimeType,
                            ]
                        );
                    }
                }
            }
        }

        return $email;
    }
} 