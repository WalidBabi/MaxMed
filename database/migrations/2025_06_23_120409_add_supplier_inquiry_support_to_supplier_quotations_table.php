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
        Schema::table('supplier_quotations', function (Blueprint $table) {
            // Check if column exists before adding to avoid duplicates
            if (!Schema::hasColumn('supplier_quotations', 'supplier_inquiry_id')) {
                $table->foreignId('supplier_inquiry_id')->nullable()->after('quotation_request_id')
                      ->constrained('supplier_inquiries')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('quotation_request_id')
                      ->constrained('orders')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('supplier_id')
                      ->constrained('products')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('shipping_cost');
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'size')) {
                $table->string('size')->nullable()->after('total_amount');
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            }
            
            // Make quotation_request_id nullable to support both systems
            DB::statement('ALTER TABLE supplier_quotations MODIFY quotation_request_id BIGINT UNSIGNED NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_quotations', function (Blueprint $table) {
            // Remove added columns if they exist
            if (Schema::hasColumn('supplier_quotations', 'supplier_inquiry_id')) {
                $table->dropForeign(['supplier_inquiry_id']);
                $table->dropColumn('supplier_inquiry_id');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'size')) {
                $table->dropColumn('size');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
