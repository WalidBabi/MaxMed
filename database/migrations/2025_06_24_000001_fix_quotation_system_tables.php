<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quotes')) {
            Schema::create('quotes', function (Blueprint $table) {
                $table->id();
            $table->string('quote_number')->unique(); // QT-000011 format
            $table->string('customer_name');
            $table->string('reference_number')->nullable();
            $table->date('quote_date');
            $table->date('expiry_date');
            $table->string('salesperson')->nullable();
            $table->text('subject')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->enum('status', ['draft', 'sent', 'invoiced'])->default('draft');
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->json('attachments')->nullable(); // Store file paths as JSON
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            });
        }
    });
        } else {
            Schema::table('quotes', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('quotes');
                $schemaContent = '$table->id();
            $table->string(\'quote_number\')->unique(); // QT-000011 format
            $table->string(\'customer_name\');
            $table->string(\'reference_number\')->nullable();
            $table->date(\'quote_date\');
            $table->date(\'expiry_date\');
            $table->string(\'salesperson\')->nullable();
            $table->text(\'subject\')->nullable();
            $table->text(\'customer_notes\')->nullable();
            $table->text(\'terms_conditions\')->nullable();
            $table->enum(\'status\', [\'draft\', \'sent\', \'invoiced\'])->default(\'draft\');
            $table->decimal(\'sub_total\', 10, 2)->default(0);
            $table->decimal(\'total_amount\', 10, 2)->default(0);
            $table->json(\'attachments\')->nullable(); // Store file paths as JSON
            $table->foreignId(\'created_by\')->nullable()->constrained(\'users\')->onDelete(\'set null\');
            $table->timestamps();';
                
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

        // Create quote_items table
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->text('item_details');
            $table->decimal('quantity', 8, 2)->default(1.00);
            $table->decimal('rate', 10, 2)->default(0.00);
            $table->decimal('discount', 8, 2)->default(0.00); // Percentage
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->integer('sort_order')->default(0); // For drag and drop ordering
            $table->text('specifications')->nullable();
            $table->timestamps();
        });

        // Create quotation_requests table
        Schema::create('quotation_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable(); // Made nullable for unlisted products
            $table->unsignedBigInteger('user_id');
            $table->integer('quantity');
            $table->string('size')->nullable();
            $table->text('requirements')->nullable();
            $table->text('notes')->nullable();
            $table->string('delivery_timeline')->nullable();
            
            // Status tracking
            $table->enum('status', [
                'pending',           // Initial state - waiting for MaxMed to forward
                'forwarded',         // Forwarded to supplier
                'supplier_responded', // Supplier has responded
                'quote_created',     // Customer quote created
                'completed',         // Process completed
                'cancelled'          // Cancelled/Not available
            ])->default('pending');
            
            // Supplier information
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->timestamp('forwarded_at')->nullable();
            $table->timestamp('supplier_responded_at')->nullable();
            
            // CRM integration
            $table->unsignedBigInteger('lead_id')->nullable();
            
            // Internal tracking
            $table->text('internal_notes')->nullable();
            $table->enum('supplier_response', ['pending', 'available', 'not_available'])->default('pending');
            $table->text('supplier_notes')->nullable();
            
            // Quote reference
            $table->unsignedBigInteger('generated_quote_id')->nullable();
            
            // Unlisted product fields
            $table->string('product_name')->nullable();
            $table->text('product_description')->nullable();
            $table->json('product_specifications')->nullable();
            $table->string('product_category')->nullable();
            $table->string('product_brand')->nullable();
            $table->string('customer_reference')->nullable();
            $table->boolean('is_unlisted_product')->default(false);
            $table->json('target_supplier_categories')->nullable();
            $table->boolean('broadcast_to_all_suppliers')->default(false);
            
            $table->timestamps();
            
            // Add foreign key constraints
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('set null');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('generated_quote_id')
                  ->references('id')
                  ->on('quotes')
                  ->onDelete('set null');
            
            // Add indexes
            $table->index(['status', 'created_at']);
            $table->index(['supplier_id', 'status']);
        });

        // Create contact_submissions table
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            
            // Workflow tracking
            $table->enum('status', [
                'new',              // Just received
                'in_review',        // Being reviewed by CRM
                'converted_to_lead', // Converted to CRM lead
                'converted_to_inquiry', // Converted to quotation request
                'responded',        // Direct response sent
                'closed'           // Closed/resolved
            ])->default('new');
            
            $table->unsignedBigInteger('converted_to_inquiry_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->enum('lead_potential', ['hot', 'warm', 'cold'])->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('converted_to_inquiry_id')
                  ->references('id')
                  ->on('quotation_requests')
                  ->onDelete('set null');
                  
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['subject', 'status']);
            $table->index('email');
        });

        // Create supplier_quotations table
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
            
            // Attachments
            $table->json('attachments')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('quotation_request_id')
                  ->references('id')
                  ->on('quotation_requests')
                  ->onDelete('cascade');
                  
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['supplier_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
}; 