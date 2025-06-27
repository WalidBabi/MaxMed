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
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index('order_items_order_id_foreign');
            $table->unsignedBigInteger('product_id')->index('order_items_product_id_foreign');
            $table->integer('quantity');
            $table->decimal('price', 10);
            $table->timestamps();
            });
        }
    });
        } else {
            Schema::table('order_items', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('order_items');
                $schemaContent = '$table->bigIncrements(\'id\');
            $table->unsignedBigInteger(\'order_id\')->index(\'order_items_order_id_foreign\');
            $table->unsignedBigInteger(\'product_id\')->index(\'order_items_product_id_foreign\');
            $table->integer(\'quantity\');
            $table->decimal(\'price\', 10);
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
