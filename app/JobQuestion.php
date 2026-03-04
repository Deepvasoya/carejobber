<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobQuestion extends Model
{
    protected $table = 'job_questions';
    public $timestamps = true;
    protected $guarded = ['id'];
    
    public function job()
    {
        return $this->belongsTo('App\Job', 'job_id', 'id');
    }
    
    public function answers()
    {
        return $this->hasMany('App\JobQuestionAnswer', 'job_question_id', 'id');
    }
}

