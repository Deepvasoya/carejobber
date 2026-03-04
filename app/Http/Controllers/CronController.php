<?php

namespace App\Http\Controllers;

use App\Traits\Cron;
use App\Http\Controllers\Controller;

class CronController extends Controller
{



    public function checkPackageValidity()
    {
        $this->runCheckPackageValidity();
    }

}
