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
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable()->index('categories_parent_id_foreign');
            $table->string('name')->unique();
            $table->timestamps();
            $table->string('image_url')->nullable();
            });
        } else {
            Schema::table('categories', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('categories');
                $schemaContent = '$table->bigIncrements(\'id\');
            $table->unsignedBigInteger(\'parent_id\')->nullable()->index(\'categories_parent_id_foreign\');
            $table->string(\'name\')->unique();
            $table->timestamps();
            $table->string(\'image_url\')->nullable();';
                
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
