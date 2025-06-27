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
        Schema::table('invoices', function (Blueprint $table) {
            // Add missing columns for proforma invoices and quotes
            $table->string('type')->default('invoice')->after('invoice_number');
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->onDelete('set null')->after('order_id');
            $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->onDelete('set null')->after('quote_id');
            $table->foreignId('parent_invoice_id')->nullable()->constrained('invoices')->onDelete('set null')->after('delivery_id');
            
            // Customer information
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->text('billing_address')->nullable()->after('customer_name');
            $table->text('shipping_address')->nullable()->after('billing_address');
            
            // Invoice details
            $table->text('description')->nullable()->after('shipping_address');
            $table->text('terms_conditions')->nullable()->after('description');
            
            // Financial fields
            $table->decimal('sub_total', 10, 2)->default(0)->after('subtotal');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('shipping');
            $table->decimal('total_amount', 10, 2)->default(0)->after('total');
            
            // Payment information
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending')->after('status');
            $table->enum('payment_terms', ['advance_50', 'advance_100', 'on_delivery', 'net_30', 'custom'])->default('advance_50')->after('payment_status');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_terms');
            $table->decimal('advance_percentage', 5, 2)->nullable()->after('paid_amount');
            $table->date('payment_due_date')->nullable()->after('advance_percentage');
            $table->timestamp('paid_at')->nullable()->after('payment_due_date');
            
            // Proforma specific
            $table->boolean('is_proforma')->default(false)->after('paid_at');
            $table->boolean('requires_advance_payment')->default(false)->after('is_proforma');
            
            // Additional fields
            $table->timestamp('sent_at')->nullable()->after('requires_advance_payment');
            $table->json('email_history')->nullable()->after('sent_at');
            $table->json('attachments')->nullable()->after('email_history');
            $table->string('reference_number')->nullable()->after('attachments');
            $table->string('po_number')->nullable()->after('reference_number');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Remove all added columns
            $table->dropForeign(['quote_id']);
            $table->dropForeign(['delivery_id']);
            $table->dropForeign(['parent_invoice_id']);
            $table->dropForeign(['updated_by']);
            
            $table->dropColumn([
                'type', 'quote_id', 'delivery_id', 'parent_invoice_id',
                'customer_name', 'billing_address', 'shipping_address',
                'description', 'terms_conditions',
                'sub_total', 'tax_amount', 'discount_amount', 'total_amount',
                'payment_status', 'payment_terms', 'paid_amount', 'advance_percentage',
                'payment_due_date', 'paid_at', 'is_proforma', 'requires_advance_payment',
                'sent_at', 'email_history', 'attachments', 'reference_number', 'po_number', 'updated_by'
            ]);
        });
    }
};
