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
        Schema::create('supplier_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique(); // SQ-000001
            $table->foreignId('quotation_request_id')->constrained('quotation_requests')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->json('specifications')->nullable();
            $table->date('valid_until');
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['supplier_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_quotations');
    }
}; 