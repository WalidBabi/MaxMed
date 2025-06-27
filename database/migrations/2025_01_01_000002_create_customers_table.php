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
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable();
            
            // Billing Address
            $table->string('billing_street')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_country')->nullable();
            
            // Shipping Address (if different from billing)
            $table->string('shipping_street')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_country')->nullable();
            
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            });
        } else {
            Schema::table('customers', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('customers');
                $schemaContent = '$table->id();
            $table->unsignedBigInteger(\'user_id\')->nullable()->constrained()->nullOnDelete();
            $table->string(\'name\');
            $table->string(\'email\')->nullable();
            $table->string(\'phone\')->nullable();
            $table->string(\'company_name\')->nullable();
            $table->string(\'tax_id\')->nullable();
            
            // Billing Address
            $table->string(\'billing_street\')->nullable();
            $table->string(\'billing_city\')->nullable();
            $table->string(\'billing_state\')->nullable();
            $table->string(\'billing_zip\')->nullable();
            $table->string(\'billing_country\')->nullable();
            
            // Shipping Address (if different from billing)
            $table->string(\'shipping_street\')->nullable();
            $table->string(\'shipping_city\')->nullable();
            $table->string(\'shipping_state\')->nullable();
            $table->string(\'shipping_zip\')->nullable();
            $table->string(\'shipping_country\')->nullable();
            
            $table->text(\'notes\')->nullable();
            $table->boolean(\'is_active\')->default(true);
            $table->timestamps();
            $table->softDeletes();';
                
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
