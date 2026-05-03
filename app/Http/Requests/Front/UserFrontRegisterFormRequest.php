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
            'phone' => 'required|numeric|digits_between:7,20',
            'date_of_birth' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'gender_id' => 'nullable|integer',
            'street_address' => 'nullable|string|max:255',
            'job_title' => 'nullable|array',
            'job_title.*' => 'string|max:100',
            'job_category_id' => 'required|integer',
            'nationality_id' => 'required|array|min:1',
            'nationality_id.*' => 'integer|exists:nationalities,nationality_id',
            'years_of_experience' => 'required|integer|min:0|max:50',
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
            'phone.numeric' => __('Phone Number must contain only numbers'),
            'phone.digits_between' => __('Phone Number must be between 7 and 20 digits'),
            'date_of_birth.required' => __('Date of Birth is required'),
            'date_of_birth.before_or_equal' => __('You must be at least 18 years old to register'),
            'job_category_id.required' => __('Please select your job category'),
            'nationality_id.required' => __('Please select at least one nationality'),
            'nationality_id.min' => __('Please select at least one nationality'),
            'years_of_experience.required' => __('Please enter your years of experience'),
            'years_of_experience.integer' => __('Years of experience must be a number'),
            'years_of_experience.min' => __('Years of experience cannot be negative'),
            'years_of_experience.max' => __('Years of experience cannot exceed 50 years'),
            'email.required' => __('Email is required'),
            'email.email' => __('The email must be a valid email address'),
            'email.unique' => __('This Email has already been taken'),
            'password.required' => __('Password is required'),
            'password.min' => __('The password should be more than 5 characters long'),
            'terms_of_use.required' => __('Please accept terms of use'),
        ];
    }

}
