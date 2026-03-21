<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('jobs', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false)->after('is_featured');
            }
            if (! Schema::hasColumn('jobs', 'is_highlighted')) {
                $table->boolean('is_highlighted')->default(false)->after('is_urgent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (Schema::hasColumn('jobs', 'is_highlighted')) {
                $table->dropColumn('is_highlighted');
            }
            if (Schema::hasColumn('jobs', 'is_urgent')) {
                $table->dropColumn('is_urgent');
            }
        });
    }
};
