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
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
            $table->string('po_number')->unique(); // PO-000001 format
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->onDelete('set null');
            
            // Supplier Information
            $table->string('supplier_name')->default('MaxMed Supplier');
            $table->text('supplier_address')->nullable();
            $table->string('supplier_email')->nullable();
            $table->string('supplier_phone')->nullable();
            
            // PO Details
            $table->date('po_date');
            $table->date('delivery_date_requested')->nullable();
            $table->text('description')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('notes')->nullable();
            
            // Financial Information
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('AED');
            
            // Status and Workflow
            $table->enum('status', ['draft', 'sent_to_supplier', 'acknowledged', 'in_production', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled'])->default('draft');
            $table->timestamp('sent_to_supplier_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            
            // Payment Information
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->timestamp('payment_due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // File attachments
            $table->string('po_file')->nullable(); // Generated PO PDF
            $table->json('attachments')->nullable(); // Additional files
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            });
        }
    });
        } else {
            Schema::table('purchase_orders', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('purchase_orders');
                $schemaContent = '$table->id();
            $table->string(\'po_number\')->unique(); // PO-000001 format
            $table->foreignId(\'order_id\')->constrained(\'orders\')->onDelete(\'cascade\');
            $table->foreignId(\'delivery_id\')->nullable()->constrained(\'deliveries\')->onDelete(\'set null\');
            
            // Supplier Information
            $table->string(\'supplier_name\')->default(\'MaxMed Supplier\');
            $table->text(\'supplier_address\')->nullable();
            $table->string(\'supplier_email\')->nullable();
            $table->string(\'supplier_phone\')->nullable();
            
            // PO Details
            $table->date(\'po_date\');
            $table->date(\'delivery_date_requested\')->nullable();
            $table->text(\'description\')->nullable();
            $table->text(\'terms_conditions\')->nullable();
            $table->text(\'notes\')->nullable();
            
            // Financial Information
            $table->decimal(\'sub_total\', 10, 2)->default(0);
            $table->decimal(\'tax_amount\', 10, 2)->default(0);
            $table->decimal(\'shipping_cost\', 10, 2)->default(0);
            $table->decimal(\'total_amount\', 10, 2)->default(0);
            $table->string(\'currency\', 3)->default(\'AED\');
            
            // Status and Workflow
            $table->enum(\'status\', [\'draft\', \'sent_to_supplier\', \'acknowledged\', \'in_production\', \'ready_to_ship\', \'shipped\', \'delivered\', \'completed\', \'cancelled\'])->default(\'draft\');
            $table->timestamp(\'sent_to_supplier_at\')->nullable();
            $table->timestamp(\'acknowledged_at\')->nullable();
            
            // Payment Information
            $table->enum(\'payment_status\', [\'pending\', \'partial\', \'paid\', \'refunded\'])->default(\'pending\');
            $table->decimal(\'paid_amount\', 10, 2)->default(0);
            $table->timestamp(\'payment_due_date\')->nullable();
            $table->timestamp(\'paid_at\')->nullable();
            
            // File attachments
            $table->string(\'po_file\')->nullable(); // Generated PO PDF
            $table->json(\'attachments\')->nullable(); // Additional files
            
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