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
        Schema::create('profile_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('image', 120)->nullable();
            $table->text('description')->nullable();
            $table->tinyText('url')->nullable();
            $table->timestamp('date_start')->useCurrentOnUpdate()->nullable();
            $table->timestamp('date_end')->useCurrentOnUpdate()->nullable();
            $table->boolean('is_on_going')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_projects');
    }
};
