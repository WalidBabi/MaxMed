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
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->text('item_details');
            $table->decimal('quantity', 8, 2)->default(1.00);
            $table->decimal('rate', 10, 2)->default(0.00);
            $table->decimal('discount', 8, 2)->default(0.00); // Percentage
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->integer('sort_order')->default(0); // For drag and drop ordering
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
