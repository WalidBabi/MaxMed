<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Invoices table updates
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->nullable()->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('invoices', 'customs_clearance_fee')) {
                $table->decimal('customs_clearance_fee', 10, 2)->default(0)->after('shipping_rate');
            }
            if (!Schema::hasColumn('invoices', 'payment_terms')) {
                $table->enum('payment_terms', ['advance_50', 'advance_100', 'on_delivery', 'net_30', 'custom'])->default('advance_50')->after('payment_status');
            }
        });

        // Quotes table updates
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'payment_terms')) {
                $table->enum('payment_terms', ['advance_50', 'advance_100', 'on_delivery', 'net_30', 'custom'])->default('advance_50')->after('status');
            }
            if (!Schema::hasColumn('quotes', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->nullable()->default(0)->after('shipping_rate');
            }
            if (!Schema::hasColumn('quotes', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0)->after('vat_rate');
            }
            if (!Schema::hasColumn('quotes', 'customs_clearance_fee')) {
                $table->decimal('customs_clearance_fee', 10, 2)->default(0)->after('shipping_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'vat_rate')) {
                $table->dropColumn('vat_rate');
            }
            if (Schema::hasColumn('invoices', 'customs_clearance_fee')) {
                $table->dropColumn('customs_clearance_fee');
            }
            // Do not drop payment_terms to preserve data
        });

        Schema::table('quotes', function (Blueprint $table) {
            if (Schema::hasColumn('quotes', 'payment_terms')) {
                $table->dropColumn('payment_terms');
            }
            if (Schema::hasColumn('quotes', 'vat_rate')) {
                $table->dropColumn('vat_rate');
            }
            if (Schema::hasColumn('quotes', 'vat_amount')) {
                $table->dropColumn('vat_amount');
            }
            if (Schema::hasColumn('quotes', 'customs_clearance_fee')) {
                $table->dropColumn('customs_clearance_fee');
            }
        });
    }
};


