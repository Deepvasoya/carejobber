<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set all NULL employer_trust_status to 'unverified' as the default
        DB::table('companies')
            ->whereNull('employer_trust_status')
            ->update(['employer_trust_status' => 'unverified']);
    }

    public function down(): void
    {
        // No need to revert - keeping 'unverified' is safe
    }
};
