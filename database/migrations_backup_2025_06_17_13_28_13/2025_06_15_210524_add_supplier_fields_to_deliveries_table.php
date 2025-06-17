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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('packing_list_file')->nullable()->after('delivery_conditions');
            $table->string('commercial_invoice_file')->nullable()->after('packing_list_file');
            $table->timestamp('processed_by_supplier_at')->nullable()->after('commercial_invoice_file');
            $table->timestamp('sent_to_carrier_at')->nullable()->after('processed_by_supplier_at');
            $table->text('supplier_notes')->nullable()->after('sent_to_carrier_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'packing_list_file',
                'commercial_invoice_file',
                'processed_by_supplier_at',
                'sent_to_carrier_at',
                'supplier_notes'
            ]);
        });
    }
};
