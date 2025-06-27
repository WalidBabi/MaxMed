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
        if (!Schema::hasTable('supplier_category_types')) {
            Schema::create('supplier_category_types', function (Blueprint $table) {
                $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            });
        }
    });
        } else {
            Schema::table('supplier_category_types', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('supplier_category_types');
                $schemaContent = '$table->id();
            $table->string(\'name\');
            $table->string(\'slug\')->unique();
            $table->text(\'description\')->nullable();
            $table->boolean(\'is_active\')->default(true);
            $table->timestamps();
            $table->softDeletes();';
                
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

        // Create pivot table for supplier categories
        Schema::create('supplier_category_type_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_category_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Use a shorter name for the unique index
            $table->unique(['user_id', 'supplier_category_type_id'], 'sct_user_unique');
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