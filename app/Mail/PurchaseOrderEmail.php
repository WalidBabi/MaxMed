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
        if ($this->purchaseOrder->attachments && is_array($this->purchaseOrder->attachments)) {
            \Log::info('Purchase Order Email: Found ' . count($this->purchaseOrder->attachments) . ' attachments for PO ' . $this->purchaseOrder->po_number);
            
            foreach ($this->purchaseOrder->attachments as $index => $attachment) {
                // Check if the attachment file exists and attach it
                if (isset($attachment['path']) && Storage::disk('public')->exists($attachment['path'])) {
                    try {
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
                        
                        \Log::info('Purchase Order Email: Successfully attached file: ' . $filename);
                    } catch (\Exception $e) {
                        // Log error but continue with other attachments
                        \Log::warning('Purchase Order Email: Failed to attach file: ' . $attachment['path'] . '. Error: ' . $e->getMessage());
                    }
                } else {
                    \Log::warning('Purchase Order Email: Attachment file not found or path missing: ' . ($attachment['path'] ?? 'no path'));
                }
            }
        } else {
            \Log::info('Purchase Order Email: No attachments found for PO ' . $this->purchaseOrder->po_number);
        }

        return $email;
    }
} 