<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoPageContentsTable extends Migration
{
    public function up()
    {
        Schema::create('medo_page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_page_contents');
    }
}
