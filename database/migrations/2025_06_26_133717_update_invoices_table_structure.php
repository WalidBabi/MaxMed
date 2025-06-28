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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'type')) {
                $table->string('type')->default('invoice')->after('invoice_number');
            }
            if (!Schema::hasColumn('invoices', 'quote_id')) {
                $table->foreignId('quote_id')->nullable()->constrained('quotes')->onDelete('set null')->after('order_id');
            }
            if (!Schema::hasColumn('invoices', 'delivery_id')) {
                $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->onDelete('set null')->after('quote_id');
            }
            if (!Schema::hasColumn('invoices', 'parent_invoice_id')) {
                $table->foreignId('parent_invoice_id')->nullable()->constrained('invoices')->onDelete('set null')->after('delivery_id');
            }
            if (!Schema::hasColumn('invoices', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('invoices', 'billing_address')) {
                $table->text('billing_address')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('invoices', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('billing_address');
            }
            if (!Schema::hasColumn('invoices', 'description')) {
                $table->text('description')->nullable()->after('shipping_address');
            }
            if (!Schema::hasColumn('invoices', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('description');
            }
            if (!Schema::hasColumn('invoices', 'sub_total')) {
                if (Schema::hasColumn('invoices', 'subtotal')) {
                    // Rename subtotal to sub_total
                    DB::statement('ALTER TABLE invoices CHANGE subtotal sub_total DECIMAL(10,2) DEFAULT 0');
                } else {
                    $table->decimal('sub_total', 10, 2)->default(0);
                }
            }
            if (!Schema::hasColumn('invoices', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax');
            }
            if (!Schema::hasColumn('invoices', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('shipping');
            }
            if (!Schema::hasColumn('invoices', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('invoices', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('invoices', 'payment_terms')) {
                $table->enum('payment_terms', ['advance_50', 'advance_100', 'on_delivery', 'net_30', 'custom'])->default('advance_50')->after('payment_status');
            }
            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_terms');
            }
            if (!Schema::hasColumn('invoices', 'advance_percentage')) {
                $table->decimal('advance_percentage', 5, 2)->nullable()->after('paid_amount');
            }
            if (!Schema::hasColumn('invoices', 'payment_due_date')) {
                $table->date('payment_due_date')->nullable()->after('advance_percentage');
            }
            if (!Schema::hasColumn('invoices', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_due_date');
            }
            if (!Schema::hasColumn('invoices', 'is_proforma')) {
                $table->boolean('is_proforma')->default(false)->after('paid_at');
            }
            if (!Schema::hasColumn('invoices', 'requires_advance_payment')) {
                $table->boolean('requires_advance_payment')->default(false)->after('is_proforma');
            }
            if (!Schema::hasColumn('invoices', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('requires_advance_payment');
            }
            if (!Schema::hasColumn('invoices', 'email_history')) {
                $table->json('email_history')->nullable()->after('sent_at');
            }
            if (!Schema::hasColumn('invoices', 'attachments')) {
                $table->json('attachments')->nullable()->after('email_history');
            }
            if (!Schema::hasColumn('invoices', 'reference_number')) {
                $table->string('reference_number')->nullable()->after('attachments');
            }
            if (!Schema::hasColumn('invoices', 'po_number')) {
                $table->string('po_number')->nullable()->after('reference_number');
            }
            if (!Schema::hasColumn('invoices', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'quote_id')) {
                $table->dropForeign(['quote_id']);
                $table->dropColumn('quote_id');
            }
            if (Schema::hasColumn('invoices', 'delivery_id')) {
                $table->dropForeign(['delivery_id']);
                $table->dropColumn('delivery_id');
            }
            if (Schema::hasColumn('invoices', 'parent_invoice_id')) {
                $table->dropForeign(['parent_invoice_id']);
                $table->dropColumn('parent_invoice_id');
            }
            if (Schema::hasColumn('invoices', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
            $columns = [
                'type', 'customer_name', 'billing_address', 'shipping_address',
                'description', 'terms_conditions',
                'sub_total', 'tax_amount', 'discount_amount', 'total_amount',
                'payment_status', 'payment_terms', 'paid_amount', 'advance_percentage',
                'payment_due_date', 'paid_at', 'is_proforma', 'requires_advance_payment',
                'sent_at', 'email_history', 'attachments', 'reference_number', 'po_number'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
