<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoCategoryProvinceSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('medo_category_province_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('medo_categories')->onDelete('cascade');
            $table->foreignId('province_id')->constrained('medo_provinces')->onDelete('cascade');
            $table->foreignId('union_id')->nullable()->constrained('medo_unions')->onDelete('set null');
            $table->foreignId('regulatory_college_id')->nullable()->constrained('medo_regulatory_colleges')->onDelete('set null');
            $table->decimal('wage_min', 6, 2)->nullable();
            $table->decimal('wage_max', 6, 2)->nullable();
            $table->string('pension_plan')->nullable();
            $table->decimal('shift_premium_evening', 5, 2)->nullable();
            $table->decimal('shift_premium_night', 5, 2)->nullable();
            $table->text('certification_requirements')->nullable();
            $table->text('training_pathways')->nullable();
            $table->text('ien_pathway')->nullable();
            $table->timestamps();

            $table->unique(['category_id', 'province_id'], 'cat_prov_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_category_province_settings');
    }
}
