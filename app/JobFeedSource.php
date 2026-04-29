<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobFeedSource extends Model
{
    protected $table = 'job_feed_sources';
    protected $guarded = ['id'];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function runs()
    {
        return $this->hasMany(JobFeedRun::class, 'job_feed_source_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
