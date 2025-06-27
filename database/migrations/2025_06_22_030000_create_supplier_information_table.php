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
        if (!Schema::hasTable('supplier_information')) {
            Schema::create('supplier_information', function (Blueprint $table) {
                $table->id();
            
            // Link to Users table - now with foreign key constraint since users table is InnoDB
            $table->unsignedBigInteger('user_id');
            
            // Business Information
            $table->string('company_name');
            $table->string('business_registration_number')->nullable();
            $table->string('tax_registration_number')->nullable();
            $table->string('trade_license_number')->nullable();
            
            // Contact Information
            $table->text('business_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('UAE');
            $table->string('phone_primary')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('fax')->nullable();
            $table->string('website')->nullable();
            
            // Contact Persons
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_email')->nullable();
            $table->string('primary_contact_phone')->nullable();
            $table->string('primary_contact_position')->nullable();
            
            $table->string('secondary_contact_name')->nullable();
            $table->string('secondary_contact_email')->nullable();
            $table->string('secondary_contact_phone')->nullable();
            $table->string('secondary_contact_position')->nullable();
            
            // Banking Information
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('beneficiary_name')->nullable();
            
            // Business Terms
            $table->integer('payment_terms_days')->default(30);
            $table->string('currency_preference', 3)->default('AED');
            $table->decimal('minimum_order_value', 10, 2)->nullable();
            $table->integer('standard_lead_time_days')->default(7);
            $table->text('terms_conditions')->nullable();
            
            // Capabilities & Certifications
            $table->json('certifications')->nullable();
            $table->json('specializations')->nullable();
            $table->text('company_description')->nullable();
            $table->integer('years_in_business')->nullable();
            $table->integer('number_of_employees')->nullable();
            
            // Performance Metrics
            $table->decimal('overall_rating', 3, 2)->default(5.00);
            $table->integer('total_orders_fulfilled')->default(0);
            $table->decimal('on_time_delivery_rate', 5, 2)->default(100.00);
            $table->decimal('quality_rating', 3, 2)->default(5.00);
            $table->timestamp('last_order_date')->nullable();
            
            // Status & Preferences
            $table->enum('status', ['active', 'inactive', 'pending_approval', 'suspended'])->default('pending_approval');
            $table->boolean('accepts_rush_orders')->default(false);
            $table->boolean('international_shipping')->default(false);
            $table->json('shipping_methods')->nullable();
            
            // Documents
            $table->string('trade_license_file')->nullable();
            $table->string('tax_certificate_file')->nullable();
            $table->string('company_profile_file')->nullable();
            $table->json('certification_files')->nullable();
            
            // Audit Trail - with foreign key constraints now that users table is InnoDB
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            
            $table->timestamps();
            
            // Create indexes for performance
            $table->index(['status', 'created_at']);
            $table->index('company_name');
            $table->index(['country', 'city']);
            $table->index('overall_rating');
            $table->unique('user_id'); // One supplier info per user
            
            // Foreign key constraints (now supported with InnoDB)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    });
        } else {
            Schema::table('supplier_information', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('supplier_information');
                $schemaContent = '$table->id();
            
            // Link to Users table - now with foreign key constraint since users table is InnoDB
            $table->unsignedBigInteger(\'user_id\');
            
            // Business Information
            $table->string(\'company_name\');
            $table->string(\'business_registration_number\')->nullable();
            $table->string(\'tax_registration_number\')->nullable();
            $table->string(\'trade_license_number\')->nullable();
            
            // Contact Information
            $table->text(\'business_address\')->nullable();
            $table->string(\'city\')->nullable();
            $table->string(\'state_province\')->nullable();
            $table->string(\'postal_code\')->nullable();
            $table->string(\'country\')->default(\'UAE\');
            $table->string(\'phone_primary\')->nullable();
            $table->string(\'phone_secondary\')->nullable();
            $table->string(\'fax\')->nullable();
            $table->string(\'website\')->nullable();
            
            // Contact Persons
            $table->string(\'primary_contact_name\')->nullable();
            $table->string(\'primary_contact_email\')->nullable();
            $table->string(\'primary_contact_phone\')->nullable();
            $table->string(\'primary_contact_position\')->nullable();
            
            $table->string(\'secondary_contact_name\')->nullable();
            $table->string(\'secondary_contact_email\')->nullable();
            $table->string(\'secondary_contact_phone\')->nullable();
            $table->string(\'secondary_contact_position\')->nullable();
            
            // Banking Information
            $table->string(\'bank_name\')->nullable();
            $table->string(\'bank_branch\')->nullable();
            $table->string(\'account_number\')->nullable();
            $table->string(\'iban\')->nullable();
            $table->string(\'swift_code\')->nullable();
            $table->string(\'beneficiary_name\')->nullable();
            
            // Business Terms
            $table->integer(\'payment_terms_days\')->default(30);
            $table->string(\'currency_preference\', 3)->default(\'AED\');
            $table->decimal(\'minimum_order_value\', 10, 2)->nullable();
            $table->integer(\'standard_lead_time_days\')->default(7);
            $table->text(\'terms_conditions\')->nullable();
            
            // Capabilities & Certifications
            $table->json(\'certifications\')->nullable();
            $table->json(\'specializations\')->nullable();
            $table->text(\'company_description\')->nullable();
            $table->integer(\'years_in_business\')->nullable();
            $table->integer(\'number_of_employees\')->nullable();
            
            // Performance Metrics
            $table->decimal(\'overall_rating\', 3, 2)->default(5.00);
            $table->integer(\'total_orders_fulfilled\')->default(0);
            $table->decimal(\'on_time_delivery_rate\', 5, 2)->default(100.00);
            $table->decimal(\'quality_rating\', 3, 2)->default(5.00);
            $table->timestamp(\'last_order_date\')->nullable();
            
            // Status & Preferences
            $table->enum(\'status\', [\'active\', \'inactive\', \'pending_approval\', \'suspended\'])->default(\'pending_approval\');
            $table->boolean(\'accepts_rush_orders\')->default(false);
            $table->boolean(\'international_shipping\')->default(false);
            $table->json(\'shipping_methods\')->nullable();
            
            // Documents
            $table->string(\'trade_license_file\')->nullable();
            $table->string(\'tax_certificate_file\')->nullable();
            $table->string(\'company_profile_file\')->nullable();
            $table->json(\'certification_files\')->nullable();
            
            // Audit Trail - with foreign key constraints now that users table is InnoDB
            $table->unsignedBigInteger(\'created_by\')->nullable();
            $table->unsignedBigInteger(\'updated_by\')->nullable();
            $table->timestamp(\'approved_at\')->nullable();
            $table->unsignedBigInteger(\'approved_by\')->nullable();
            
            $table->timestamps();
            
            // Create indexes for performance
            $table->index([\'status\', \'created_at\']);
            $table->index(\'company_name\');
            $table->index([\'country\', \'city\']);
            $table->index(\'overall_rating\');
            $table->unique(\'user_id\'); // One supplier info per user
            
            // Foreign key constraints (now supported with InnoDB)
            $table->foreign(\'user_id\')->references(\'id\')->on(\'users\')->onDelete(\'cascade\');
            $table->foreign(\'created_by\')->references(\'id\')->on(\'users\')->onDelete(\'set null\');
            $table->foreign(\'updated_by\')->references(\'id\')->on(\'users\')->onDelete(\'set null\');
            $table->foreign(\'approved_by\')->references(\'id\')->on(\'users\')->onDelete(\'set null\');';
                
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