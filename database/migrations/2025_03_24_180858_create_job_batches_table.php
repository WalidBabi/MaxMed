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
        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
            });
        }
    });
        } else {
            Schema::table('job_batches', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('job_batches');
                $schemaContent = '$table->string(\'id\')->primary();
            $table->string(\'name\');
            $table->integer(\'total_jobs\');
            $table->integer(\'pending_jobs\');
            $table->integer(\'failed_jobs\');
            $table->longText(\'failed_job_ids\');
            $table->mediumText(\'options\')->nullable();
            $table->integer(\'cancelled_at\')->nullable();
            $table->integer(\'created_at\');
            $table->integer(\'finished_at\')->nullable();';
                
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
