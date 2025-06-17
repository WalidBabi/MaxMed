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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // INV-000001 or PF-000001 format
            $table->enum('type', ['proforma', 'final'])->default('proforma');
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->onDelete('set null');
            $table->foreignId('parent_invoice_id')->nullable()->constrained('invoices')->onDelete('set null'); // For linking proforma to final
            
            // Customer Information
            $table->string('customer_name');
            $table->text('billing_address');
            $table->text('shipping_address')->nullable();
            
            // Invoice Details
            $table->date('invoice_date');
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('notes')->nullable();
            
            // Financial Information
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            
            // Payment Information
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->enum('payment_terms', ['advance_50', 'advance_100', 'on_delivery', 'net_30', 'custom'])->default('advance_50');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('advance_percentage', 5, 2)->nullable(); // For custom advance payments
            $table->timestamp('payment_due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Status and Workflow
            $table->enum('status', ['draft', 'sent', 'confirmed', 'in_production', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_proforma')->default(true);
            $table->boolean('requires_advance_payment')->default(true);
            
            // Email tracking
            $table->timestamp('sent_at')->nullable();
            $table->json('email_history')->nullable(); // Track email sends
            
            // Attachments and References
            $table->json('attachments')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('po_number')->nullable(); // Purchase Order Number
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
