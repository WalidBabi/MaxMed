<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('supplier_quotations')) {
            Schema::create('supplier_quotations', function (Blueprint $table) {
                $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('quotation_request_id')->nullable()->constrained('quotation_requests')->onDelete('cascade');
            $table->foreignId('supplier_inquiry_id')->nullable()->constrained('supplier_inquiries')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            
            // Pricing details
            $table->decimal('unit_price', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('AED');
            
            // Product details
            $table->string('size')->nullable();
            $table->json('specifications')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            
            // Status and workflow
            $table->enum('status', ['draft', 'submitted', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['supplier_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('order_id');
            });
        }
    });
        } else {
            Schema::table('supplier_quotations', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('supplier_quotations');
                $schemaContent = '$table->id();
            $table->string(\'quotation_number\')->unique();
            $table->foreignId(\'quotation_request_id\')->nullable()->constrained(\'quotation_requests\')->onDelete(\'cascade\');
            $table->foreignId(\'supplier_inquiry_id\')->nullable()->constrained(\'supplier_inquiries\')->onDelete(\'cascade\');
            $table->foreignId(\'order_id\')->nullable()->constrained(\'orders\')->onDelete(\'cascade\');
            $table->foreignId(\'supplier_id\')->constrained(\'users\')->onDelete(\'cascade\');
            $table->foreignId(\'product_id\')->nullable()->constrained(\'products\')->onDelete(\'set null\');
            
            // Pricing details
            $table->decimal(\'unit_price\', 10, 2);
            $table->decimal(\'shipping_cost\', 10, 2)->default(0);
            $table->decimal(\'total_amount\', 10, 2)->nullable();
            $table->string(\'currency\', 3)->default(\'AED\');
            
            // Product details
            $table->string(\'size\')->nullable();
            $table->json(\'specifications\')->nullable();
            $table->text(\'description\')->nullable();
            $table->text(\'notes\')->nullable();
            
            // Status and workflow
            $table->enum(\'status\', [\'draft\', \'submitted\', \'accepted\', \'rejected\', \'expired\'])->default(\'draft\');
            $table->timestamp(\'approved_at\')->nullable();
            $table->timestamp(\'rejected_at\')->nullable();
            $table->text(\'rejection_reason\')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index([\'supplier_id\', \'status\']);
            $table->index([\'status\', \'created_at\']);
            $table->index(\'order_id\');';
                
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

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
