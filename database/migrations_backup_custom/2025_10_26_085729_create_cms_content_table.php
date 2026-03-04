<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_content', function (Blueprint $table) {
            $table->id('id');
            $table->integer('page_id')->nullable();
            $table->text('page_title')->nullable();
            $table->mediumText('page_content')->nullable();
            $table->timestamps()->useCurrent();
            $table->string('updated_at')->nullable();
            $table->string('lang', 10)->nullable()->default('en');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_content');
    }
}