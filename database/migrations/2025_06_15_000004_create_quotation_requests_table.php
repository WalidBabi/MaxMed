<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('quotation_requests')) {
            Schema::create('quotation_requests', function (Blueprint $table) {
                $table->id();
                $table->string('request_number')->unique(); // QR-000001
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
                $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
                $table->string('product_name')->nullable(); // For unlisted products
                $table->text('product_description')->nullable(); // For unlisted products
                $table->integer('quantity');
                $table->text('specifications')->nullable();
                $table->text('special_requirements')->nullable();
                $table->date('delivery_timeline')->nullable();
                $table->enum('status', ['new', 'assigned', 'quoted', 'accepted', 'rejected', 'cancelled'])->default('new');
                $table->foreignId('generated_quote_id')->nullable()->constrained('quotes')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();

                // Add indexes for better performance
                $table->index(['status', 'created_at']);
                $table->index(['supplier_id', 'status']);
                $table->index('order_id');
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