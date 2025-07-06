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
        Schema::create('supplier_inquiry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_inquiry_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name')->nullable(); // For unlisted products
            $table->text('product_description')->nullable(); // For unlisted products
            $table->string('product_category')->nullable(); // For unlisted products
            $table->string('product_brand')->nullable(); // For unlisted products
            $table->text('product_specifications')->nullable(); // For unlisted products
            $table->decimal('quantity', 10, 2)->nullable();
            $table->text('requirements')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_inquiry_items');
    }
};
