<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class ScheduleDateRequest extends BaseAPIRequest
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
            'date' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'DateTime is required!',
        ];
    }
}
