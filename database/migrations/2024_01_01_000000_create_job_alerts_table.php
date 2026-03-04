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
        Schema::create('job_alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 500)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('email', 500)->nullable();
            $table->string('unsubscribe_token')->nullable()->index('idx_job_alerts_unsubscribe_token');
            $table->boolean('is_active')->nullable()->default(true)->index('idx_job_alerts_is_active');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->text('search_title')->nullable();
            $table->string('country_id', 500)->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('functional_area_id')->nullable();
            $table->timestamps();

            $table->unique(['unsubscribe_token'], 'unsubscribe_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_alerts');
    }
};
