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
        Schema::table('supplier_quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_quotations', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('quotation_request_id')
                      ->constrained('orders')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_quotations', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_quotations', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
        });
    }
};
