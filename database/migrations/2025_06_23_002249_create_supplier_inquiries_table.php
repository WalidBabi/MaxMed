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
        }
    });
        } else {
            Schema::table('supplier_inquiries', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('supplier_inquiries');
                $schemaContent = '$table->id();
            // Product Information
            $table->unsignedBigInteger(\'product_id\')->nullable();
            $table->string(\'product_name\')->nullable(); // For unlisted products
            $table->text(\'product_description\')->nullable();
            $table->string(\'product_category\')->nullable();
            $table->string(\'product_brand\')->nullable();
            $table->text(\'product_specifications\')->nullable();
            
            // Inquiry Details
            $table->integer(\'quantity\')->unsigned();
            $table->text(\'requirements\')->nullable();
            $table->text(\'notes\')->nullable();
            $table->text(\'internal_notes\')->nullable();
            $table->string(\'customer_reference\')->nullable();
            $table->string(\'reference_number\')->unique(); // INQ-YYYY-XXXXX
            
            // Supplier Targeting
            $table->boolean(\'broadcast_to_all_suppliers\')->default(false);
            $table->json(\'target_supplier_categories\')->nullable();
            
            // Status and Tracking
            $table->enum(\'status\', [
                \'pending\',      // Just created
                \'processing\',   // Admin reviewing
                \'broadcast\',    // Sent to suppliers
                \'in_progress\',  // Suppliers are responding
                \'quoted\',       // Has received quotations
                \'converted\',    // Converted to order
                \'cancelled\',    // Cancelled/Rejected
                \'expired\'       // No response/timeout
            ])->default(\'pending\');
            
            // Timestamps
            $table->timestamp(\'broadcast_at\')->nullable();
            $table->timestamp(\'expires_at\')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign(\'product_id\')
                  ->references(\'id\')
                  ->on(\'products\')
                  ->onDelete(\'set null\');';
                
                // Parse the schema content to find column definitions
                preg_match_all('/$table->([^;]+);/', $schemaContent, $columnMatches);
                foreach ($columnMatches[1] as $columnDef) {
                    if (preg_match('/^(\w+)\(['"]([^'"]+)['"]\)/', $columnDef, $colMatch)) {
                        $columnName = $colMatch[2];
                        if (!in_array($columnName, $columns)) {
                            $table->{$colMatch[1]}($columnName);
                        }
                    }
                }
            });
        }
    });

        // Pivot table for supplier responses tracking
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
};
