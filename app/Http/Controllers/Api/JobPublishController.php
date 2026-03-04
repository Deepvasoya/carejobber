<?php

namespace App\Http\Controllers\Api;

use Auth;
use DB;
use Input;
use Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Traits\JobApiTrait;

class JobPublishController extends BaseController
{

    use JobApiTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('company');
    }

}
