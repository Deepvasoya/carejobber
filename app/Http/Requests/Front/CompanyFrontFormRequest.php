<?php

namespace App\Http\Requests\Front;

use Auth;
use App\Http\Requests\Request;
use App\Models\CustomField;
use App\Services\CustomFieldValueService;

class CompanyFrontFormRequest extends Request
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
                    $id = (int) Auth::guard('company')->user()->id;
                    $unique_id = ($id > 0) ? ',' . $id : '';
                    return [
                        "id" => "",
                        "name" => "required|max:150",
                        'email' => 'required|unique:companies,email' . $unique_id . '|email|max:100',
                        "ceo" => "required|max:60",
                        "industry_id" => "required",
                        "ownership_type_id" => "required",
                        "description" => "required",
                        //"location" => "required|max:150",
                        //"map" => "required",
                        "no_of_offices" => "required|max:11",
                        "website" => "required|url|max:150",
                        "no_of_employees" => "required|max:15",
                        "established_in" => "required|max:12",
                        //"fax" => "required|max:30",
                        "phone" => "required|max:30",
                        "logo" => 'mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                        "country_id" => "required",
                        "state_id" => "required",
                        "city_id" => "required",
                        "contact_name" => "required",
                        "contact_email" => "required",
                        'custom_fields' => 'nullable|array',
                    ];
                }
            default:break;
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            app(CustomFieldValueService::class)->validateContext($this, CustomField::CONTEXT_COMPANY_PROFILE, $v);
        });
    }

    public function messages()
    {
        return [
            'name.required' => __('Name is required'),
            'email.required' => __('Email is required'),
            'email.email' => __('The email must be a valid email address'),
            'email.unique' => __('This Email has already been taken'),
            'password.required' => __('Password is required'),
            'ceo.required' => __('CEO name is required'),
            'industry_id.required' => __('Please select Industry'),
            'ownership_type_id.required' => __('Please select Ownership Type'),
            'description.required' => __('Description required'),
            //'location.required' => __('Location required'),
            //'map.required' => __('Google Map required'),
            'no_of_offices.required' => __('Number of offices required'),
            'website.required' => __('Website required'),
            'website.url' => __('Complete url of website required'),
            'no_of_employees.required' => __('Number of employees required'),
            'established_in.required' => __('Established in year required'),
            //'fax.required' => __('Fax number required'),
            'phone.required' => __('Phone number required'),
            'logo.image' => __('Only Images can be used as logo'),
            'logo.mimes' => __('Logo must be a file of type: jpeg, png, jpg, gif, svg, webp'),
            'logo.max' => __('Logo file size must not exceed 2MB'),
            'country_id.required' => __('Please select country'),
            'state_id.required' => __('Please select state'),
            'city_id.required' => __('Please select city'),            
            'contact_name.required' => __('Contact Name is required'),            
            'contact_email.required' => __('Contact Email is required '),
        ];
    }

}
