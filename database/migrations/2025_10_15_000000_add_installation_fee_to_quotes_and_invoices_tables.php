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
        // Add installation_fee to quotes table
        if (Schema::hasTable('quotes')) {
            Schema::table('quotes', function (Blueprint $table) {
                if (!Schema::hasColumn('quotes', 'installation_fee')) {
                    $table->decimal('installation_fee', 10, 2)->default(0)->after('shipping_rate');
                }
            });
        }

        // Add installation_fee to invoices table
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'installation_fee')) {
                    $table->decimal('installation_fee', 10, 2)->default(0)->after('shipping_rate');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove installation_fee from quotes table
        if (Schema::hasTable('quotes') && Schema::hasColumn('quotes', 'installation_fee')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->dropColumn('installation_fee');
            });
        }

        // Remove installation_fee from invoices table
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'installation_fee')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('installation_fee');
            });
        }
    }
};






