<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('employer_trust_status', 20)
                  ->default('unverified')
                  ->after('verification_reviewed_at')
                  ->comment('unverified | reviewed | verified');
        });

        // Migrate existing verified companies → 'verified'
        DB::table('companies')
            ->where('verified', 1)
            ->update(['employer_trust_status' => 'verified']);
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('employer_trust_status');
        });
    }
};
