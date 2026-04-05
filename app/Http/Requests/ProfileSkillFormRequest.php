<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProfileSkillFormRequest extends Request
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
                    return [
                        'job_skill_id' => 'required',
                        'custom_job_skill_name' => 'nullable|string|max:200',
                    ];
                }
            default:break;
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            if ((int) $this->input('job_skill_id') === 0) {
                if (mb_strlen(trim((string) $this->input('custom_job_skill_name', ''))) < 2) {
                    $v->errors()->add('custom_job_skill_name', __('Please enter the skill name.'));
                }
            }
        });
    }

    public function messages()
    {
        return [
            'job_skill_id.required' => 'Please select skill.',
            'job_experience_id.required' => 'Please select year of experience chosen above.',
        ];
    }

}
