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
        Schema::table('faq_categories', function (Blueprint $table) {
            $table->unsignedInteger('faq_section_id')->nullable()->after('id');
            $table->foreign('faq_section_id')->references('id')->on('faq_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faq_categories', function (Blueprint $table) {
            $table->dropForeign(['faq_section_id']);
            $table->dropColumn('faq_section_id');
        });
    }
};
