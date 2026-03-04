<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id('id');
            $table->string('heading', 255);
            $table->string('slug', 255);
            $table->string('cate_id', 500);
            $table->text('content');
            $table->string('image', 255)->nullable();
            $table->string('featured')->nullable()->default('0');
            $table->string('meta_title', 255)->nullable();
            $table->string('lang', 255)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_descriptions', 255)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps()->nullable();
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
        Schema::dropIfExists('blogs');
    }
}