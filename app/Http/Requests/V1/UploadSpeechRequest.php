<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class UploadSpeechRequest extends BaseAPIRequest
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
            'file' => 'required|mimes:m4a,mp3,wav',
        ];
    }

    public function messages()
    {
        return [
            'file.mimes' => 'The uploaded file is not in the correct format',
        ];
    }
}
