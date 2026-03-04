<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = ['role_name', 'role_abbreviation', 'role_description'];

    public function users()
    {
        return $this->hasMany('App\User', 'role_id', 'id');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id', 'id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }
}
