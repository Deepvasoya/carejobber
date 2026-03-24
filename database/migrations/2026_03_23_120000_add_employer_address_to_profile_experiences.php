<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_experiences', function (Blueprint $table) {
            if (! Schema::hasColumn('profile_experiences', 'employer_address')) {
                $table->string('employer_address', 500)->nullable()->after('company');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profile_experiences', function (Blueprint $table) {
            if (Schema::hasColumn('profile_experiences', 'employer_address')) {
                $table->dropColumn('employer_address');
            }
        });
    }
};
