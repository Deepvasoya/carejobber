<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobApplyRejectedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_apply_rejected', function (Blueprint $table) {
            $table->id('id');
            $table->integer('apply_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('job_id')->nullable();
            $table->integer('cv_id')->nullable();
            $table->integer('current_salary')->nullable();
            $table->integer('expected_salary')->nullable();
            $table->string('salary_currency', 5)->nullable();
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
        Schema::dropIfExists('job_apply_rejected');
    }
}