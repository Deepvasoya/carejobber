<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_apply', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->nullable();
            $table->integer('job_id')->nullable();
            $table->integer('cv_id')->nullable();
            $table->integer('current_salary')->nullable();
            $table->integer('expected_salary')->nullable();
            $table->string('salary_currency', 5)->nullable();
            $table->string('status', 255)->nullable()->default('applied');
            $table->timestamps()->nullable()->useCurrent();
            $table->string('updated_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_apply');
    }
}