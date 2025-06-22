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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('payment_number')->unique(); // PAY-000001
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'check', 'cash', 'online', 'other'])->default('bank_transfer');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->date('payment_date');
            $table->string('transaction_reference')->nullable();
            $table->text('payment_notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('payment_details')->nullable(); // Store additional payment info
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}; 