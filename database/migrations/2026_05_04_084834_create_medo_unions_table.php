<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoUnionsTable extends Migration
{
    public function up()
    {
        Schema::create('medo_unions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('acronym')->nullable();
            $table->foreignId('province_id')->nullable()->constrained('medo_provinces')->onDelete('set null');
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_unions');
    }
}
