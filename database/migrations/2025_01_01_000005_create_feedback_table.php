<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('rating');
            $table->text('feedback');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            });
        }
    });
        } else {
            Schema::table('feedback', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('feedback');
                $schemaContent = '$table->id();
            $table->unsignedBigInteger(\'user_id\');
            $table->unsignedBigInteger(\'order_id\');
            $table->integer(\'rating\');
            $table->text(\'feedback\');
            $table->timestamps();

            $table->foreign(\'user_id\')->references(\'id\')->on(\'users\')->onDelete(\'cascade\');
            $table->foreign(\'order_id\')->references(\'id\')->on(\'orders\')->onDelete(\'cascade\');';
                
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

    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
}; 