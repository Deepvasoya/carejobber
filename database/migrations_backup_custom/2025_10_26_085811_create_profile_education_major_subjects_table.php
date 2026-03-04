<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileEducationMajorSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_education_major_subjects', function (Blueprint $table) {
            $table->id('id');
            $table->integer('profile_education_id')->nullable();
            $table->integer('major_subject_id')->nullable();
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
        Schema::dropIfExists('profile_education_major_subjects');
    }
}