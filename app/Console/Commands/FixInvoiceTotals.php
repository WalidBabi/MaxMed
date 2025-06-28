<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class FixInvoiceTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:fix-totals {--invoice-id= : Fix specific invoice by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoice totals by recalculating total_amount to include item-level discounts';

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
            
            $this->fixInvoice($invoice);
            $this->info("Fixed invoice {$invoice->invoice_number} (ID: {$invoice->id})");
        } else {
            $invoices = Invoice::with('items')->get();
            $this->info("Found {$invoices->count()} invoices to process...");
            
            $bar = $this->output->createProgressBar($invoices->count());
            $bar->start();
            
            $fixedCount = 0;
            foreach ($invoices as $invoice) {
                $oldTotal = $invoice->total_amount;
                $this->fixInvoice($invoice);
                $invoice->refresh();
                
                if ($oldTotal != $invoice->total_amount) {
                    $fixedCount++;
                    $this->line("\nFixed invoice {$invoice->invoice_number}: {$oldTotal} → {$invoice->total_amount}");
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("Fixed {$fixedCount} invoices with incorrect totals.");
        }
        
        return 0;
    }
    
    private function fixInvoice(Invoice $invoice)
    {
        // Calculate the correct totals
        $subTotal = $invoice->items->sum(function($item) {
            return $item->quantity * $item->unit_price;
        });
        
        $itemDiscounts = $invoice->items->sum('calculated_discount_amount');
        $invoiceDiscount = $invoice->discount_amount ?? 0;
        $totalDiscount = $itemDiscounts + $invoiceDiscount;
        
        $totalAfterDiscount = $subTotal - $totalDiscount;
        $taxAmount = $invoice->tax_amount ?? 0;
        $finalTotal = $totalAfterDiscount + $taxAmount;
        
        // Update the invoice with correct totals
        $invoice->update([
            'subtotal' => $subTotal,
            'total_amount' => $finalTotal
        ]);
        
        Log::info("Fixed invoice {$invoice->id} totals: subtotal={$subTotal}, total_discount={$totalDiscount}, final_total={$finalTotal}");
    }
}
