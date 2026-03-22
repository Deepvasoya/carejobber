<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('packages')
            ->where('package_for', 'employer')
            ->where('type', 'monthly_recurring')
            ->update(['subscription_unlimited_jobs' => true]);
    }

    public function down(): void
    {
        DB::table('packages')
            ->where('package_for', 'employer')
            ->where('type', 'monthly_recurring')
            ->update(['subscription_unlimited_jobs' => false]);
    }
};
