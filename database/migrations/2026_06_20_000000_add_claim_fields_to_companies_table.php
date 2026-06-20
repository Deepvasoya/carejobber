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
        Schema::table('companies', function (Blueprint $table) {
            $table->tinyInteger('is_claimed')->default(0)->after('is_featured');
            $table->tinyInteger('created_by_admin')->default(0)->after('is_claimed');
            $table->unsignedInteger('claimed_by_user_id')->nullable()->after('created_by_admin');
            $table->timestamp('claimed_at')->nullable()->after('claimed_by_user_id');
            
            // Add index for claimed_by_user_id
            $table->index('claimed_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['claimed_by_user_id']);
            $table->dropColumn(['is_claimed', 'created_by_admin', 'claimed_by_user_id', 'claimed_at']);
        });
    }
};
