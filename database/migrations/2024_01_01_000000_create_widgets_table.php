<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->unsignedInteger('widget_page_id')->index('widgets_widget_page_id_foreign');
            $table->longText('is_description')->nullable();
            $table->longText('extra_fields')->nullable();
            $table->string('extra_field_title_1')->nullable();
            $table->string('extra_field_title_2')->nullable();
            $table->string('extra_field_title_3')->nullable();
            $table->string('extra_field_title_4')->nullable();
            $table->string('extra_field_title_5')->nullable();
            $table->string('extra_field_title_6')->nullable();
            $table->string('extra_field_title_7')->nullable();
            $table->string('is_extra_image_title_1')->nullable();
            $table->string('extra_image_title_1')->nullable();
            $table->string('extra_image_1_height')->nullable();
            $table->string('extra_image_1_width')->nullable();
            $table->string('is_extra_image_title_2')->nullable();
            $table->string('extra_image_title_2')->nullable();
            $table->string('extra_image_2_height')->nullable();
            $table->string('extra_image_2_width')->nullable();
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
