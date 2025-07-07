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
        Schema::create('supplier_quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_quotation_id');
            $table->unsignedBigInteger('supplier_inquiry_item_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->text('product_description')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('AED');
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->string('size')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->json('attachments')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        // Add foreign key constraints after table creation
        Schema::table('supplier_quotation_items', function (Blueprint $table) {
            $table->foreign('supplier_quotation_id')->references('id')->on('supplier_quotations')->onDelete('cascade');
            $table->foreign('supplier_inquiry_item_id')->references('id')->on('supplier_inquiry_items')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_quotation_items');
    }
};
