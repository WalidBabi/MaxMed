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
        Schema::table('supplier_inquiry_items', function (Blueprint $table) {
            // Drop the existing foreign key if it exists
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {}
            // Change the column type
            $table->unsignedBigInteger('product_id')->nullable()->change();
            // Re-add the foreign key constraint
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_inquiry_items', function (Blueprint $table) {
            // Drop the fixed foreign key
            $table->dropForeign(['product_id']);
            // Optionally, change back to integer if needed (not recommended)
            // $table->integer('product_id')->nullable()->change();
        });
    }
};
