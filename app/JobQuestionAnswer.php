<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobQuestionAnswer extends Model
{
    protected $table = 'job_question_answers';
    public $timestamps = true;
    protected $guarded = ['id'];
    
    public function jobQuestion()
    {
        return $this->belongsTo('App\JobQuestion', 'job_question_id', 'id');
    }
    
    public function jobApply()
    {
        return $this->belongsTo('App\JobApply', 'job_apply_id', 'id');
    }
}

