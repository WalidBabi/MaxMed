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
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
            $table->string('invoice_number')->unique(); // INV-000001
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            });
        } else {
            Schema::table('invoices', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('invoices');
                $schemaContent = '$table->id();
            $table->string(\'invoice_number\')->unique(); // INV-000001
            $table->foreignId(\'order_id\')->nullable()->constrained(\'orders\')->onDelete(\'set null\');
            $table->foreignId(\'customer_id\')->constrained(\'customers\')->onDelete(\'cascade\');
            $table->decimal(\'subtotal\', 10, 2);
            $table->decimal(\'tax\', 10, 2)->default(0);
            $table->decimal(\'shipping\', 10, 2)->default(0);
            $table->decimal(\'total\', 10, 2);
            $table->string(\'currency\', 3)->default(\'AED\');
            $table->date(\'invoice_date\');
            $table->date(\'due_date\');
            $table->enum(\'status\', [\'draft\', \'sent\', \'paid\', \'overdue\', \'cancelled\'])->default(\'draft\');
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

        // Create invoice items table
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        // Create payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('payment_number')->unique(); // PAY-000001
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'check', 'cash', 'online', 'other'])->default('bank_transfer');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->date('payment_date');
            $table->string('transaction_reference')->nullable();
            $table->text('payment_notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('payment_details')->nullable(); // Store additional payment info
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
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