<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
            $table->string('name');
            $table->string('subject');
            $table->longText('html_content')->nullable();
            $table->longText('text_content')->nullable();
            $table->json('variables')->nullable(); // Available template variables
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index('created_by');
            $table->index(['category', 'is_active']);
            });
        } else {
            Schema::table('email_templates', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('email_templates');
                $schemaContent = '$table->id();
            $table->string(\'name\');
            $table->string(\'subject\');
            $table->longText(\'html_content\')->nullable();
            $table->longText(\'text_content\')->nullable();
            $table->json(\'variables\')->nullable(); // Available template variables
            $table->string(\'category\')->nullable();
            $table->boolean(\'is_active\')->default(true);
            $table->unsignedBigInteger(\'created_by\');
            $table->timestamps();
            
            $table->index(\'created_by\');
            $table->index([\'category\', \'is_active\']);';
                
                // Parse the schema content to find column definitions
                preg_match_all('/\$table->([^;]+);/', $schemaContent, $columnMatches);
                foreach ($columnMatches[1] as $columnDef) {
                    if (preg_match('/^(\w+)\([\'\"]([^\'\"]+)[\'\"]\)/', $columnDef, $colMatch)) {
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

    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
}; 