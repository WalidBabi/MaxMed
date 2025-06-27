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
        if (!Schema::hasTable('supplier_inquiries')) {
            Schema::create('supplier_inquiries', function (Blueprint $table) {
                $table->id();
                // Product Information
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('product_name')->nullable(); // For unlisted products
                $table->text('product_description')->nullable();
                $table->string('product_category')->nullable();
                $table->string('product_brand')->nullable();
                $table->text('product_specifications')->nullable();
                
                // Inquiry Details
                $table->integer('quantity')->unsigned();
                $table->text('requirements')->nullable();
                $table->text('notes')->nullable();
                $table->text('internal_notes')->nullable();
                $table->string('customer_reference')->nullable();
                $table->string('reference_number')->unique(); // INQ-YYYY-XXXXX
                
                // Supplier Targeting
                $table->boolean('broadcast_to_all_suppliers')->default(false);
                $table->json('target_supplier_categories')->nullable();
                
                // Status and Tracking
                $table->enum('status', [
                    'pending',      // Just created
                    'processing',   // Admin reviewing
                    'broadcast',    // Sent to suppliers
                    'in_progress',  // Suppliers are responding
                    'quoted',       // Has received quotations
                    'converted',    // Converted to order
                    'cancelled',    // Cancelled/Rejected
                    'expired'       // No response/timeout
                ])->default('pending');
                
                // Timestamps
                $table->timestamp('broadcast_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Foreign key constraints
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('set null');
            });
        } else {
            Schema::table('supplier_inquiries', function (Blueprint $table) {
                // Add columns if they don't exist
                if (!Schema::hasColumn('supplier_inquiries', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'product_name')) {
                    $table->string('product_name')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'product_description')) {
                    $table->text('product_description')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'product_category')) {
                    $table->string('product_category')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'product_brand')) {
                    $table->string('product_brand')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'product_specifications')) {
                    $table->text('product_specifications')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'quantity')) {
                    $table->integer('quantity')->unsigned();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'requirements')) {
                    $table->text('requirements')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'internal_notes')) {
                    $table->text('internal_notes')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'customer_reference')) {
                    $table->string('customer_reference')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'reference_number')) {
                    $table->string('reference_number')->unique();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'broadcast_to_all_suppliers')) {
                    $table->boolean('broadcast_to_all_suppliers')->default(false);
                }
                if (!Schema::hasColumn('supplier_inquiries', 'target_supplier_categories')) {
                    $table->json('target_supplier_categories')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'status')) {
                    $table->enum('status', [
                        'pending', 'processing', 'broadcast', 'in_progress',
                        'quoted', 'converted', 'cancelled', 'expired'
                    ])->default('pending');
                }
                if (!Schema::hasColumn('supplier_inquiries', 'broadcast_at')) {
                    $table->timestamp('broadcast_at')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable();
                }
                if (!Schema::hasColumn('supplier_inquiries', 'deleted_at')) {
                    $table->softDeletes();
                }

                // Add foreign key if it doesn't exist
                if (!Schema::hasColumn('supplier_inquiries', 'product_id')) {
                    $table->foreign('product_id')
                        ->references('id')
                        ->on('products')
                        ->onDelete('set null');
                }
            });
        }

        // Create responses table if it doesn't exist
        if (!Schema::hasTable('supplier_inquiry_responses')) {
            Schema::create('supplier_inquiry_responses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_inquiry_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Supplier user
                $table->enum('status', [
                    'pending',      // Supplier hasn't viewed
                    'viewed',       // Supplier has viewed
                    'interested',   // Supplier showed interest
                    'not_interested', // Supplier declined
                    'quoted'        // Supplier submitted quote
                ])->default('pending');
                $table->timestamp('viewed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the responses table first to maintain referential integrity
        Schema::dropIfExists('supplier_inquiry_responses');
        
        // Don't drop the main table in production to preserve data
        if (app()->environment('local', 'testing')) {
            Schema::dropIfExists('supplier_inquiries');
        }
    }
};
