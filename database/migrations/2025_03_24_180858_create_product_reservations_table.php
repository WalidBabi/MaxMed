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
        if (!Schema::hasTable('product_reservations')) {
            Schema::create('product_reservations', function (Blueprint $table) {
                $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('product_reservations_product_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('product_reservations_user_id_foreign');
            $table->integer('quantity');
            $table->string('session_id');
            $table->timestamp('expires_at')->useCurrentOnUpdate()->useCurrent();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();
            });
        } else {
            Schema::table('product_reservations', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('product_reservations');
                $schemaContent = '$table->bigIncrements(\'id\');
            $table->unsignedBigInteger(\'product_id\')->index(\'product_reservations_product_id_foreign\');
            $table->unsignedBigInteger(\'user_id\')->nullable()->index(\'product_reservations_user_id_foreign\');
            $table->integer(\'quantity\');
            $table->string(\'session_id\');
            $table->timestamp(\'expires_at\')->useCurrentOnUpdate()->useCurrent();
            $table->enum(\'status\', [\'pending\', \'confirmed\', \'cancelled\'])->default(\'pending\');
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
