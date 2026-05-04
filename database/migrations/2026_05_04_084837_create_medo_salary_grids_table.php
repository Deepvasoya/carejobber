<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoSalaryGridsTable extends Migration
{
    public function up()
    {
        Schema::create('medo_salary_grids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('medo_categories')->onDelete('cascade');
            $table->foreignId('province_id')->constrained('medo_provinces')->onDelete('cascade');
            $table->foreignId('union_id')->nullable()->constrained('medo_unions')->onDelete('cascade');
            $table->integer('step');
            $table->decimal('hourly_rate', 6, 2);
            $table->date('effective_date')->nullable();
            $table->string('source_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_salary_grids');
    }
}
