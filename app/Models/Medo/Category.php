<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'medo_categories';

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function jobs()
    {
        return $this->hasMany(\App\Job::class, 'medo_category_id');
    }

    public function settingsFor(Province $province)
    {
        return CategoryProvinceSetting::where('category_id', $this->id)
            ->where('province_id', $province->id)
            ->first();
    }

    public function related()
    {
        // For pSEO pages, returns related categories. Currently stubbed to return all others.
        return Category::where('id', '!=', $this->id)->get();
    }
}
