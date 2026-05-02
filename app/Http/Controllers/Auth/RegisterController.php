<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Jrean\UserVerification\Traits\VerifiesUsers;
use Jrean\UserVerification\Facades\UserVerification;
use App\Http\Requests\Front\UserFrontRegisterFormRequest;
use Illuminate\Auth\Events\Registered;
use App\Events\UserRegistered;

class RegisterController extends Controller
{
    /*
      |--------------------------------------------------------------------------
      | Register Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users as well as their
      | validation and creation. By default this controller uses a trait to
      | provide this functionality without requiring any additional code.
      |
     */

use RegistersUsers;
    use VerifiesUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['getVerification', 'getVerificationError', 'notVerified']]);
    }

    public function showRegistrationForm()
    {
        $genders = \App\Helpers\DataArrayHelper::langGendersArray();
        $jobCategories = \App\Helpers\DataArrayHelper::langJobCategoriesArray();
        $careerLevels = \App\Helpers\DataArrayHelper::langCareerLevelsArray();
        $nationalities = \App\Helpers\DataArrayHelper::langNationalitiesArray();
        return view('auth.register', compact('genders', 'jobCategories', 'careerLevels', 'nationalities'));
    }

    public function register(UserFrontRegisterFormRequest $request)
    {
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->phone = $request->input('phone');
        
        // Step 1 optional fields
        if ($request->has('date_of_birth')) {
            $user->date_of_birth = $request->input('date_of_birth');
        }
        if ($request->has('gender_id')) {
            $user->gender_id = $request->input('gender_id');
        }
        if ($request->has('street_address')) {
            $user->street_address = $request->input('street_address');
        }

        // Step 2 fields
        if ($request->has('job_title') && is_array($request->input('job_title'))) {
            $user->job_title = implode(', ', $request->input('job_title'));
        }
        if ($request->has('job_category_id')) {
            $user->job_category_id = $request->input('job_category_id');
        }
        if ($request->has('career_level_id')) {
            $user->career_level_id = $request->input('career_level_id');
        }
        // Handle nationality - store first selected or primary nationality
        if ($request->has('nationality_id') && is_array($request->input('nationality_id')) && count($request->input('nationality_id')) > 0) {
            $user->nationality_id = $request->input('nationality_id')[0]; // Store first selected nationality
        }

        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->is_active = 1;
        $user->verified = 0;
        $user->save();
        /*         * *********************** */
        $user->name = $user->getName();
        $user->update();
        /*         * *********************** */
        event(new Registered($user));
        event(new UserRegistered($user));
        $this->guard()->login($user);
        UserVerification::generate($user);
        
        // Send verification email using our custom template
        $user->sendEmailVerificationNotification();
        
        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }
    
    public function notVerified()
    {
        return view('vendor.laravel-user-verification.resend-user-verification');
    }
    
    

}
