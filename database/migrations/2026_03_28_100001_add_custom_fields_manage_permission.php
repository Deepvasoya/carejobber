<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->insertOrIgnore([
            'name' => 'Manage Custom Fields',
            'slug' => 'custom_fields.manage',
            'module' => 'Custom Fields',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $newId = DB::table('permissions')->where('slug', 'custom_fields.manage')->value('id');
        $jobAttrId = DB::table('permissions')->where('slug', 'job_attributes.manage')->value('id');

        if ($newId && $jobAttrId) {
            $roleIds = DB::table('role_permission')
                ->where('permission_id', $jobAttrId)
                ->pluck('role_id');

            foreach ($roleIds as $roleId) {
                DB::table('role_permission')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $newId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $id = DB::table('permissions')->where('slug', 'custom_fields.manage')->value('id');
        if ($id) {
            DB::table('role_permission')->where('permission_id', $id)->delete();
            DB::table('permissions')->where('id', $id)->delete();
        }
    }
};
