<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('verification_status', 20)->nullable()->after('verified_at');
            $table->text('verification_rejection_reason')->nullable()->after('verification_status');
            $table->timestamp('verification_reviewed_at')->nullable()->after('verification_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'verification_rejection_reason',
                'verification_reviewed_at',
            ]);
        });
    }
};
