<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupplierCategory;
use App\Models\QuotationRequest;
use Illuminate\Support\Facades\DB;

class UpdateSupplierResponseTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suppliers:update-response-times {--supplier-id= : Update specific supplier only} {--category-id= : Update specific category only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update supplier response times based on actual quotation data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting supplier response time update...');

        $query = SupplierCategory::with(['supplier', 'category']);

        // Filter by supplier if specified
        if ($supplierId = $this->option('supplier-id')) {
            $query->where('supplier_id', $supplierId);
            $this->info("Filtering by supplier ID: {$supplierId}");
        }

        // Filter by category if specified
        if ($categoryId = $this->option('category-id')) {
            $query->where('category_id', $categoryId);
            $this->info("Filtering by category ID: {$categoryId}");
        }

        $assignments = $query->get();
        $totalAssignments = $assignments->count();

        if ($totalAssignments === 0) {
            $this->warn('No supplier category assignments found.');
            return 0;
        }

        $this->info("Found {$totalAssignments} supplier category assignments to process.");
        
        $progressBar = $this->output->createProgressBar($totalAssignments);
        $progressBar->start();

        $updatedCount = 0;
        $noDataCount = 0;

        foreach ($assignments as $assignment) {
            $oldResponseTime = $assignment->avg_response_time_hours;
            $newResponseTime = $this->calculateResponseTime($assignment);

            if ($newResponseTime !== null) {
                $assignment->avg_response_time_hours = $newResponseTime;
                $assignment->save();
                $updatedCount++;
            } else {
                $noDataCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Update completed!");
        $this->info("- Updated: {$updatedCount} assignments");
        $this->info("- No data: {$noDataCount} assignments");
        $this->info("- Total processed: {$totalAssignments} assignments");

        return 0;
    }

    /**
     * Calculate response time for a supplier category assignment
     */
    private function calculateResponseTime($assignment)
    {
        // Get quotation requests for this supplier and category
        $quotationRequests = QuotationRequest::where('supplier_id', $assignment->supplier_id)
            ->whereHas('product', function($query) use ($assignment) {
                $query->where('category_id', $assignment->category_id);
            })
            ->whereNotNull('forwarded_at')
            ->whereNotNull('supplier_responded_at')
            ->get();

        if ($quotationRequests->count() === 0) {
            return null;
        }

        $totalResponseTime = 0;
        $validResponses = 0;

        foreach ($quotationRequests as $request) {
            $responseTime = $request->forwarded_at->diffInHours($request->supplier_responded_at);
            
            // Only count reasonable response times (0 to 1 week)
            if ($responseTime >= 0 && $responseTime <= 168) {
                $totalResponseTime += $responseTime;
                $validResponses++;
            }
        }

        if ($validResponses === 0) {
            return null;
        }

        return round($totalResponseTime / $validResponses, 1);
    }
} 