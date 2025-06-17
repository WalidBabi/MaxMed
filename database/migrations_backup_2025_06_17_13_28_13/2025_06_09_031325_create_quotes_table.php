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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique(); // QT-000011 format
            $table->string('customer_name');
            $table->string('reference_number')->nullable();
            $table->date('quote_date');
            $table->date('expiry_date');
            $table->string('salesperson')->nullable();
            $table->text('subject')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->enum('status', ['draft', 'sent', 'invoiced'])->default('draft');
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->json('attachments')->nullable(); // Store file paths as JSON
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
