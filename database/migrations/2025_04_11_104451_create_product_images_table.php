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
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('image_path');
            $table->string('image_url');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
            });
        } else {
            Schema::table('product_images', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('product_images');
                $schemaContent = '$table->id();
            $table->unsignedBigInteger(\'product_id\');
            $table->string(\'image_path\');
            $table->string(\'image_url\');
            $table->integer(\'sort_order\')->default(0);
            $table->boolean(\'is_primary\')->default(false);
            $table->timestamps();

            $table->foreign(\'product_id\')
                  ->references(\'id\')
                  ->on(\'products\')
                  ->onDelete(\'cascade\');';
                
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
