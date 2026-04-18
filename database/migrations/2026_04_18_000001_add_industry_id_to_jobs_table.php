<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('jobs', 'industry_id')) {
                $table->integer('industry_id')->nullable()->default(0)->after('functional_area_id');
            }
            if (! Schema::hasColumn('jobs', 'custom_industry')) {
                $table->string('custom_industry', 200)->nullable()->after('industry_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('jobs', 'industry_id') ? 'industry_id' : null,
                Schema::hasColumn('jobs', 'custom_industry') ? 'custom_industry' : null,
            ]));
        });
    }
};
