<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            
            // Workflow tracking
            $table->enum('status', [
                'new',              // Just received
                'in_review',        // Being reviewed by CRM
                'converted_to_lead', // Converted to CRM lead
                'converted_to_inquiry', // Converted to quotation request
                'responded',        // Direct response sent
                'closed'           // Closed/resolved
            ])->default('new');
            
            $table->unsignedBigInteger('converted_to_inquiry_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable(); // Admin user handling this
            $table->text('admin_notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('converted_to_inquiry_id')->references('id')->on('quotation_requests')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['subject', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
    }
}; 