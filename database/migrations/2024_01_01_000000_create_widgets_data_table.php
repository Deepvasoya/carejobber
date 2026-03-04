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
        Schema::create('widgets_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->unsignedInteger('widget_id')->index('widgets_data_widget_id_foreign');
            $table->longText('description')->nullable();
            $table->string('extra_field_1')->nullable();
            $table->string('extra_field_2')->nullable();
            $table->string('extra_field_3')->nullable();
            $table->string('extra_field_4')->nullable();
            $table->string('extra_field_5')->nullable();
            $table->string('extra_field_6')->nullable();
            $table->string('extra_field_7')->nullable();
            $table->string('extra_field_8')->nullable();
            $table->string('extra_field_9')->nullable();
            $table->string('extra_field_10')->nullable();
            $table->string('extra_field_11')->nullable();
            $table->string('extra_field_12')->nullable();
            $table->string('extra_field_13')->nullable();
            $table->string('extra_field_14')->nullable();
            $table->string('extra_field_15')->nullable();
            $table->string('extra_field_16')->nullable();
            $table->string('extra_field_17')->nullable();
            $table->string('extra_field_18')->nullable();
            $table->string('extra_field_19')->nullable();
            $table->string('extra_field_20')->nullable();
            $table->string('extra_image_1')->nullable();
            $table->string('extra_image_2')->nullable();
            $table->tinyInteger('radio_button_1')->nullable()->default(0);
            $table->tinyInteger('radio_button_2')->nullable()->default(0);
            $table->tinyInteger('radio_button_3')->nullable()->default(0);
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets_data');
    }
};
