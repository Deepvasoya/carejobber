<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create user_push_tokens table
        Schema::create('user_push_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('push_token', 255);
            $table->enum('platform', ['ios', 'android']);
            $table->string('device_id', 255)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'device_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create user_notification_preferences table
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
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
            $table->time('quiet_hours_start')->default('22:00');
            $table->time('quiet_hours_end')->default('08:00');
            $table->timestamps();
            
            $table->unique('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 50);
            $table->string('title', 255);
            $table->text('body');
            $table->json('data')->nullable();
            $table->boolean('sound')->default(true);
            $table->integer('badge')->nullable();
            $table->enum('priority', ['min', 'low', 'default', 'high', 'max'])->default('default');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'read_at']);
            $table->index('type');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('user_notification_preferences');
        Schema::dropIfExists('user_push_tokens');
    }
}
