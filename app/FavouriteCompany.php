<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class FavouriteCompany extends Model
{

    protected $table = 'favourites_company';
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $dateFormat = 'U';
    protected $dates = ['created_at', 'updated_at'];

    
    public function company()
{
    return $this->belongsTo(Company::class, 'company_id', 'id');
}


}
