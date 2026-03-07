<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds advanced location settings: toggle, number of fields, and custom labels
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('location_multiple_fields')->default(true)->after('location_levels')->comment('Enable/disable multiple location fields');
            $table->string('location_field_1_label', 50)->nullable()->after('location_multiple_fields')->comment('Label for first location field (e.g., country)');
            $table->string('location_field_2_label', 50)->nullable()->after('location_field_1_label')->comment('Label for second location field (e.g., state)');
            $table->string('location_field_3_label', 50)->nullable()->after('location_field_2_label')->comment('Label for third location field (e.g., city)');
            $table->string('location_field_4_label', 50)->nullable()->after('location_field_3_label')->comment('Label for fourth location field (e.g., district)');
        });

        // Set default values
        DB::table('site_settings')->update([
            'location_multiple_fields' => true,
            'location_field_1_label' => 'country',
            'location_field_2_label' => 'state',
            'location_field_3_label' => 'city',
            'location_field_4_label' => 'district',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'location_multiple_fields',
                'location_field_1_label',
                'location_field_2_label',
                'location_field_3_label',
                'location_field_4_label',
            ]);
        });
    }
};
