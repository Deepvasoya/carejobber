<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class VerificationDocumentRequest extends Request
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
            'business_registration' => 'required|file|mimes:png,jpg,jpeg,pdf|max:2048',
            'tax_document' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
            'establishment_photo' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'business_registration.required' => 'Business registration document is required',
            'business_registration.file' => 'Business registration must be a valid file',
            'business_registration.mimes' => 'Business registration must be png, jpg, jpeg, or pdf',
            'business_registration.max' => 'Business registration must not exceed 2MB',
            'tax_document.file' => 'Tax document must be a valid file',
            'tax_document.mimes' => 'Tax document must be png, jpg, jpeg, or pdf',
            'tax_document.max' => 'Tax document must not exceed 2MB',
            'establishment_photo.file' => 'Establishment photo must be a valid file',
            'establishment_photo.mimes' => 'Establishment photo must be png, jpg, jpeg, or pdf',
            'establishment_photo.max' => 'Establishment photo must not exceed 2MB',
        ];
    }
}
