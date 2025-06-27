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
        Schema::create('supplier_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('category_id');
            
            // Supplier capabilities for this category
            $table->enum('status', ['active', 'inactive', 'pending_approval'])->default('active');
            $table->decimal('minimum_order_value', 10, 2)->nullable(); // Minimum order value for this category
            $table->integer('lead_time_days')->nullable(); // Standard lead time for this category
            $table->text('notes')->nullable(); // Special notes or capabilities
            $table->decimal('commission_rate', 5, 2)->nullable(); // Commission rate for this category (if applicable)
            
            // Audit trail
            $table->unsignedBigInteger('assigned_by')->nullable(); // Who assigned this category
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('last_quotation_at')->nullable(); // Last time they provided a quotation
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['supplier_id', 'status']);
            $table->index(['category_id', 'status']);
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['supplier_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_categories');
    }
}; 