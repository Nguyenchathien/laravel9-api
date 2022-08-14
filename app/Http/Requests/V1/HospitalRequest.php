<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class HospitalRequest extends BaseAPIRequest
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
            'name' => 'max:128',
            'type' => 'max:24',
            'user' => 'max:128',
            'post' => 'max:8',
            'pref' => 'max:24',
            'pref_code' => 'max:24',
            'address' => 'max:1024',
            'xaddress' => 'max:1024',
            'remark' => 'max:1024',
            'phone' => 'max:128',
            'mail' => 'email|max:255',
        ];
    }

    public function messages()
    {
        return [
            'address.max' => 'Address must be at least 1024 characters!',
            'remark.max' => 'Remark must be at least 1024 characters!',
        ];
    }
}
