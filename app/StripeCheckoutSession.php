<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeCheckoutSession extends Model
{
    protected $table = 'stripe_checkout_sessions';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $fillable = ['session_id', 'company_id', 'package_id', 'country_code', 'status'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
