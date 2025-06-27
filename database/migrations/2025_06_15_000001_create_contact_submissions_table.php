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
        if (!Schema::hasTable('quotes')) {
            Schema::create('quotes', function (Blueprint $table) {
                $table->id();
            $table->string('quote_number')->unique(); // QUO-000001
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->date('quote_date');
            $table->date('valid_until');
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            });
        } else {
            Schema::table('quotes', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('quotes');
                $schemaContent = '$table->id();
            $table->string(\'quote_number\')->unique(); // QUO-000001
            $table->foreignId(\'customer_id\')->constrained(\'customers\')->onDelete(\'cascade\');
            $table->decimal(\'subtotal\', 10, 2);
            $table->decimal(\'tax\', 10, 2)->default(0);
            $table->decimal(\'shipping\', 10, 2)->default(0);
            $table->decimal(\'total\', 10, 2);
            $table->string(\'currency\', 3)->default(\'AED\');
            $table->date(\'quote_date\');
            $table->date(\'valid_until\');
            $table->enum(\'status\', [\'draft\', \'sent\', \'accepted\', \'rejected\', \'expired\'])->default(\'draft\');
            $table->text(\'notes\')->nullable();
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
            $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->json('specifications')->nullable();
            $table->timestamps();
        });

        // Create quotation_requests table
        Schema::create('quotation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // QR-000001
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->string('product_name')->nullable(); // For unlisted products
            $table->text('product_description')->nullable(); // For unlisted products
            $table->integer('quantity');
            $table->text('specifications')->nullable();
            $table->text('special_requirements')->nullable();
            $table->date('delivery_timeline')->nullable();
            $table->enum('status', ['new', 'assigned', 'quoted', 'accepted', 'rejected', 'cancelled'])->default('new');
            $table->foreignId('generated_quote_id')->nullable()->constrained('quotes')->onDelete('set null');
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['supplier_id', 'status']);
            $table->index('order_id');
        });

        // Create contact_submissions table
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('message');
            $table->enum('status', ['new', 'in_progress', 'converted', 'closed'])->default('new');
            $table->enum('lead_potential', ['hot', 'warm', 'cold'])->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('converted_to_inquiry_id')->nullable()->constrained('quotation_requests')->onDelete('set null');
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