<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoScraperRunsTable extends Migration
{
    public function up()
    {
        Schema::create('medo_scraper_runs', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->integer('jobs_added')->default(0);
            $table->integer('jobs_updated')->default(0);
            $table->integer('jobs_expired')->default(0);
            $table->text('errors')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_scraper_runs');
    }
}
