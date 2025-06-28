<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class RecalculateInvoiceTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:recalculate-totals {--invoice-id= : Specific invoice ID to recalculate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate invoice totals to include discounts and tax properly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceId = $this->option('invoice-id');
        
        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if (!$invoice) {
                $this->error("Invoice with ID {$invoiceId} not found.");
                return 1;
            }
            
            $this->recalculateInvoice($invoice);
            $this->info("Recalculated totals for invoice {$invoice->invoice_number}");
        } else {
            $invoices = Invoice::with('items')->get();
            $bar = $this->output->createProgressBar($invoices->count());
            $bar->start();
            
            foreach ($invoices as $invoice) {
                $this->recalculateInvoice($invoice);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("Recalculated totals for {$invoices->count()} invoices.");
        }
        
        return 0;
    }
    
    private function recalculateInvoice(Invoice $invoice)
    {
        $oldTotal = $invoice->total_amount;
        
        // Recalculate totals
        $invoice->calculateTotals();
        
        // Refresh the model to get updated values
        $invoice->refresh();
        
        $newTotal = $invoice->total_amount;
        
        if ($oldTotal != $newTotal) {
            Log::info("Invoice {$invoice->id} total updated: {$oldTotal} -> {$newTotal}");
            
            // If there are payments, we need to update the payment status
            if ($invoice->payments()->where('status', 'completed')->exists()) {
                $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
                
                // Determine new payment status
                $newPaymentStatus = $this->determinePaymentStatus($newTotal, $totalPaid);
                
                if ($newPaymentStatus !== $invoice->payment_status) {
                    $invoice->update([
                        'payment_status' => $newPaymentStatus,
                        'paid_at' => $newPaymentStatus === 'paid' ? now() : null
                    ]);
                    
                    Log::info("Invoice {$invoice->id} payment status updated: {$invoice->payment_status} -> {$newPaymentStatus}");
                }
            }
        }
    }
    
    private function determinePaymentStatus($totalAmount, $paidAmount)
    {
        if ($paidAmount <= 0) {
            return 'pending';
        } elseif ($paidAmount >= $totalAmount) {
            return 'paid';
        } else {
            return 'partial';
        }
    }
} 