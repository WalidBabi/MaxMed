<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all existing quotations that have product_id set
        $quotations = DB::table('supplier_quotations')
            ->whereNotNull('product_id')
            ->where('product_id', '!=', 0)
            ->get();

        foreach ($quotations as $quotation) {
            // Create a quotation item for each existing quotation
            DB::table('supplier_quotation_items')->insert([
                'supplier_quotation_id' => $quotation->id,
                'supplier_inquiry_item_id' => null, // We don't have this mapping for old quotations
                'product_id' => $quotation->product_id,
                'product_name' => null, // Will be filled from product if available
                'product_description' => $quotation->description,
                'unit_price' => $quotation->unit_price,
                'currency' => $quotation->currency ?? 'AED',
                'shipping_cost' => $quotation->shipping_cost,
                'size' => $quotation->size,
                'notes' => $quotation->notes,
                'quantity' => 1, // Default quantity for old quotations
                'attachments' => $quotation->attachments,
                'sort_order' => 1,
                'created_at' => $quotation->created_at,
                'updated_at' => $quotation->updated_at,
            ]);
        }

        echo "Migrated " . $quotations->count() . " existing quotations to new structure.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear all quotation items
        DB::table('supplier_quotation_items')->truncate();
    }
};
