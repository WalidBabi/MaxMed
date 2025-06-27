<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupplierQuotation;
use App\Models\SupplierInquiryResponse;

class UpdateQuotationResponseStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotations:update-response-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update supplier inquiry response status for approved quotations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating supplier inquiry response status for approved quotations...');
        
        // Find all accepted quotations that have supplier inquiries
        $quotations = SupplierQuotation::where('status', 'accepted')
            ->whereNotNull('supplier_inquiry_id')
            ->with(['supplierInquiry', 'supplierInquiryResponse'])
            ->get();

        $updatedCount = 0;
        
        foreach ($quotations as $quotation) {
            $response = null;
            
            // Try to get response from relationship first
            if ($quotation->supplier_inquiry_response_id && $quotation->supplierInquiryResponse) {
                $response = $quotation->supplierInquiryResponse;
            } else {
                // Find response by inquiry and supplier
                $response = SupplierInquiryResponse::where('supplier_inquiry_id', $quotation->supplier_inquiry_id)
                    ->where('user_id', $quotation->supplier_id)
                    ->first();
            }
            
            if ($response && $response->status !== SupplierInquiryResponse::STATUS_ACCEPTED) {
                $response->update(['status' => SupplierInquiryResponse::STATUS_ACCEPTED]);
                $updatedCount++;
                $this->info("Updated response status for quotation #{$quotation->quotation_number}");
            }
        }
        
        $this->info("Successfully updated {$updatedCount} supplier inquiry response(s).");
        
        return 0;
    }
} 