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
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index('transactions_order_id_foreign');
            $table->unsignedBigInteger('user_id')->index('transactions_user_id_foreign');
            $table->decimal('amount', 10);
            $table->string('payment_method');
            $table->string('status');
            $table->string('transaction_id')->unique();
            $table->timestamps();
            });
        } else {
            Schema::table('transactions', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('transactions');
                $schemaContent = '$table->bigIncrements(\'id\');
            $table->unsignedBigInteger(\'order_id\')->index(\'transactions_order_id_foreign\');
            $table->unsignedBigInteger(\'user_id\')->index(\'transactions_user_id_foreign\');
            $table->decimal(\'amount\', 10);
            $table->string(\'payment_method\');
            $table->string(\'status\');
            $table->string(\'transaction_id\')->unique();
            $table->timestamps();';
                
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
