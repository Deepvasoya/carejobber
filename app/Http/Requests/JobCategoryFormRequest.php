<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class JobCategoryFormRequest extends Request
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
                    $job_category_unique = '';
                    if ($id > 0) {
                        $job_category_unique = ',id,' . $id;
                    }
                    return [
                        'job_category' => 'required|unique:job_categories' . $job_category_unique,
                        'job_category_id' => 'required_if:is_default,0',
                        'is_active' => 'required',
                        'is_default' => 'required',
                        'lang' => 'required',
                    ];
                }
            default:break;
        }
    }

    public function messages()
    {
        return [
            'job_category.required' => 'Please enter Job Category.',
            'job_category_id.required_if' => 'Please select default/fallback Job Category.',
            'is_default.required' => 'Is this Job Category default?',
            'is_active.required' => 'Please select status.',
            'lang.required' => 'Please select language.',
        ];
    }
}