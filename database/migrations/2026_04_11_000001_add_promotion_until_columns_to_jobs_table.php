<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->timestamp('promotion_urgent_until')->nullable()->after('display_end_date');
            $table->timestamp('promotion_featured_until')->nullable()->after('promotion_urgent_until');
            $table->timestamp('promotion_highlighted_until')->nullable()->after('promotion_featured_until');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'promotion_urgent_until',
                'promotion_featured_until',
                'promotion_highlighted_until',
            ]);
        });
    }
};
