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
        if (!Schema::hasTable('deliveries')) {
            Schema::create('deliveries', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('tracking_number')->unique()->nullable();
            $table->string('status')->default('pending'); // pending, processing, in_transit, delivered, cancelled
            $table->string('carrier')->nullable();
            $table->text('shipping_address');
            $table->text('billing_address')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_weight', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            // Add foreign key constraint separately
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
            });
        }
    });
        } else {
            Schema::table('deliveries', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('deliveries');
                $schemaContent = '$table->id();
            $table->unsignedBigInteger(\'order_id\')->index();
            $table->string(\'tracking_number\')->unique()->nullable();
            $table->string(\'status\')->default(\'pending\'); // pending, processing, in_transit, delivered, cancelled
            $table->string(\'carrier\')->nullable();
            $table->text(\'shipping_address\');
            $table->text(\'billing_address\')->nullable();
            $table->decimal(\'shipping_cost\', 10, 2)->default(0);
            $table->decimal(\'total_weight\', 10, 2)->nullable();
            $table->text(\'notes\')->nullable();
            $table->timestamp(\'shipped_at\')->nullable();
            $table->timestamp(\'delivered_at\')->nullable();
            $table->timestamps();

            // Add foreign key constraint separately
            $table->foreign(\'order_id\')
                  ->references(\'id\')
                  ->on(\'orders\')
                  ->onDelete(\'cascade\');';
                
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
