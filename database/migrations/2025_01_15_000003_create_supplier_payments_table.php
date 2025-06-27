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
        if (!Schema::hasTable('supplier_payments')) {
            Schema::create('supplier_payments', function (Blueprint $table) {
                $table->id();
            $table->string('payment_number')->unique(); // SP-000001 format
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Payment Details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->date('payment_date');
            $table->enum('payment_method', ['bank_transfer', 'cash', 'check', 'credit_card', 'online_transfer', 'other'])->default('bank_transfer');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            
            // Bank Details (if applicable)
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('transaction_id')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            
            // Attachments (receipts, transfer confirmations, etc.)
            $table->json('attachments')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            });
        } else {
            Schema::table('supplier_payments', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('supplier_payments');
                $schemaContent = '$table->id();
            $table->string(\'payment_number\')->unique(); // SP-000001 format
            $table->foreignId(\'purchase_order_id\')->constrained(\'purchase_orders\')->onDelete(\'cascade\');
            $table->foreignId(\'order_id\')->constrained(\'orders\')->onDelete(\'cascade\');
            
            // Payment Details
            $table->decimal(\'amount\', 10, 2);
            $table->string(\'currency\', 3)->default(\'AED\');
            $table->date(\'payment_date\');
            $table->enum(\'payment_method\', [\'bank_transfer\', \'cash\', \'check\', \'credit_card\', \'online_transfer\', \'other\'])->default(\'bank_transfer\');
            $table->string(\'reference_number\')->nullable();
            $table->text(\'notes\')->nullable();
            
            // Bank Details (if applicable)
            $table->string(\'bank_name\')->nullable();
            $table->string(\'account_number\')->nullable();
            $table->string(\'transaction_id\')->nullable();
            
            // Status
            $table->enum(\'status\', [\'pending\', \'processing\', \'completed\', \'failed\', \'cancelled\'])->default(\'pending\');
            $table->timestamp(\'processed_at\')->nullable();
            
            // Attachments (receipts, transfer confirmations, etc.)
            $table->json(\'attachments\')->nullable();
            
            // Audit
            $table->foreignId(\'created_by\')->nullable()->constrained(\'users\')->onDelete(\'set null\');
            $table->foreignId(\'updated_by\')->nullable()->constrained(\'users\')->onDelete(\'set null\');
            
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