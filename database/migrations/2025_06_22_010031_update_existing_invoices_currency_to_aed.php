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
        // Update all existing invoices with USD currency to AED
        DB::table('invoices')
            ->where('currency', 'USD')
            ->update(['currency' => 'AED']);
            
        // Update all existing payments with USD currency to AED
        DB::table('payments')
            ->where('currency', 'USD')
            ->update(['currency' => 'AED']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to USD if needed
        DB::table('invoices')
            ->where('currency', 'AED')
            ->update(['currency' => 'USD']);
            
        DB::table('payments')
            ->where('currency', 'AED')
            ->update(['currency' => 'USD']);
    }
};
