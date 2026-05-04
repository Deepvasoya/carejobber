<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('medo_cities', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->foreignId('province_id')->constrained('medo_provinces')->onDelete('cascade');
            $table->string('health_region')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('population')->nullable();
            $table->string('primary_facility')->nullable();
            $table->timestamps();
            
            $table->unique(['slug', 'province_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_cities');
    }
}
