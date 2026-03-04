<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavouriteApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favourite_applicants', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->nullable();
            $table->integer('job_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->string('status', 255)->nullable();
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
        Schema::dropIfExists('favourite_applicants');
    }
}