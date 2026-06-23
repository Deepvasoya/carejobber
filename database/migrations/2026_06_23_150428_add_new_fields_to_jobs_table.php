<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_id')->nullable()->after('id');
            $table->string('union')->nullable()->after('job_id');
            $table->string('fte')->nullable()->after('union');
            $table->text('job_primary_location')->nullable()->after('fte');
            $table->string('hours_per_shift')->nullable()->after('job_primary_location');
            $table->string('shifts_per_cycle')->nullable()->after('hours_per_shift');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['job_id', 'union', 'fte', 'job_primary_location', 'hours_per_shift', 'shifts_per_cycle']);
        });
    }
};
