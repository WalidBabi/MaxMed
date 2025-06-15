<?php

namespace App\Console\Commands;

use App\Models\Delivery;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConvertProformaInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maxmed:convert-proforma {--delivery-id= : Specific delivery ID to process} {--dry-run : Show what would be converted without actually converting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert eligible proforma invoices to final invoices based on delivery status and payment terms';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deliveryId = $this->option('delivery-id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No actual conversions will be performed');
        }

        if ($deliveryId) {
            $this->processSpecificDelivery($deliveryId, $dryRun);
        } else {
            $this->processAllEligibleDeliveries($dryRun);
        }

        return 0;
    }

    /**
     * Process a specific delivery
     */
    private function processSpecificDelivery($deliveryId, $dryRun)
    {
        $delivery = Delivery::find($deliveryId);
        
        if (!$delivery) {
            $this->error("❌ Delivery with ID {$deliveryId} not found");
            return;
        }

        $this->info("📦 Processing delivery {$delivery->id} (Status: {$delivery->status})");

        $proformaInvoice = $delivery->getProformaInvoice();
        
        if (!$proformaInvoice) {
            $this->warn("⚠️  No proforma invoice found for delivery {$delivery->id}");
            return;
        }

        $this->info("📄 Found proforma invoice {$proformaInvoice->invoice_number}");
        $this->info("   Payment Terms: {$proformaInvoice->payment_terms}");
        $this->info("   Total Amount: {$proformaInvoice->total_amount} AED");
        $this->info("   Paid Amount: {$proformaInvoice->paid_amount} AED");
        $this->info("   Status: {$proformaInvoice->status}");

        $conversionStatus = $delivery->getFinalInvoiceConversionStatus();
        
        if ($conversionStatus['ready']) {
            $this->info("✅ Delivery is ready for conversion");
            
            if (!$dryRun) {
                try {
                    $delivery->autoConvertToFinalInvoice();
                    $this->info("🎉 Successfully converted proforma invoice to final invoice");
                } catch (\Exception $e) {
                    $this->error("❌ Conversion failed: " . $e->getMessage());
                    Log::error("Manual conversion failed for delivery {$delivery->id}: " . $e->getMessage());
                }
            } else {
                $this->info("🔄 Would convert proforma invoice to final invoice");
            }
        } else {
            $this->warn("⚠️  Delivery not ready for conversion: {$conversionStatus['reason']}");
            
            if (!empty($conversionStatus['details'])) {
                $this->info("Details:");
                foreach ($conversionStatus['details'] as $key => $value) {
                    $this->info("   {$key}: {$value}");
                }
            }
        }
    }

    /**
     * Process all eligible deliveries
     */
    private function processAllEligibleDeliveries($dryRun)
    {
        $this->info("🔍 Scanning for deliveries eligible for proforma-to-final conversion...");

        // Get deliveries that might be eligible
        $deliveries = Delivery::with(['order.proformaInvoice'])
            ->whereIn('status', ['in_transit', 'delivered'])
            ->whereHas('order.proformaInvoice', function($query) {
                $query->where('status', 'confirmed')
                      ->where('type', 'proforma');
            })
            ->get();

        $this->info("📊 Found {$deliveries->count()} deliveries to check");

        $convertedCount = 0;
        $skippedCount = 0;

        foreach ($deliveries as $delivery) {
            $conversionStatus = $delivery->getFinalInvoiceConversionStatus();
            
            if ($conversionStatus['ready']) {
                $proformaInvoice = $delivery->getProformaInvoice();
                $this->info("✅ Delivery {$delivery->id} - Proforma {$proformaInvoice->invoice_number} ready for conversion");
                
                if (!$dryRun) {
                    try {
                        $delivery->autoConvertToFinalInvoice();
                        $convertedCount++;
                        $this->info("   🎉 Converted successfully");
                    } catch (\Exception $e) {
                        $this->error("   ❌ Conversion failed: " . $e->getMessage());
                        Log::error("Batch conversion failed for delivery {$delivery->id}: " . $e->getMessage());
                    }
                } else {
                    $convertedCount++;
                    $this->info("   🔄 Would convert");
                }
            } else {
                $skippedCount++;
                $this->warn("⚠️  Delivery {$delivery->id} skipped: {$conversionStatus['reason']}");
            }
        }

        $this->info("");
        $this->info("📈 Summary:");
        $this->info("   Converted: {$convertedCount}");
        $this->info("   Skipped: {$skippedCount}");
        $this->info("   Total processed: " . ($convertedCount + $skippedCount));

        if ($dryRun) {
            $this->info("");
            $this->info("💡 Run without --dry-run to perform actual conversions");
        }
    }
} 