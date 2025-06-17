<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_request_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('product_id');
            
            // Quotation details
            $table->string('quotation_number')->unique();
            $table->decimal('unit_price', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->integer('minimum_quantity')->default(1);
            $table->integer('lead_time_days')->nullable();
            $table->date('valid_until')->nullable();
            
            // Product specifications
            $table->string('size')->nullable();
            $table->json('specifications')->nullable();
            $table->text('description')->nullable();
            
            // Supplier notes and terms
            $table->text('supplier_notes')->nullable();
            $table->text('terms_conditions')->nullable();
            
            // Status and workflow
            $table->enum('status', ['draft', 'submitted', 'accepted', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            
            // Attachments (for additional documents)
            $table->json('attachments')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('quotation_request_id')->references('id')->on('quotation_requests')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Indexes
            $table->index(['supplier_id', 'status']);
            $table->index(['quotation_request_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_quotations');
    }
}; 