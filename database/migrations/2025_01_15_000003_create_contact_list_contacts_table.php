<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contact_list_contacts')) {
            Schema::create('contact_list_contacts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contact_list_id');
                $table->unsignedBigInteger('marketing_contact_id');
                $table->timestamp('added_at')->useCurrent();
                
                $table->foreign('contact_list_id')->references('id')->on('contact_lists')->onDelete('cascade');
                $table->foreign('marketing_contact_id')->references('id')->on('marketing_contacts')->onDelete('cascade');
                
                $table->unique(['contact_list_id', 'marketing_contact_id'], 'contact_list_contact_unique');
            });
        } else {
            Schema::table('contact_list_contacts', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('contact_list_contacts');
                $schemaContent = '$table->id();
            $table->unsignedBigInteger(\'contact_list_id\');
            $table->unsignedBigInteger(\'marketing_contact_id\');
            $table->timestamp(\'added_at\')->useCurrent();
            
            $table->foreign(\'contact_list_id\')->references(\'id\')->on(\'contact_lists\')->onDelete(\'cascade\');
            $table->foreign(\'marketing_contact_id\')->references(\'id\')->on(\'marketing_contacts\')->onDelete(\'cascade\');
            
            $table->unique([\'contact_list_id\', \'marketing_contact_id\'], \'contact_list_contact_unique\');';
                
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