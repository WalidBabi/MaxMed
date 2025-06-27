<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_deals')) {
            Schema::create('crm_deals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('crm_leads')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('value', 15, 2);
                $table->enum('stage', ['prospecting', 'qualification', 'proposal', 'negotiation', 'closed_won', 'closed_lost'])->default('prospecting');
                $table->integer('probability')->default(0); // 0-100%
                $table->date('expected_close_date')->nullable();
                $table->date('actual_close_date')->nullable();
                $table->text('close_reason')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['stage', 'expected_close_date']);
                $table->index(['assigned_to', 'stage']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deals');
    }
}; 