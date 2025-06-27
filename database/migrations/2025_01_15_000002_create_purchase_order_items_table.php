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
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->text('item_description');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2);
            $table->string('unit_of_measure')->nullable();
            $table->text('specifications')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            });
        } else {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('purchase_order_items');
                $schemaContent = '$table->id();
            $table->foreignId(\'purchase_order_id\')->constrained(\'purchase_orders\')->onDelete(\'cascade\');
            $table->foreignId(\'product_id\')->nullable()->constrained(\'products\')->onDelete(\'set null\');
            $table->text(\'item_description\');
            $table->decimal(\'quantity\', 10, 2);
            $table->decimal(\'unit_price\', 10, 2);
            $table->decimal(\'discount_percentage\', 5, 2)->default(0);
            $table->decimal(\'discount_amount\', 10, 2)->default(0);
            $table->decimal(\'line_total\', 10, 2);
            $table->string(\'unit_of_measure\')->nullable();
            $table->text(\'specifications\')->nullable();
            $table->integer(\'sort_order\')->default(0);
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