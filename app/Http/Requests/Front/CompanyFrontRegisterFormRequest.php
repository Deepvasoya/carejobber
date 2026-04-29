<?php

namespace App\Http\Requests\Front;

use Auth;
use App\Http\Requests\Request;

class CompanyFrontRegisterFormRequest extends Request
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
            'name' => 'required|max:150',
            'phone' => 'required|max:30',
            'ownership_type_id' => 'required|integer',
            'contact_name' => 'required|max:150',
            'contact_phone' => 'required|max:30',
            'email' => 'required|unique:companies,email|email|max:100',
            'password' => 'required|confirmed|min:6|max:50',
            'terms_of_use' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('Company Name is required'),
            'phone.required' => __('Company Number is required'),
            'ownership_type_id.required' => __('Company Type is required'),
            'contact_name.required' => __('Contact Person Name is required'),
            'contact_phone.required' => __('Contact Person Number is required'),
            'email.required' => __('Email is required'),
            'email.email' => __('The email must be a valid email address'),
            'email.unique' => __('This Email has already been taken'),
            'password.required' => __('Password is required'),
            'password.min' => __('The password should be more than 5 characters long'),
            'terms_of_use.required' => __('Please accept terms of use'),
        ];
    }

}
