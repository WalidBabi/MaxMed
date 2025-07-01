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
        if (!Schema::hasTable('system_feedback')) {
            Schema::create('system_feedback', function (Blueprint $table) {
                $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['bug_report', 'feature_request', 'improvement', 'general'])->default('general');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('system_feedback', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('system_feedback');
                $schemaContent = '$table->id();
            $table->foreignId(\'user_id\')->constrained()->onDelete(\'cascade\');
            $table->enum(\'type\', [\'bug_report\', \'feature_request\', \'improvement\', \'general\'])->default(\'general\');
            $table->string(\'title\');
            $table->text(\'description\');
            $table->enum(\'priority\', [\'low\', \'medium\', \'high\'])->default(\'medium\');
            $table->enum(\'status\', [\'pending\', \'in_progress\', \'completed\', \'rejected\'])->default(\'pending\');
            $table->text(\'admin_response\')->nullable();
            $table->timestamps();';
                
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
