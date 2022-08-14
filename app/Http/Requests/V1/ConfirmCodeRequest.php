<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConfirmCodeRequest extends BaseAPIRequest
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
            'code' => 'required|max:6'
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Code is required!'
        ];
    }
}
