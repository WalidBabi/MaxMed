<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupplierInquiry;
use Illuminate\Support\Facades\Log;

class UpdateInquiryStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inquiries:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update inquiry statuses based on the new per-product approval logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting inquiry status update...');

        $inquiries = SupplierInquiry::with('quotations')->get();
        $updatedCount = 0;

        foreach ($inquiries as $inquiry) {
            $oldStatus = $inquiry->status;
            $newStatus = $this->calculateInquiryStatus($inquiry);

            if ($oldStatus !== $newStatus) {
                $inquiry->update(['status' => $newStatus]);
                $updatedCount++;

                $this->info("Updated inquiry #{$inquiry->id} ({$inquiry->reference_number}): {$oldStatus} → {$newStatus}");
                
                Log::info("Updated inquiry status", [
                    'inquiry_id' => $inquiry->id,
                    'reference_number' => $inquiry->reference_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);
            }
        }

        $this->info("Completed! Updated {$updatedCount} inquiries.");
        
        return 0;
    }

    /**
     * Calculate the appropriate status for an inquiry based on its quotations
     */
    private function calculateInquiryStatus(SupplierInquiry $inquiry): string
    {
        $allQuotations = $inquiry->quotations;
        
        if ($allQuotations->isEmpty()) {
            return 'in_progress';
        }

        // Count quotations by status
        $totalQuotations = $allQuotations->count();
        $acceptedQuotations = $allQuotations->where('status', 'accepted')->count();
        $rejectedQuotations = $allQuotations->where('status', 'rejected')->count();
        $pendingQuotations = $allQuotations->where('status', 'submitted')->count();

        // Determine inquiry status based on quotation statuses
        if ($acceptedQuotations > 0) {
            if ($acceptedQuotations === $totalQuotations) {
                // All products have approved quotations
                return 'converted';
            } else {
                // Some products have approved quotations, others are pending
                return 'partially_quoted';
            }
        } elseif ($rejectedQuotations === $totalQuotations) {
            // All quotations were rejected
            return 'cancelled';
        } elseif ($pendingQuotations > 0) {
            // Some quotations are still pending
            return 'in_progress';
        }

        return 'in_progress'; // Default status
    }
} 