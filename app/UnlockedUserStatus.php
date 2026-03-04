<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnlockedUserStatus extends Model
{
    protected $table = 'unlocked_user_status';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function getCompany($field = '')
    {
        if (null !== $company = $this->company()->first()) {
            return !empty($field) ? $company->$field : $company;
        }
    }

    public function getUser($field = '')
    {
        if (null !== $user = $this->user()->first()) {
            return !empty($field) ? $user->$field : $user;
        }
    }
}

