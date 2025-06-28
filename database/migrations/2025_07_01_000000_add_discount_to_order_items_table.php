<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
            }
            if (!Schema::hasColumn('order_items', 'line_total')) {
                $table->decimal('line_total', 10, 2)->after('discount_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'discount_amount', 'line_total']);
        });
    }
} 