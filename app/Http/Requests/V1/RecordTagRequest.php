<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class RecordTagRequest extends BaseAPIRequest
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
            'time'  => 'max:128',
            'record_item'  => 'max:24',
            'tag' => 'max:128',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required!',
            'name.min'      => 'Name must be at least 3 characters!',
            'remark.min'    => 'Remark must be at least 6 characters!',
            'remark.max'    => 'Remark must be at least 1024 characters!',
        ];
    }
}
