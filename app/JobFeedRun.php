<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobFeedRun extends Model
{
    protected $table = 'job_feed_runs';
    protected $guarded = ['id'];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(JobFeedSource::class, 'job_feed_source_id');
    }
}
