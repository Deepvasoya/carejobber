<?php

namespace App\Http\Requests\Front;

use App\Http\Requests\Request;

class ApplyJobFormRequest extends Request
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
                        "cv_id" => "nullable",
                        "cover_letter" => "nullable|string|max:2000",
                        "resume_source" => "nullable|string|in:existing_cv,uploaded",
                    ];
                }
            default:break;
        }
    }

    public function messages()
    {
        return [
            'cv_id.required' => __('Please select CV'),
            'cover_letter.max' => __('Cover letter must not exceed 2000 characters'),
        ];
    }

}
