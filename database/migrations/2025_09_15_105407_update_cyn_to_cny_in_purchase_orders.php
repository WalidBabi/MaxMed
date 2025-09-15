<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing purchase orders with CYN currency to CNY
        DB::table('purchase_orders')
            ->where('currency', 'CYN')
            ->update(['currency' => 'CNY']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert CNY back to CYN (if needed)
        DB::table('purchase_orders')
            ->where('currency', 'CNY')
            ->update(['currency' => 'CYN']);
    }
};
