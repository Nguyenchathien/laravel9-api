<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class ShareRequest extends BaseAPIRequest
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
            'record' => 'required',
            'user' => 'max:128',
            'to' => 'max:128',
            'mail' => 'email|max:255',
        ];
    }

    public function messages()
    {
        return [
            'record.required' => 'Record is required!',
        ];
    }
}
