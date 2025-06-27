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
        Schema::create('supplier_invitations', function (Blueprint $table) {
            $table->id();
            
            // Invitation details
            $table->string('email')->index();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('token', 60)->unique();
            
            // Who invited and when
            $table->unsignedBigInteger('invited_by');
            $table->text('custom_message')->nullable();
            
            // Status and tracking
            $table->enum('status', ['pending', 'accepted', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            
            // Link to created user (when accepted)
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['email', 'status']);
            $table->index(['token', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invitations');
    }
}; 