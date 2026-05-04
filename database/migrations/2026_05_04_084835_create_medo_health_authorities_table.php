<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoHealthAuthoritiesTable extends Migration
{
    public function up()
    {
        Schema::create('medo_health_authorities', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('acronym')->nullable();
            $table->foreignId('province_id')->constrained('medo_provinces')->onDelete('cascade');
            $table->string('website')->nullable();
            $table->string('careers_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_health_authorities');
    }
}
