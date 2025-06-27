<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contact_lists')) {
            Schema::create('contact_lists', function (Blueprint $table) {
                $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('criteria')->nullable(); // For dynamic lists based on criteria
            $table->enum('type', ['static', 'dynamic'])->default('static');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index('created_by');
            });
        } else {
            Schema::table('contact_lists', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('contact_lists');
                $schemaContent = '$table->id();
            $table->string(\'name\');
            $table->text(\'description\')->nullable();
            $table->json(\'criteria\')->nullable(); // For dynamic lists based on criteria
            $table->enum(\'type\', [\'static\', \'dynamic\'])->default(\'static\');
            $table->boolean(\'is_active\')->default(true);
            $table->unsignedBigInteger(\'created_by\');
            $table->timestamps();
            
            $table->index(\'created_by\');';
                
                // Parse the schema content to find column definitions
                preg_match_all('/\$table->([^;]+);/', $schemaContent, $columnMatches);
                foreach ($columnMatches[1] as $columnDef) {
                    if (preg_match('/^(\w+)\([\'"]([^\'"]+)[\'"]\)/', $columnDef, $colMatch)) {
                        $columnName = $colMatch[2];
                        if (!in_array($columnName, $columns)) {
                            $table->{$colMatch[1]}($columnName);
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
}; 