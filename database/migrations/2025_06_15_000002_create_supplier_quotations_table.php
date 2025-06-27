<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('supplier_quotations')) {
            Schema::create('supplier_quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quotation_number')->unique(); // SQ-000001
                $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax', 10, 2)->default(0);
                $table->decimal('shipping', 10, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->string('currency', 3)->default('AED');
                $table->date('quotation_date');
                $table->date('valid_until');
                $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
                $table->text('terms_conditions')->nullable();
                $table->text('notes')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();

                // Add indexes for better performance
                $table->index(['status', 'created_at']);
                $table->index(['supplier_id', 'status']);
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
} 