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
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('orders_user_id_foreign');
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10);
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_zipcode');
            $table->string('shipping_phone');
            $table->text('notes')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('orders', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('orders');
                $schemaContent = '$table->bigIncrements(\'id\');
            $table->unsignedBigInteger(\'user_id\')->index(\'orders_user_id_foreign\');
            $table->string(\'order_number\')->unique();
            $table->decimal(\'total_amount\', 10);
            $table->enum(\'status\', [\'pending\', \'processing\', \'shipped\', \'delivered\', \'cancelled\'])->default(\'pending\');
            $table->string(\'shipping_address\');
            $table->string(\'shipping_city\');
            $table->string(\'shipping_state\');
            $table->string(\'shipping_zipcode\');
            $table->string(\'shipping_phone\');
            $table->text(\'notes\')->nullable();
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
