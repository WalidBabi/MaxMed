<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_expense_id')->constrained('recurring_expenses')->cascadeOnDelete();
            $table->date('period_date'); // first day of the month this payment relates to
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('AED');
            $table->string('status', 20)->default('paid'); // paid/unpaid (future use)
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['recurring_expense_id', 'period_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_payments');
    }
};


