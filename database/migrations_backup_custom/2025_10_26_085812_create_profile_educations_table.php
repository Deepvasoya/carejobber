<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_educations', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->nullable();
            $table->integer('degree_level_id')->nullable();
            $table->integer('degree_type_id')->nullable();
            $table->string('degree_title', 150)->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('date_completion', 15)->nullable();
            $table->string('institution', 150)->nullable();
            $table->string('degree_result', 20)->nullable();
            $table->integer('result_type_id')->nullable();
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
        Schema::dropIfExists('profile_educations');
    }
}