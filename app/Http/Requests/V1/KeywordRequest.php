<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class KeywordRequest extends BaseAPIRequest
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
            'image' => 'image|mimes:jpg,jpeg,png|max:10000',
            'name'  => 'max:128',
            'type'  => 'max:24',
            'color' => 'max:128',
            'user'  => 'max:128',
            'vx01'  => 'max:128',
            'vx02'  => 'max:128',
            'remark' => 'max:1024'
        ];
    }

    public function messages()
    {
        return [
            'remark.max'    => 'Remark must be at least 1024 characters!',
        ];
    }
}
