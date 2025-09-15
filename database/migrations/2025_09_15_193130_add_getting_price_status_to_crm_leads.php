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
        // Add 'getting_price' to the status enum
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'new_inquiry',
            'quote_requested', 
            'getting_price',
            'quote_sent',
            'follow_up_1',
            'follow_up_2',
            'follow_up_3',
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
        // Remove 'getting_price' from the status enum
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'new_inquiry',
            'quote_requested',
            'quote_sent',
            'follow_up_1',
            'follow_up_2',
            'follow_up_3',
            'negotiating_price',
            'payment_pending',
            'order_confirmed',
            'deal_lost'
        ) DEFAULT 'new_inquiry'");
    }
};