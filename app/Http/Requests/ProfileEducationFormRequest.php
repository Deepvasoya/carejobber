<?php

namespace App\Http\Requests;

use App\Helpers\LocationHelper;
use App\Http\Requests\Request;

class ProfileEducationFormRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch ($this->method()) {
            case 'PUT':
            case 'POST':
                $rules = [
                    'degree_level_id' => 'nullable',
                    'degree_title' => 'required',
                    'institution' => 'required',
                    'date_completion' => 'required',
                    'major_subjects' => 'nullable',
                    'degree_result' => 'nullable',
                    'result_type_id' => 'nullable',
                    'school_location' => 'nullable|string|max:500',
                    'description' => 'nullable|string|max:5000',
                ];

                $rules['country_id'] = LocationHelper::showCountry() ? 'required' : 'nullable';

                return $rules;
            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            'degree_title.required' => 'Please enter your degree or program.',
            'major_subjects.required' => 'Please select major subjects.',
            'country_id.required' => 'Please select country.',
            'institution.required' => 'Please enter institution.',
            'date_completion.required' => 'Please set completion date.',
            'degree_result.required' => 'Please enter result.',
            'result_type_id.required' => 'Please select result type.',
        ];
    }
}
