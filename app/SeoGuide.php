<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeoGuide extends Model
{
    protected $table = 'seo_guides';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
