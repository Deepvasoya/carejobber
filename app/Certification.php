<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $table = 'certifications';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_certifications', 'certification_id', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getLangNameAttribute()
    {
        return $this->name;
    }
}
