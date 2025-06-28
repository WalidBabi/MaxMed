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
        // Only proceed if the requires_quotation column exists
        if (Schema::hasColumn('orders', 'requires_quotation')) {
            // Check if the order_id column exists in supplier_quotations table
            if (Schema::hasColumn('supplier_quotations', 'order_id')) {
                // Fix existing order statuses to align with new status system
                
                // 1. Orders that require quotation but are in 'pending' status
                // should be moved to 'quotations_received' if they have no quotations
                DB::statement("
                    UPDATE orders
                    SET status = 'quotations_received'
                    WHERE status = 'pending'
                    AND requires_quotation = 1
                    AND quotation_status = 'pending'
                ");

                // 2. Orders that require quotation, are in 'pending' status, 
                // but have quotations should be moved to 'quotations_received'
                DB::statement("
                    UPDATE orders o
                    INNER JOIN supplier_quotations sq ON o.id = sq.order_id
                    SET o.status = 'quotations_received'
                    WHERE o.status = 'pending'
                    AND o.requires_quotation = 1
                ");
                
                // 3. Orders with approved quotations should be moved to 'processing'
                DB::statement("
                    UPDATE orders o
                    INNER JOIN supplier_quotations sq ON o.id = sq.order_id
                    SET o.status = 'processing'
                    WHERE o.status IN ('quotations_received')
                    AND sq.status = 'approved'
                ");
                
                echo "Fixed existing order statuses to align with new status system.\n";
            } else {
                echo "Skipping order status fix - order_id column not yet available in supplier_quotations table.\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only proceed if the requires_quotation column exists
        if (Schema::hasColumn('orders', 'requires_quotation')) {
            // Check if the order_id column exists in supplier_quotations table
            if (Schema::hasColumn('supplier_quotations', 'order_id')) {
                // Reverse the changes - put orders back to pending if they were changed
                DB::statement("
                    UPDATE orders 
                    SET status = 'pending' 
                    WHERE status IN ('quotations_received')
                    AND requires_quotation = 1
                ");
                
                echo "Reverted order statuses back to pending.\n";
            } else {
                echo "Skipping order status revert - order_id column not yet available in supplier_quotations table.\n";
            }
        }
    }
};
