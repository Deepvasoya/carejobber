<?php

namespace App\Http\Requests;

use App\Helpers\LocationHelper;
use App\Http\Requests\Request;

class ProfileExperienceFormRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    protected function isFrontExperienceRoute(): bool
    {
        $name = $this->route()?->getName();

        return in_array($name, ['store.front.profile.experience', 'update.front.profile.experience'], true);
    }

    public function rules()
    {
        switch ($this->method()) {
            case 'PUT':
            case 'POST':
                $rules = [
                    'title' => 'required|string|max:100',
                    'company' => 'required|string|max:120',
                    'date_start' => 'required|date',
                    'date_end' => 'required_if:is_currently_working,0|nullable|date',
                    'is_currently_working' => 'required|in:0,1',
                    'description' => 'nullable|string|max:65535',
                ];

                if ($this->isFrontExperienceRoute()) {
                    $rules['employer_address'] = 'nullable|string|max:500';
                    $rules['country_id'] = 'nullable';
                    $rules['state_id'] = 'nullable';
                    $rules['city_id'] = 'nullable';
                } else {
                    // Admin form — location fields are optional (dropdowns load via AJAX)
                    $rules['employer_address'] = 'nullable|string|max:500';
                    $rules['country_id'] = 'nullable';
                    $rules['state_id'] = 'nullable';
                    $rules['city_id'] = 'nullable';
                }

                return $rules;
            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            'title.required' => 'Please enter title.',
            'company.required' => 'Please enter company.',
            'description.required' => 'Please enter description.',
            'country_id.required' => 'Please select country.',
            'state_id.required' => 'Please select state.',
            'city_id.required' => 'Please select city.',
            'date_start.required' => 'Please set start date.',
            'date_end.required_if' => 'Please set end date.',
            'is_currently_working.required' => 'Are you currently working here?',
        ];
    }
}
