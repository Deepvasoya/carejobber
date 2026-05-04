<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoEmployersTable extends Migration
{
    public function up()
    {
        Schema::create('medo_employers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->enum('type', ['public_health', 'ltc', 'agency', 'private_clinic'])->nullable();
            $table->foreignId('province_id')->constrained('medo_provinces')->onDelete('cascade');
            $table->foreignId('health_authority_id')->nullable()->constrained('medo_health_authorities')->onDelete('set null');
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_employers');
    }
}
