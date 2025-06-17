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
            
            // Performance tracking
            $table->decimal('avg_response_time_hours', 8, 2)->default(24.00); // Average response time in hours
            $table->decimal('quotation_win_rate', 5, 2)->default(0.00); // Win rate percentage
            $table->integer('total_quotations', false, true)->default(0); // Total quotations sent
            $table->integer('won_quotations', false, true)->default(0); // Won quotations
            $table->decimal('avg_customer_rating', 3, 2)->default(5.00); // Average rating from customers
            
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
            $table->index(['status', 'quotation_win_rate']);
            $table->index(['avg_response_time_hours']);
            
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