<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class RecordItemRequest extends BaseAPIRequest
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
            'record' => 'nullable|integer',
            'begin' => 'required',
            'end' => 'required',
            'content' =>'nullable'
        ];
    }

    public function messages()
    {
        return [
            'begin.required' => 'Begin is required!',
            'end.required' => 'End is required!',
        ];
    }
}
