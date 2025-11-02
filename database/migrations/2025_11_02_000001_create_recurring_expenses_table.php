<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vendor')->nullable();
            $table->decimal('unit_amount', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->string('currency', 10)->default('AED');
            $table->string('frequency', 20)->default('monthly');
            $table->unsignedInteger('repeats_every')->default(1);
            $table->unsignedInteger('active_months_mask')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_expenses');
    }
};


