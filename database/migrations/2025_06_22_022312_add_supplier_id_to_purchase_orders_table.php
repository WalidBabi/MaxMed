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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Add supplier_id to link PO directly to supplier user
            $table->unsignedBigInteger('supplier_id')->nullable()->after('delivery_id');
            
            // Add quotation_request_id to track the original inquiry
            $table->unsignedBigInteger('quotation_request_id')->nullable()->after('supplier_id');
            
            // Add supplier_quotation_id to track the accepted quotation
            $table->unsignedBigInteger('supplier_quotation_id')->nullable()->after('quotation_request_id');
            
            // Add foreign key constraints
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('quotation_request_id')->references('id')->on('quotation_requests')->onDelete('set null');
            $table->foreign('supplier_quotation_id')->references('id')->on('supplier_quotations')->onDelete('set null');
            
            // Add indexes for performance
            $table->index('supplier_id');
            $table->index('quotation_request_id');
            $table->index('supplier_quotation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['quotation_request_id']);
            $table->dropForeign(['supplier_quotation_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['quotation_request_id']);
            $table->dropIndex(['supplier_quotation_id']);
            $table->dropColumn(['supplier_id', 'quotation_request_id', 'supplier_quotation_id']);
        });
    }
};
