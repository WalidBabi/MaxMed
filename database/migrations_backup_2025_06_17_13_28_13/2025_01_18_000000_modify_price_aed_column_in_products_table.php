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
            // Change price_aed from decimal(3,2) to decimal(10,2) to handle larger values
            $table->decimal('price_aed', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert back to the original decimal(3,2)
            $table->decimal('price_aed', 3, 2)->nullable()->change();
        });
    }
}; 