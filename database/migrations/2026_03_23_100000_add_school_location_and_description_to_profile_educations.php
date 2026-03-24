<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_educations', function (Blueprint $table) {
            if (! Schema::hasColumn('profile_educations', 'school_location')) {
                $table->string('school_location', 500)->nullable()->after('institution');
            }
            if (! Schema::hasColumn('profile_educations', 'description')) {
                $table->text('description')->nullable()->after('school_location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profile_educations', function (Blueprint $table) {
            if (Schema::hasColumn('profile_educations', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('profile_educations', 'school_location')) {
                $table->dropColumn('school_location');
            }
        });
    }
};
