<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jobs') && ! Schema::hasColumn('jobs', 'apply_type')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->string('apply_type', 20)->default('internal')->after('external_job');
            });

            DB::table('jobs')
                ->where('external_job', 'yes')
                ->update(['apply_type' => 'external']);
        }

        if (Schema::hasTable('jobsb') && ! Schema::hasColumn('jobsb', 'apply_type')) {
            Schema::table('jobsb', function (Blueprint $table) {
                $table->string('apply_type', 20)->default('internal')->after('external_job');
            });

            DB::table('jobsb')
                ->where('external_job', 'yes')
                ->update(['apply_type' => 'external']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('jobs') && Schema::hasColumn('jobs', 'apply_type')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropColumn('apply_type');
            });
        }

        if (Schema::hasTable('jobsb') && Schema::hasColumn('jobsb', 'apply_type')) {
            Schema::table('jobsb', function (Blueprint $table) {
                $table->dropColumn('apply_type');
            });
        }
    }
};
