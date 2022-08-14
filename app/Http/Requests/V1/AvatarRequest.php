<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class AvatarRequest extends BaseAPIRequest
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
            'file' =>'required|mimes:jpg,png,jpeg,gif,svg|max:10000'
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Required',
            'file.mimes' => 'wrong file extension',
        ];
    }
}
