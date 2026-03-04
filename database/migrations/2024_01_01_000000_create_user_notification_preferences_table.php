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
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->boolean('new_job_matches')->default(true);
            $table->boolean('application_updates')->default(true);
            $table->boolean('company_jobs')->default(true);
            $table->boolean('job_alerts')->default(true);
            $table->boolean('profile_reminders')->default(true);
            $table->boolean('messages')->default(true);
            $table->boolean('security_alerts')->default(true);
            $table->boolean('app_updates')->default(true);
            $table->boolean('marketing')->default(false);
            $table->boolean('quiet_hours_enabled')->default(true);
            $table->time('quiet_hours_start')->default('22:00:00');
            $table->time('quiet_hours_end')->default('08:00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
