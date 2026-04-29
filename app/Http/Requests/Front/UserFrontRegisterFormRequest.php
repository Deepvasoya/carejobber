<?php

namespace App\Http\Requests\Front;

use Auth;
use App\Http\Requests\Request;

class UserFrontRegisterFormRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'first_name' => 'required|max:80',
            'last_name' => 'required|max:80',
            'phone' => 'required|max:20',
            'date_of_birth' => 'nullable|date',
            'gender_id' => 'nullable|integer',
            'street_address' => 'nullable|string|max:255',
            'job_title' => 'nullable|array',
            'job_title.*' => 'string|max:100',
            'functional_area_id' => 'nullable|integer',
            'career_level_id' => 'nullable|integer',
            'email' => 'required|unique:users,email|email|max:100',
            'password' => 'required|confirmed|min:6|max:50',
            'terms_of_use' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('First Name is required'),
            'last_name.required' => __('Last Name is required'),
            'phone.required' => __('Phone Number is required'),
            'email.required' => __('Email is required'),
            'email.email' => __('The email must be a valid email address'),
            'email.unique' => __('This Email has already been taken'),
            'password.required' => __('Password is required'),
            'password.min' => __('The password should be more than 5 characters long'),
            'terms_of_use.required' => __('Please accept terms of use'),
        ];
    }

}
