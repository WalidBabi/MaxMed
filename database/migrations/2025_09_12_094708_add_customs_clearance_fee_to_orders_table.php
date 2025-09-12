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
        Schema::table('orders', function (Blueprint $table) {
            // Add customs clearance fee field
            if (!Schema::hasColumn('orders', 'customs_clearance_fee')) {
                $table->decimal('customs_clearance_fee', 10, 2)->default(0)->after('vat_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customs_clearance_fee')) {
                $table->dropColumn('customs_clearance_fee');
            }
        });
    }
};