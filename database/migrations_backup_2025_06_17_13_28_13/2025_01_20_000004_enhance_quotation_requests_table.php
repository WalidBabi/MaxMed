<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_requests', function (Blueprint $table) {
            // Add workflow status
            $table->enum('status', [
                'pending',           // Initial state - waiting for MaxMed to forward
                'forwarded',         // Forwarded to supplier
                'supplier_responded', // Supplier has responded
                'quote_created',     // Customer quote created
                'completed',         // Process completed
                'cancelled'          // Cancelled/Not available
            ])->default('pending')->after('notes');
            
            // Add supplier information
            $table->unsignedBigInteger('supplier_id')->nullable()->after('user_id');
            $table->timestamp('forwarded_at')->nullable()->after('status');
            $table->timestamp('supplier_responded_at')->nullable()->after('forwarded_at');
            
            // Add CRM integration
            $table->unsignedBigInteger('lead_id')->nullable()->after('supplier_id');
            
            // Add MaxMed internal notes
            $table->text('internal_notes')->nullable()->after('notes');
            
            // Add supplier response
            $table->enum('supplier_response', ['pending', 'available', 'not_available'])->default('pending')->after('internal_notes');
            $table->text('supplier_notes')->nullable()->after('supplier_response');
            
            // Add quotation reference
            $table->unsignedBigInteger('generated_quote_id')->nullable()->after('supplier_notes');
            
            // Add foreign keys
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('generated_quote_id')->references('id')->on('quotes')->onDelete('set null');
            
            // Add indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['supplier_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('quotation_requests', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['generated_quote_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['supplier_id', 'status']);
            
            $table->dropColumn([
                'status',
                'supplier_id',
                'forwarded_at',
                'supplier_responded_at',
                'internal_notes',
                'supplier_response',
                'supplier_notes',
                'generated_quote_id'
            ]);
        });
    }
}; 