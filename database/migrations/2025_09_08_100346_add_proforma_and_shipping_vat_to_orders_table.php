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
            // Add proforma invoice link
            if (!Schema::hasColumn('orders', 'proforma_invoice_id')) {
                $table->unsignedBigInteger('proforma_invoice_id')->nullable()->after('customer_id');
                $table->foreign('proforma_invoice_id')->references('id')->on('invoices')->onDelete('set null');
            }
            
            // Add shipping and VAT fields
            if (!Schema::hasColumn('orders', 'shipping_rate')) {
                $table->decimal('shipping_rate', 10, 2)->default(0)->after('total_amount');
            }
            
            if (!Schema::hasColumn('orders', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->default(0)->after('shipping_rate');
            }
            
            if (!Schema::hasColumn('orders', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0)->after('vat_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'proforma_invoice_id')) {
                $table->dropForeign(['proforma_invoice_id']);
                $table->dropColumn('proforma_invoice_id');
            }
            
            if (Schema::hasColumn('orders', 'shipping_rate')) {
                $table->dropColumn('shipping_rate');
            }
            
            if (Schema::hasColumn('orders', 'vat_rate')) {
                $table->dropColumn('vat_rate');
            }
            
            if (Schema::hasColumn('orders', 'vat_amount')) {
                $table->dropColumn('vat_amount');
            }
        });
    }
};