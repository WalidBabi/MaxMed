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
        // First, modify the column to include both old and new values
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost',
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
        
        // Now update existing statuses to match new ones
        \DB::statement("UPDATE crm_leads SET status = 'fresh_lead' WHERE status = 'new'");
        \DB::statement("UPDATE crm_leads SET status = 'first_call_scheduled' WHERE status = 'contacted'");
        \DB::statement("UPDATE crm_leads SET status = 'quote_requested' WHERE status = 'qualified'");
        \DB::statement("UPDATE crm_leads SET status = 'quote_sent_pending' WHERE status = 'proposal'");
        \DB::statement("UPDATE crm_leads SET status = 'negotiating_terms' WHERE status = 'negotiation'");
        \DB::statement("UPDATE crm_leads SET status = 'deal_won' WHERE status = 'won'");
        \DB::statement("UPDATE crm_leads SET status = 'deal_lost' WHERE status = 'lost'");
        
        // Finally, remove old values from enum
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old status values
        \DB::statement("UPDATE crm_leads SET status = 'new' WHERE status = 'fresh_lead'");
        \DB::statement("UPDATE crm_leads SET status = 'contacted' WHERE status = 'first_call_scheduled'");
        \DB::statement("UPDATE crm_leads SET status = 'qualified' WHERE status = 'quote_requested'");
        \DB::statement("UPDATE crm_leads SET status = 'proposal' WHERE status = 'quote_sent_pending'");
        \DB::statement("UPDATE crm_leads SET status = 'negotiation' WHERE status = 'negotiating_terms'");
        \DB::statement("UPDATE crm_leads SET status = 'won' WHERE status = 'deal_won'");
        \DB::statement("UPDATE crm_leads SET status = 'lost' WHERE status = 'deal_lost'");
        
        Schema::table('crm_leads', function (Blueprint $table) {
            \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
                'new',
                'contacted', 
                'qualified',
                'proposal',
                'negotiation',
                'won',
                'lost'
            ) DEFAULT 'new'");
        });
    }
};
