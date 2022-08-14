<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class RecordAndKeywordRequest extends BaseAPIRequest
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
            'keywordId' => 'integer',
            'recordId' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            
        ];
    }
}
