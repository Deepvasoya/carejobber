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
        Schema::create('job_apply_rejected', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('apply_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('job_id')->nullable();
            $table->integer('cv_id')->nullable();
            $table->integer('current_salary')->nullable();
            $table->integer('expected_salary')->nullable();
            $table->string('salary_currency', 5)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_apply_rejected');
    }
};
