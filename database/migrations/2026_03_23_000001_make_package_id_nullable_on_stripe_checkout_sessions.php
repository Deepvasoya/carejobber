<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Resume unlock and other non-package Stripe flows store sessions without a package_id.
     */
    public function up(): void
    {
        if (! Schema::hasTable('stripe_checkout_sessions')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE stripe_checkout_sessions MODIFY package_id INT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('stripe_checkout_sessions')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('UPDATE stripe_checkout_sessions SET package_id = 0 WHERE package_id IS NULL');
            DB::statement('ALTER TABLE stripe_checkout_sessions MODIFY package_id INT UNSIGNED NOT NULL');
        }
    }
};
