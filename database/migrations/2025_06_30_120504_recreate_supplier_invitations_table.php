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
        // Drop the existing table if it has issues
        Schema::dropIfExists('supplier_invitations');
        
        // Recreate the table with the correct structure
        Schema::create('supplier_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('contact_name');
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->string('token', 64)->unique();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('invited_by')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
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