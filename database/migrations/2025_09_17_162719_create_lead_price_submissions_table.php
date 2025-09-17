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
        Schema::create('lead_price_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_lead_id')->constrained('crm_leads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->string('currency', 3)->default('AED');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            $table->index(['crm_lead_id', 'status']);
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_price_submissions');
    }
};
