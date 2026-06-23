<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class JobFormRequest extends Request
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
                    $job_unique = '';
                    if ($id > 0) {
                        $job_unique = ',id,' . $id;
                    }
                    return [
                        "id" => "",
                        "title" => "required",
                        "company_id" => "nullable",
                        "description" => "nullable",
                        "skills" => "nullable",
                        "country_id" => "nullable",
                        "state_id" => "nullable",
                        "city_id" => "nullable",
                        "functional_area_id" => "nullable",
                        "job_type_id" => "nullable",
                        "expiry_date" => "nullable",
                        "job_experience_id" => "nullable",
                        'apply_type' => 'nullable|in:internal,external,email,phone',
                        'job_link' => 'nullable|string|max:255',
                        "is_active" => "required",
                        "is_featured" => "required",
                        "is_urgent" => "required",
                        "is_highlighted" => "required",
                        "job_id" => "nullable|string|max:255",
                        "union" => "nullable|string|max:255",
                        "fte" => "nullable|string|max:255",
                        "job_primary_location" => "nullable|string",
                        "hours_per_shift" => "nullable|string|max:255",
                        "shifts_per_cycle" => "nullable|string|max:255",
                    ];
                }
            default:break;
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $applyType = $this->input('apply_type', $this->input('external_job') === 'yes' ? 'external' : 'internal');
            $applyValue = trim((string) $this->input('job_link', ''));

            if (! in_array($applyType, ['internal', 'external', 'email', 'phone'], true)) {
                $applyType = 'internal';
            }

            if ($applyType !== 'internal' && $applyValue === '') {
                $v->errors()->add('job_link', 'Please enter the application contact for this apply type.');
            }

            if ($applyType === 'email' && $applyValue !== '') {
                $email = preg_replace('/^mailto:/i', '', $applyValue);
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $v->errors()->add('job_link', 'Please enter a valid application email address.');
                }
            }

            if ($applyType === 'phone' && $applyValue !== '') {
                $digits = preg_replace('/\D/', '', $applyValue);
                if (strlen($digits) < 7) {
                    $v->errors()->add('job_link', 'Please enter a valid application phone number.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'title.required' => 'Please enter Job title.',
            'is_active.required' => 'Is this Job active?',
            'is_featured.required' => 'Is this Job featured?',
            'is_urgent.required' => 'Is this Job urgent?',
            'is_highlighted.required' => 'Is this Job highlighted?',
        ];
    }

}
