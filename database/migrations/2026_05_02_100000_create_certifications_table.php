<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(99999);
            $table->timestamps();
        });

        Schema::create('user_certifications', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('certification_id');
            $table->primary(['user_id', 'certification_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_certifications');
        Schema::dropIfExists('certifications');
    }
};