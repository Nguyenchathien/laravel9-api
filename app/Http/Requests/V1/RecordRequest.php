<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class RecordRequest extends BaseAPIRequest
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
            'title' => 'required',
            'hospital' => 'integer|nullable',
            'people' => 'nullable',
            'folder' => 'nullable',
            'begin' => 'required',
            'end' => 'required',
            'recordItems' => [],
            'keywords' => [],
            'audios' => [],
            'total_time' => 'required|integer|min:1|max:36000'
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
