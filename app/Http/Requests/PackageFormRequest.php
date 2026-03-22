<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PackageFormRequest extends Request
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
                    $unique_id = ($id > 0) ? ',' . $id : '';
                    return [
                        'package_title' => 'required',
                        'package_price' => 'required',
                        'package_num_days' => 'required',
                        'package_for' => 'required',
                        'package_type' => 'nullable|in:one_time_credits,monthly_recurring,resume_boost',
                        'duration_days' => [
                            'nullable',
                            'integer',
                            'min:1',
                            'max:3650',
                            Rule::requiredIf(function () {
                                return $this->input('package_for') === 'employer'
                                    && $this->input('package_type') === 'monthly_recurring';
                            }),
                        ],
                        'subscription_unlimited_jobs' => 'nullable|boolean',
                        'stripe_price_id' => 'nullable|string|max:255',
                        'country_code' => 'nullable|string|size:2',
                        'rebate_percent' => 'nullable|integer|min:0|max:100',
                        'is_active' => 'nullable|boolean',
                    ];
                }
            default:break;
        }
    }

    public function messages()
    {
        return [
            'package_title.required' => 'Package Title is required',
            'package_price.required' => 'Package price is required',
            'package_num_days.required' => 'Package num days required',
            'package_num_listings.required' => 'Package num listings required',
            'package_for.required' => 'Please select package for',
        ];
    }

}
