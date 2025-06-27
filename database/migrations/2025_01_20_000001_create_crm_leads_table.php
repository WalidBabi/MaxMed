<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_leads')) {
            Schema::create('crm_leads', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->string('mobile')->nullable();
                $table->string('phone')->nullable();
                $table->string('company_name');
                $table->string('job_title')->nullable();
                $table->text('company_address')->nullable();
                $table->enum('status', ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'])->default('new');
                $table->enum('source', ['website', 'linkedin', 'email', 'phone', 'whatsapp', 'on_site_visit', 'referral', 'trade_show', 'google_ads', 'other'])->default('website');
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
                $table->decimal('estimated_value', 15, 2)->nullable();
                $table->text('notes')->nullable();
                $table->date('expected_close_date')->nullable();
                $table->timestamp('last_contacted_at')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['status', 'created_at']);
                $table->index(['assigned_to', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
}; 