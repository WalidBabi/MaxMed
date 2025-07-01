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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('procurement_price_aed', 10, 2)->nullable()->after('price_aed');
            $table->decimal('procurement_price_usd', 10, 2)->nullable()->after('procurement_price_aed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['procurement_price_aed', 'procurement_price_usd']);
        });
    }
};
