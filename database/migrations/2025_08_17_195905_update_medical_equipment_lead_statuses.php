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
        // First, expand the enum to include both old and new values
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'fresh_lead', 'first_call_scheduled', 'needs_follow_up', 'demo_scheduled', 
            'quote_requested', 'quote_sent_pending', 'negotiating_terms', 'contract_review', 
            'ready_to_close', 'deal_won', 'deal_lost', 'nurturing',
            'new_inquiry', 'info_sent', 'quote_sent', 'negotiating_price', 'payment_pending', 'order_confirmed'
        ) DEFAULT 'new_inquiry'");
        
        // Now update existing statuses to match medical equipment workflow
        \DB::statement("UPDATE crm_leads SET status = 'new_inquiry' WHERE status = 'fresh_lead'");
        \DB::statement("UPDATE crm_leads SET status = 'info_sent' WHERE status = 'first_call_scheduled'");
        \DB::statement("UPDATE crm_leads SET status = 'info_sent' WHERE status = 'needs_follow_up'");
        \DB::statement("UPDATE crm_leads SET status = 'info_sent' WHERE status = 'demo_scheduled'");
        \DB::statement("UPDATE crm_leads SET status = 'quote_sent' WHERE status = 'quote_sent_pending'");
        \DB::statement("UPDATE crm_leads SET status = 'negotiating_price' WHERE status = 'negotiating_terms'");
        \DB::statement("UPDATE crm_leads SET status = 'payment_pending' WHERE status = 'contract_review'");
        \DB::statement("UPDATE crm_leads SET status = 'order_confirmed' WHERE status = 'ready_to_close'");
        \DB::statement("UPDATE crm_leads SET status = 'order_confirmed' WHERE status = 'deal_won'");
        \DB::statement("UPDATE crm_leads SET status = 'new_inquiry' WHERE status = 'nurturing'");
        
        // Finally, update the enum column to only include medical equipment trading statuses
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous statuses
        \DB::statement("UPDATE crm_leads SET status = 'fresh_lead' WHERE status = 'new_inquiry'");
        \DB::statement("UPDATE crm_leads SET status = 'first_call_scheduled' WHERE status = 'info_sent'");
        \DB::statement("UPDATE crm_leads SET status = 'quote_requested' WHERE status = 'quote_requested'");
        \DB::statement("UPDATE crm_leads SET status = 'quote_sent_pending' WHERE status = 'quote_sent'");
        \DB::statement("UPDATE crm_leads SET status = 'negotiating_terms' WHERE status = 'negotiating_price'");
        \DB::statement("UPDATE crm_leads SET status = 'contract_review' WHERE status = 'payment_pending'");
        \DB::statement("UPDATE crm_leads SET status = 'deal_won' WHERE status = 'order_confirmed'");
        \DB::statement("UPDATE crm_leads SET status = 'deal_lost' WHERE status = 'deal_lost'");
        
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'fresh_lead',
            'first_call_scheduled', 
            'needs_follow_up',
            'demo_scheduled',
            'quote_requested',
            'quote_sent_pending',
            'negotiating_terms',
            'contract_review',
            'ready_to_close',
            'deal_won',
            'deal_lost',
            'nurturing'
        ) DEFAULT 'fresh_lead'");
    }
};