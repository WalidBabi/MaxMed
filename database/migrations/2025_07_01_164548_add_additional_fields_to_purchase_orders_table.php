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
            // Add missing fields for the improved purchase order form
            $table->string('payment_terms')->nullable()->after('currency');
            $table->string('shipping_method')->nullable()->after('payment_terms');
            $table->string('supplier_contact_person')->nullable()->after('supplier_phone');
            $table->text('special_instructions')->nullable()->after('notes');
            $table->decimal('tax_rate', 5, 2)->default(5.00)->after('special_instructions');
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('tax_rate');
            
            // Add missing fields that were in the original migration but might be missing
            if (!Schema::hasColumn('purchase_orders', 'po_date')) {
                $table->date('po_date')->nullable()->after('po_number');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('po_number');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'supplier_name')) {
                $table->string('supplier_name')->nullable()->after('supplier_id');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'supplier_email')) {
                $table->string('supplier_email')->nullable()->after('supplier_name');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'supplier_phone')) {
                $table->string('supplier_phone')->nullable()->after('supplier_email');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'supplier_address')) {
                $table->text('supplier_address')->nullable()->after('supplier_phone');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'delivery_date_requested')) {
                $table->date('delivery_date_requested')->nullable()->after('po_date');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'description')) {
                $table->text('description')->nullable()->after('delivery_date_requested');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'notes')) {
                $table->text('notes')->nullable()->after('terms_conditions');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'sub_total')) {
                $table->decimal('sub_total', 10, 2)->default(0.00)->after('notes');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0.00)->after('discount_amount');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'status')) {
                $table->enum('status', ['draft', 'sent_to_supplier', 'acknowledged', 'in_production', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled'])->default('draft')->after('total_amount');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending')->after('status');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_terms',
                'shipping_method',
                'supplier_contact_person',
                'special_instructions',
                'tax_rate',
                'discount_amount'
            ]);
        });
    }
};
