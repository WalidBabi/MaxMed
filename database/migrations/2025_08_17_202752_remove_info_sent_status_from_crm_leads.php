<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, move any leads from info_sent to quote_requested
        \DB::statement("UPDATE crm_leads SET status = 'quote_requested' WHERE status = 'info_sent'");
        
        // Remove info_sent from the enum and update the workflow
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'new_inquiry',
            'quote_requested',
            'follow_up_1',
            'follow_up_2', 
            'follow_up_3',
            'quote_sent',
            'negotiating_price',
            'payment_pending',
            'order_confirmed',
            'deal_lost'
        ) DEFAULT 'new_inquiry'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add info_sent back to the enum
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'new_inquiry',
            'info_sent',
            'follow_up_1',
            'follow_up_2', 
            'follow_up_3',
            'quote_requested',
            'quote_sent',
            'negotiating_price',
            'payment_pending',
            'order_confirmed',
            'deal_lost'
        ) DEFAULT 'new_inquiry'");
    }
};