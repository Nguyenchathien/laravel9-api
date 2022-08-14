<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class ListRecordItemRequest extends BaseAPIRequest
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
            'record' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'record.required' => 'record id is required!',
        ];
    }
}
