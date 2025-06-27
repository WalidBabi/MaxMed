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
            // Fix existing order statuses to align with new status system
            
            // 1. Orders that require quotation but are in 'pending' status
            // should be moved to 'awaiting_quotations' if they have no quotations
            // or 'quotations_received' if they have quotations
            
            DB::statement("
                UPDATE orders 
                SET status = 'awaiting_quotations' 
                WHERE status = 'pending' 
                AND requires_quotation = 1 
                AND id NOT IN (
                    SELECT DISTINCT o.id 
                    FROM orders o
                    JOIN quotation_requests qr ON qr.order_id = o.id
                    JOIN supplier_quotations sq ON sq.quotation_request_id = qr.id
                )
            ");
            
            // 2. Orders that require quotation, are in 'pending' status, 
            // but have quotations should be moved to 'quotations_received'
            DB::statement("
                UPDATE orders 
                SET status = 'quotations_received' 
                WHERE status = 'pending' 
                AND requires_quotation = 1 
                AND id IN (
                    SELECT DISTINCT o.id 
                    FROM orders o
                    JOIN quotation_requests qr ON qr.order_id = o.id
                    JOIN supplier_quotations sq ON sq.quotation_request_id = qr.id
                )
            ");
            
            // 3. Orders in 'processing' status should stay as they are for now
            // as they might be actively being processed
            
            echo "Fixed existing order statuses to align with new status system.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only proceed if the requires_quotation column exists
        if (Schema::hasColumn('orders', 'requires_quotation')) {
            // Reverse the changes - put orders back to pending if they were changed
            DB::statement("
                UPDATE orders 
                SET status = 'pending' 
                WHERE status IN ('awaiting_quotations', 'quotations_received')
                AND requires_quotation = 1
            ");
            
            echo "Reverted order statuses back to pending.\n";
        }
    }
};
