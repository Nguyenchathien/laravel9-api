<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class ScheduleUpdateRequest extends BaseAPIRequest
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
            'id' => 'integer',
            'title' => 'max:128',
            'date' => 'nullable',
            'hospital' => 'nullable',
            'people' => 'nullable',
            'remark' => 'max:1024',
        ];
    }

    public function messages()
    {
        return [
            
        ];
    }
}
