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
        if (!Schema::hasTable('product_specifications')) {
            Schema::create('product_specifications', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('specification_key')->comment('e.g., test_count, detection_time, sensitivity');
            $table->text('specification_value')->comment('The actual value');
            $table->string('unit', 50)->nullable()->comment('e.g., tests, minutes, %, ml');
            $table->string('category', 100)->nullable()->comment('e.g., Performance, Physical, Regulatory');
            $table->string('display_name')->comment('Human-readable name for frontend');
            $table->text('description')->nullable()->comment('Additional details about the specification');
            $table->integer('sort_order')->default(0)->comment('For ordering display');
            $table->boolean('is_filterable')->default(false)->comment('Can be used as a filter');
            $table->boolean('is_searchable')->default(false)->comment('Can be searched');
            $table->boolean('show_on_listing')->default(false)->comment('Show in product listings');
            $table->boolean('show_on_detail')->default(true)->comment('Show on product detail page');
            $table->timestamps();

            // Indexes only - no foreign key constraints for now
            $table->index(['product_id', 'specification_key'], 'product_specifications_product_id_specification_key_index');
            $table->index(['specification_key', 'is_filterable'], 'product_specifications_specification_key_is_filterable_index');
            $table->index(['category', 'sort_order'], 'product_specifications_category_sort_order_index');
            });
        } else {
            Schema::table('product_specifications', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('product_specifications');
                $schemaContent = '$table->id();
            $table->unsignedBigInteger(\'product_id\');
            $table->string(\'specification_key\')->comment(\'e.g., test_count, detection_time, sensitivity\');
            $table->text(\'specification_value\')->comment(\'The actual value\');
            $table->string(\'unit\', 50)->nullable()->comment(\'e.g., tests, minutes, %, ml\');
            $table->string(\'category\', 100)->nullable()->comment(\'e.g., Performance, Physical, Regulatory\');
            $table->string(\'display_name\')->comment(\'Human-readable name for frontend\');
            $table->text(\'description\')->nullable()->comment(\'Additional details about the specification\');
            $table->integer(\'sort_order\')->default(0)->comment(\'For ordering display\');
            $table->boolean(\'is_filterable\')->default(false)->comment(\'Can be used as a filter\');
            $table->boolean(\'is_searchable\')->default(false)->comment(\'Can be searched\');
            $table->boolean(\'show_on_listing\')->default(false)->comment(\'Show in product listings\');
            $table->boolean(\'show_on_detail\')->default(true)->comment(\'Show on product detail page\');
            $table->timestamps();

            // Indexes only - no foreign key constraints for now
            $table->index([\'product_id\', \'specification_key\'], \'product_specifications_product_id_specification_key_index\');
            $table->index([\'specification_key\', \'is_filterable\'], \'product_specifications_specification_key_is_filterable_index\');
            $table->index([\'category\', \'sort_order\'], \'product_specifications_category_sort_order_index\');';
                
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
        
        // Note: Foreign key constraint can be added later if needed
        // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
