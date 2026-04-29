<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('functional_areas') && ! Schema::hasColumn('functional_areas', 'slug')) {
            Schema::table('functional_areas', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('functional_area');
                $table->index('slug', 'functional_areas_slug_index');
            });

            foreach (DB::table('functional_areas')->orderBy('id')->get() as $row) {
                DB::table('functional_areas')
                    ->where('id', $row->id)
                    ->update(['slug' => $this->uniqueSlug('functional_areas', $this->categorySlug($row->functional_area), $row->id)]);
            }
        }

        if (Schema::hasTable('cities') && ! Schema::hasColumn('cities', 'slug')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('city');
                $table->index('slug', 'cities_slug_index');
            });

            foreach (DB::table('cities')->orderBy('id')->get() as $row) {
                DB::table('cities')
                    ->where('id', $row->id)
                    ->update(['slug' => $this->uniqueSlug('cities', Str::slug($row->city), $row->id)]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('functional_areas') && Schema::hasColumn('functional_areas', 'slug')) {
            Schema::table('functional_areas', function (Blueprint $table) {
                $table->dropIndex('functional_areas_slug_index');
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasTable('cities') && Schema::hasColumn('cities', 'slug')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropIndex('cities_slug_index');
                $table->dropColumn('slug');
            });
        }
    }

    private function categorySlug(?string $name): string
    {
        $name = trim((string) $name);
        $lower = strtolower($name);

        if (preg_match('/\b(hca|health\s*care\s*(aide|assistant))\b/i', $lower)) {
            return 'hca';
        }
        if (preg_match('/\b(lpn|licensed\s+practical\s+nurse)\b/i', $lower)) {
            return 'lpn';
        }
        if (preg_match('/\b(rn|registered\s+nurse)\b/i', $lower)) {
            return 'rn';
        }

        return Str::slug($name) ?: 'category';
    }

    private function uniqueSlug(string $table, string $base, int $id): string
    {
        $slug = $base ?: 'page';
        $candidate = $slug;
        $i = 2;

        while (DB::table($table)->where('slug', $candidate)->where('id', '!=', $id)->exists()) {
            $candidate = $slug . '-' . $i++;
        }

        return $candidate;
    }
};
