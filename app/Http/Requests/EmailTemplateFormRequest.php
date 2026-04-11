<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EmailTemplateFormRequest extends Request
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
        return [
            'name' => 'required|max:255',
            'subject' => 'required|max:500',
            'body' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter template name.',
            'name.max' => 'Template name cannot exceed 255 characters.',
            'subject.required' => 'Please enter email subject.',
            'subject.max' => 'Email subject cannot exceed 500 characters.',
            'body.required' => 'Please enter email body.'
        ];
    }
}
