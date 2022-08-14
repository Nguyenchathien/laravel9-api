<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class AccountRequest extends BaseAPIRequest
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
            'type' => 'max:24',
            'email' => 'email|max:255',
            'name' => 'max:128',
            'gender' => 'max:24', 
            'temail' => 'email|max:255',
            'remark' => 'max:1024',
        ];
    }

    public function messages()
    {
        return [
            'remark.max' => 'Remark must be at least 1024 characters!',
        ];
    }
}
