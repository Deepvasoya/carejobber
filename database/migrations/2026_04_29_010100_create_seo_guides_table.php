<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seo_guides')) {
            Schema::create('seo_guides', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 191)->unique();
                $table->string('title', 255);
                $table->text('excerpt')->nullable();
                $table->longText('body');
                $table->string('seo_title', 255)->nullable();
                $table->text('seo_description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_guides');
    }
};
