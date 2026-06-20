<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CompanyFormRequest extends Request
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
        switch ($this->method()) {
            case 'PUT':
            case 'POST': {
                    $id = (int) $this->input('id', 0);
                    $unique_id = ($id > 0) ? ',' . $id : '';
                    $password = ($id > 0) ? "" : "required";
                    $logo = ($id > 0) ? "" : "required";
                    
                    // Check if being created by admin without full user account
                    $createdByAdmin = $this->input('created_by_admin', 0);
                    
                    // Email and password are optional for admin-created profiles
                    $emailRule = $createdByAdmin ? "nullable|email" : "required|email";
                    $passwordRule = $createdByAdmin ? "nullable" : $password;
                    
                    // Logo is optional for admin-created profiles
                    $logoRule = $createdByAdmin ? 'nullable' : ($logo ?: 'nullable');
                    $logoRule .= '|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
                    
                    // Website is optional without URL validation for admin-created profiles
                    $websiteRule = $createdByAdmin ? 'nullable' : 'nullable|url';
                    
                    return [
                        "id" => "",
                        "name" => "required",
                        "email" => $emailRule,
                        "password" => $passwordRule,
                        "industry_id" => "nullable",
                        "ownership_type_id" => "nullable",
                        "description" => "nullable",
                        "location" => "nullable",
                        "no_of_offices" => "nullable",
                        "website" => $websiteRule,
                        "no_of_employees" => "nullable",
                        "established_in" => "nullable",
                        "logo" => $logoRule,
                        "country_id" => "nullable",
                        "state_id" => "nullable",
                        "city_id" => "nullable",
                        "is_active" => "required",
                        "is_featured" => "required",
                        "created_by_admin" => "nullable|boolean",
                    ];
                }
            default:break;
        }
    }

    public function messages()
    {
        return [
            'name.required' => 'Company Name is required',
            'email.required' => 'Company Email is required',
            'password.required' => 'Password is required',
            //'ceo.required' => 'Company\'s CEO name is required',
            'industry_id.required' => 'Please select Industry',
            'ownership_type_id.required' => 'Please select Ownership Type',
            'description.required' => 'Company Details required',
            'location.required' => 'Company location required',
            'map.required' => 'Company Google Map location required',
            'no_of_offices.required' => 'Number of offices required',
            'website.required' => 'Company website required',
            'website.url' => 'Complete url of company website required',
            'no_of_employees.required' => 'Number of employees required',
            'established_in.required' => 'Company established in year required',
            //'fax.required' => 'Fax number required',
            //'phone.required' => 'Phone number required',
            'logo.required' => 'Company logo is required',
            'logo.mimes' => 'Logo must be a file of type: jpeg, png, jpg, gif, svg, webp',
            'logo.max' => 'Logo file size must not exceed 2MB',
            'country_id.required' => 'Please select country',
            'state_id.required' => 'Please select state',
            'city_id.required' => 'Please select city',
            'is_active.required' => 'Is this Company Acive?',
            'is_featured.required' => 'Is this Company featured?',
        ];
    }

}
