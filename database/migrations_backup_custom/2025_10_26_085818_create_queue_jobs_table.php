<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_jobs', function (Blueprint $table) {
            $table->id('id');
            $table->string('queue', 255);
            $table->longText('payload');
            $table->tinyInteger('attempts');
            $table->integer('reserved_at')->nullable();
            $table->integer('available_at');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue_jobs');
    }
}