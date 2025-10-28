<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'alternate_names')) {
			Schema::table('customers', function (Blueprint $table) {
				$table->json('alternate_names')->nullable()->after('name');
			});
		}
	}

	public function down(): void
	{
		if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'alternate_names')) {
			Schema::table('customers', function (Blueprint $table) {
				$table->dropColumn('alternate_names');
			});
		}
	}
};


