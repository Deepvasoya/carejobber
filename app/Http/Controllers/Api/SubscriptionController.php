<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Validator;
use DB;
use Input;
use Redirect;
use App\User;
use App\Subscription;
use App\Alert;
use Newsletter;
use App\Company;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriptionController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    { }

    public function submitAlert(Request $request)
    {
        try {
            $rules = array(
                'email' => 'required|email|max:100',
            );
            
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return $this->sendError('Validation Error', $validation->errors(), 422);
            }
            
            $subscription = new Alert();
            $subscription->email = $request->get('email');
            $subscription->country_id = $request->get('country_id');
            $subscription->search_title = $request->get('search');
            $subscription->name = $request->get('name');
            $subscription->state_id = $request->get('state_id');
            $subscription->city_id = $request->get('city_id');
            $subscription->save();
            
            return $this->sendResponse($subscription, 'Alert submitted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error submitting alert', [], 500);
        }
    }
}
