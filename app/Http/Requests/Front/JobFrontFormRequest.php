<?php

namespace App\Http\Requests\Front;

use App\Helpers\LocationHelper;
use App\Http\Requests\Request;
use App\Models\CustomField;
use App\Services\CustomFieldValueService;

class JobFrontFormRequest extends Request
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

    protected function prepareForValidation(): void
    {
        if (! $this->has('job_action')) {
            $this->merge(['job_action' => 'submit']);
        }
    }

    public function isDraftAction(): bool
    {
        return $this->input('job_action') === 'draft';
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
                    $locationLevel = LocationHelper::getLocationLevels();

                    if ($this->isDraftAction()) {
                        return [
                            'job_action' => 'required|in:draft,submit',
                            'title' => 'nullable|string|max:180',
                            'description' => 'nullable|string',
                            'skills' => 'nullable|array',
                            'industry_id' => 'nullable',
                            'custom_industry' => 'nullable|string|max:200',
                            'custom_functional_area' => 'nullable|string|max:200',
                            'custom_city_name' => 'nullable|string|max:30',
                            'custom_job_skills_lines' => 'nullable|string|max:10000',
                            'custom_fields' => 'nullable|array',
                            'country_id' => 'nullable',
                            'state_id' => 'nullable',
                            'city_id' => 'nullable',
                            'functional_area_id' => 'nullable',
                            'job_type_id' => 'nullable',
                            'expiry_date' => 'nullable',
                            'job_experience_id' => 'nullable',
                            'apply_type' => 'nullable|in:internal,external,email,phone',
                            'job_link' => 'nullable|string|max:255',
                            'promote_urgent_days' => 'nullable|in:0,7,15',
                            'promote_featured_days' => 'nullable|in:0,15,30',
                            'promote_highlighted' => 'nullable|in:0,1',
                            'job_id' => 'nullable|string|max:255',
                            'union' => 'nullable|string|max:255',
                            'fte' => 'nullable|string|max:255',
                            'job_primary_location' => 'nullable|array',
                            'hours_per_shift' => 'nullable|string|max:255',
                            'shifts_per_cycle' => 'nullable|string|max:255',
                        ];
                    }

                    return [
                        'job_action' => 'required|in:draft,submit',
                        "title" => "required|max:180",
                        "description" => "required",
                        "skills" => "nullable|array",
                        "industry_id" => "required",
                        "custom_industry" => "nullable|string|max:200",
                        "custom_functional_area" => "nullable|string|max:200",
                        "custom_city_name" => "nullable|string|max:30",
                        "custom_job_skills_lines" => "nullable|string|max:10000",
                        "custom_fields" => "nullable|array",
                        "country_id" => "required",
                        "state_id" => in_array((int) $locationLevel, [2, 3, 4], true) ? "required" : "nullable",
                        "city_id" => "required",
                        //"is_freelance" => "required",
                        //"career_level_id" => "required",
                        //"salary_from" => "required|max:11",
                        //"salary_to" => "required|max:11",
                        //"salary_currency" => "required|max:5",
                        //"salary_period_id" => "required",
                       // "hide_salary" => "required",
                        "functional_area_id" => "required",
                        "job_type_id" => "required",
                        //"job_shift_id" => "required",
                        //"num_of_positions" => "required",
                        //"gender_id" => "required",
                        "expiry_date" => "required",
                        //"degree_level_id" => "required",
                        "job_experience_id" => "required",
                        'apply_type' => 'nullable|in:internal,external,email,phone',
                        'job_link' => 'nullable|string|max:255',
                        'promote_urgent_days' => 'nullable|in:0,7,15',
                        'promote_featured_days' => 'nullable|in:0,15,30',
                        'promote_highlighted' => 'nullable|in:0,1',
                        'job_id' => 'nullable|string|max:255',
                        'union' => 'nullable|string|max:255',
                        'fte' => 'nullable|string|max:255',
                        'job_primary_location' => 'nullable|array',
                        'hours_per_shift' => 'nullable|string|max:255',
                        'shifts_per_cycle' => 'nullable|string|max:255',
                    ];
                }
            default:break;
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            if ($this->isDraftAction()) {
                return;
            }
            if ((int) $this->input('functional_area_id') === 0) {
                if (mb_strlen(trim((string) $this->input('custom_functional_area', ''))) < 2) {
                    $v->errors()->add('custom_functional_area', __('Please enter a custom job category.'));
                }
            }
            if ((int) $this->input('industry_id') === 0) {
                if (mb_strlen(trim((string) $this->input('custom_industry', ''))) < 2) {
                    $v->errors()->add('custom_industry', __('Please enter a facility type.'));
                }
            }
            if (LocationHelper::showCity() && (int) $this->input('city_id') === 0) {
                $sid = app(\App\Services\UserSubmittedLookupService::class)->resolveStateIdForCity($this);
                if ($sid <= 0) {
                    $v->errors()->add('custom_city_name', __('Choose country and state/province before adding a custom city.'));
                } elseif (mb_strlen(trim((string) $this->input('custom_city_name', ''))) < 2) {
                    $v->errors()->add('custom_city_name', __('Please enter a custom city.'));
                }
            }
            $skills = $this->input('skills', []);
            if (! is_array($skills)) {
                $skills = [];
            }
            $applyType = $this->input('apply_type', $this->input('external_job') === 'yes' ? 'external' : 'internal');
            $applyValue = trim((string) $this->input('job_link', ''));
            if (! in_array($applyType, ['internal', 'external', 'email', 'phone'], true)) {
                $applyType = 'internal';
            }
            if ($applyType !== 'internal' && $applyValue === '') {
                $v->errors()->add('job_link', __('Please enter the application contact for this apply type.'));
            }
            if ($applyType === 'email' && $applyValue !== '') {
                $email = preg_replace('/^mailto:/i', '', $applyValue);
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $v->errors()->add('job_link', __('Please enter a valid application email address.'));
                }
            }
            if ($applyType === 'phone' && $applyValue !== '') {
                $digits = preg_replace('/\D/', '', $applyValue);
                if (strlen($digits) < 7) {
                    $v->errors()->add('job_link', __('Please enter a valid application phone number.'));
                }
            }
            $picked = array_filter($skills, function ($x) {
                return (int) $x !== 0;
            });
            $custom = trim((string) $this->input('custom_job_skills_lines', ''));
            if (count($picked) === 0 && $custom === '') {
                $v->errors()->add('skills', __('Please select at least one skill and/or enter custom skills (one per line).'));
            }
            app(CustomFieldValueService::class)->validateContext($this, CustomField::CONTEXT_JOB_LISTING, $v);
        });
    }

    public function messages()
    {
        return [
            'title.required' => __('Please enter Job title'),
            'description.required' => __('Please enter Job description'),
            'skills.required' => __('Please select or enter job skills'),
            'industry_id.required' => __('Please select Facility Type'),
            'country_id.required' => __('Please select Country'),
            'state_id.required' => __('Please select State'),
            'city_id.required' => __('Please select City'),
            //'is_freelance.required' => __('Is this freelance Job?'),
            //'career_level_id.required' => __('Please select Career level'),
           // 'salary_from.required' => __('Please select salary from'),
           // 'salary_to.required' => __('Please select salary to'),
           // 'salary_currency.required' => __('Please select salary currency'),
            //'salary_period_id.required' => __('Please select salary period'),
            //'hide_salary.required' => __('Is salary hidden?'),
            'functional_area_id.required' => __('Please select job category'),
            'job_type_id.required' => __('Please select job type'),
            //'job_shift_id.required' => __('Please select job shift'),
           // 'num_of_positions.required' => __('Please select number of positions'),
           // 'gender_id.required' => __('Please select gender'),
            'expiry_date.required' => __('Please enter Job expiry date'),
            //'degree_level_id.required' => __('Please select degree level'),
            'job_experience_id.required' => __('Please select job experience'),
        ];
    }

}
