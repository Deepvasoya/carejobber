<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('slug', 191)->unique();
            $table->string('icon_url', 2048)->nullable();
            $table->string('field_type', 32);
            $table->json('options')->nullable()->comment('Choices for select, radio, multiselect, checkboxes');
            $table->json('contexts')->nullable()->comment('Where shown: profile, job_listing, resume_builder');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
