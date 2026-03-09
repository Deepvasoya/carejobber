<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProfileSummaryFormRequest extends Request
{

    /**
     * Prepare the data for validation (normalize Summary -> summary for case-insensitive form handling).
     */
    protected function prepareForValidation()
    {
        if ($this->has('Summary') && !$this->has('summary')) {
            $this->merge(['summary' => $this->input('Summary')]);
        }
    }

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
                    return [
                        "summary" => "required",
                    ];
                }
            default:break;
        }
    }

    public function messages()
    {
        return [
            'summary.required' => 'Please enter Profile Summary.',
        ];
    }

}
