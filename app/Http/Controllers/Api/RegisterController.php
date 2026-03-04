<?php

   

namespace App\Http\Controllers\Api;

   

use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController as BaseController;

use App\User;
use App\Company;

use Illuminate\Support\Facades\Auth;

use Validator;

use Illuminate\Foundation\Auth\RegistersUsers;



use Jrean\UserVerification\Traits\VerifiesUsers;

use Jrean\UserVerification\Facades\UserVerification;

use Illuminate\Auth\Events\Registered;

use App\Events\UserRegistered;

use App\Events\CompanyRegistered;

use Illuminate\Support\Str;
use Mail; 

class RegisterController extends BaseController

{



    use RegistersUsers;

    use VerifiesUsers;

    /**

     * Register api

     *

     * @return \Illuminate\Http\Response

     */

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:80',
            'middle_name' => 'max:80',
            'last_name' => 'required|max:80',
            'email' => 'required|unique:users,email|email|max:100',
            'password' => 'required|confirmed|min:6|max:50',
            'terms_of_use' => 'required',
            //'g-recaptcha-response' => 'required|captcha',
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

       $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = new User();

        //Image Upload with base64 format
        if ($request->has('profile_photo')) {

            $images =  $request->profile_photo;
            foreach($images  as $baseString){
            $image_parts = explode(";base64,", $baseString);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $profile_photo_name = "jobeify_user_" . uniqid() . '.'.$image_type;            
                    
          //  Storage::put($profile_photo_name,  $image_base64);
            $file = public_path('/user_profile_photos/') . $profile_photo_name;
            file_put_contents($file, $image_base64);
            $user->image = $profile_photo_name ;
            }
        }
        //End Image Uploading Code
        $token = Str::random(64);
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->is_active = 0;
        $user->verified = 0;
       // $user->remember_token = $token ;
        $user->save();

        /*         * *********************** */

        $user->name = $user->getName();

       $success = $user->update();


    //    Mail::send('emails.emailVerificationEmail', ['token' => $token], function($message) use($request){
    //     $message->to($request->email);
    //     $message->subject('Email Verification Mail');
    // });

        /*         * *********************** */

     
        event(new Registered($user));
        event(new UserRegistered($user));
        $this->guard()->login($user);
        UserVerification::generate($user);
      //  UserVerification::send($user, 'User Verification', config('mail.recieve_to.address'), config('mail.recieve_to.name'));
        UserVerification::send($user, 'User Verification', $user->email, $user->name);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

   

    /**

     * Login api

     *

     * @return \Illuminate\Http\Response

     */

    public function login(Request $request)

    {

        //dd($request);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 

            $user = Auth::user(); 

            $success['token'] =  $user->createToken('MyApp')->accessToken; 

            $success['name'] =  $user->name;

   

            return $this->sendResponse($success, 'User login successfully.');

        } 

        else{ 

            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);

        } 

    }



    public function employerRegister(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required|max:150',

            'email' => 'required|unique:companies,email|email|max:100',

            'password' => 'required|confirmed|min:6|max:50',

            'terms_of_use' => 'required',

           // 'g-recaptcha-response' => 'required|captcha',

        ]);

   

        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

   

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $company = new Company();

        $company->name = $request->input('name');

        $company->email = $request->input('email');

        $company->password = bcrypt($request->input('password'));

        $company->is_active = 0;

        $company->verified = 0;

        $company->save();

        /*         * ******************** */

        $company->slug = Str::slug($company->name, '-') . '-' . $company->id;

        $company->update();

        /*         * ******************** */



        event(new Registered($company));

        event(new CompanyRegistered($company));

        $this->guard('company')->login($company);

        UserVerification::generate($company);

        UserVerification::send($company, 'Company Verification', config('mail.recieve_to.address'), config('mail.recieve_to.name'));

        $success['token'] =  $company->createToken('MyApp')->accessToken;

        $success['name'] =  $company->name;

   

        return $this->sendResponse($success, 'Company register successfully.');

    }





    public function employerLogin(Request $request)

    {

        //dd($request);

        if(Auth::guard('company')->attempt(['email' => $request->email, 'password' => $request->password])){ 

            $user = Auth::guard('company')->user(); 

            $success['token'] =  $user->createToken('MyApp')->accessToken; 

            $success['name'] =  $user->name;

   

            return $this->sendResponse($success, 'Company login successfully.');

        } 

        else{ 

            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);

        } 

    }

}