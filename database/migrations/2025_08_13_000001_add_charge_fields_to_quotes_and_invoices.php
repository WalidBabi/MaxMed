<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add fields to quotes table (VAT stored in tax_amount to align with invoices)
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'shipping_rate')) {
                $table->decimal('shipping_rate', 10, 2)->default(0)->after('sub_total');
            }
            if (!Schema::hasColumn('quotes', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('shipping_rate');
            }
            if (!Schema::hasColumn('quotes', 'customs_clearance')) {
                $table->decimal('customs_clearance', 10, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('quotes', 'bank_charges')) {
                $table->decimal('bank_charges', 10, 2)->default(0)->after('customs_clearance');
            }
        });

        // Add fields to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'customs_clearance')) {
                $table->decimal('customs_clearance', 10, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('invoices', 'bank_charges')) {
                $table->decimal('bank_charges', 10, 2)->default(0)->after('customs_clearance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (Schema::hasColumn('quotes', 'bank_charges')) {
                $table->dropColumn('bank_charges');
            }
            if (Schema::hasColumn('quotes', 'customs_clearance')) {
                $table->dropColumn('customs_clearance');
            }
            if (Schema::hasColumn('quotes', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }
            // Keep shipping_rate as other parts of the system rely on it
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'bank_charges')) {
                $table->dropColumn('bank_charges');
            }
            if (Schema::hasColumn('invoices', 'customs_clearance')) {
                $table->dropColumn('customs_clearance');
            }
        });
    }
};


