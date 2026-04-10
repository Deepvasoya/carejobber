<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_history', function (Blueprint $table) {
            $table->decimal('package_list_price', 10, 2)->nullable()->after('package_price');
        });
    }

    public function down(): void
    {
        Schema::table('payment_history', function (Blueprint $table) {
            $table->dropColumn('package_list_price');
        });
    }
};
