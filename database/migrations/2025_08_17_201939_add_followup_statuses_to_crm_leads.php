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
        // Add the new follow-up statuses to the existing enum
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the follow-up statuses and revert to previous enum
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'new_inquiry',
            'info_sent',
            'quote_requested',
            'quote_sent',
            'negotiating_price',
            'payment_pending',
            'order_confirmed',
            'deal_lost'
        ) DEFAULT 'new_inquiry'");
    }
};