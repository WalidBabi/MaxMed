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
        // Add shipping_rate to quotes table
        if (Schema::hasTable('quotes')) {
            Schema::table('quotes', function (Blueprint $table) {
                if (!Schema::hasColumn('quotes', 'shipping_rate')) {
                    $table->decimal('shipping_rate', 10, 2)->default(0)->after('sub_total');
                }
            });
        }

        // Add shipping_rate to invoices table
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'shipping_rate')) {
                    $table->decimal('shipping_rate', 10, 2)->default(0)->after('subtotal');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove shipping_rate from quotes table
        if (Schema::hasTable('quotes') && Schema::hasColumn('quotes', 'shipping_rate')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->dropColumn('shipping_rate');
            });
        }

        // Remove shipping_rate from invoices table
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'shipping_rate')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('shipping_rate');
            });
        }
    }
}; 