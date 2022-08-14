<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class ScheduleRequest extends BaseAPIRequest
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
            'title' => 'required|max:128',
            'date' => 'required',
            'hospital' => 'nullable',
            'people' => 'nullable',
            'remark' => 'max:1024',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required!',
            'date.required' => 'DateTime is required!',
//            'people.required' => 'Doctor is required!',
            'remark.max' => 'Note must be at least 1024 characters!',
        ];
    }
}
