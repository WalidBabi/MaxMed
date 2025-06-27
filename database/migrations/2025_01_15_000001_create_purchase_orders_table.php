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
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_number')->unique();
                $table->unsignedBigInteger('supplier_id');
                $table->decimal('total_amount', 10, 2);
                $table->enum('status', ['draft', 'sent', 'approved', 'rejected', 'completed', 'cancelled'])->default('draft');
                $table->text('notes')->nullable();
                $table->date('expected_delivery_date')->nullable();
                $table->string('payment_terms')->nullable();
                $table->string('shipping_method')->nullable();
                $table->decimal('shipping_cost', 10, 2)->default(0);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->string('currency')->default('AED');
                $table->string('billing_address')->nullable();
                $table->string('shipping_address')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('supplier_id')->references('id')->on('users')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
}; 