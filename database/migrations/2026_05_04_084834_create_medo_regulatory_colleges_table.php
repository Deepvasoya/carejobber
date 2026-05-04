<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoRegulatoryCollegesTable extends Migration
{
    public function up()
    {
        Schema::create('medo_regulatory_colleges', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('acronym')->nullable();
            $table->foreignId('province_id')->constrained('medo_provinces')->onDelete('cascade');
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_regulatory_colleges');
    }
}
