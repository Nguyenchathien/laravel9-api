<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class KeywordsRequest extends BaseAPIRequest
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
            'name'  => 'required|max:128',
            'color' => 'max:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required!',
            'color.max'    => 'Remark must be at least 6 characters!',
        ];
    }
}
