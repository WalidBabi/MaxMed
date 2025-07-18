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
        if (Schema::hasTable('categories')) {
            Schema::table('supplier_inquiries', function (Blueprint $table) {
                if (!Schema::hasColumn('supplier_inquiries', 'product_category_id')) {
                    $table->unsignedBigInteger('product_category_id')->nullable()->after('product_id');
                    $table->foreign('product_category_id')->references('id')->on('categories')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('supplier_inquiries')) {
            Schema::table('supplier_inquiries', function (Blueprint $table) {
                if (Schema::hasColumn('supplier_inquiries', 'product_category_id')) {
                    $table->dropForeign(['product_category_id']);
                    $table->dropColumn('product_category_id');
                }
            });
        }
    }
};
