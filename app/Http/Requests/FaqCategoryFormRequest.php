<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class FaqCategoryFormRequest extends Request
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
            'lang' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter category name.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'lang.required' => 'Please select a language.'
        ];
    }
}
