<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stripe_checkout_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('stripe_checkout_sessions', 'job_id')) {
                $table->unsignedInteger('job_id')->nullable()->after('package_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stripe_checkout_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('stripe_checkout_sessions', 'job_id')) {
                $table->dropColumn('job_id');
            }
        });
    }
};
