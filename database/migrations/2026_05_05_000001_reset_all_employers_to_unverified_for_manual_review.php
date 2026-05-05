<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Reset ALL employers to 'unverified' status for manual admin review
        // Admin will manually set to 'reviewed' or 'verified' after checking each employer
        DB::table('companies')
            ->update(['employer_trust_status' => 'unverified']);
    }

    public function down(): void
    {
        // Cannot reliably revert - would need to restore previous status values
    }
};
