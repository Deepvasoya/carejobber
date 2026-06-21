<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchedulerLog extends Model
{
    protected $table = 'scheduler_logs';

    protected $fillable = [
        'command',
        'status',
        'output',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
