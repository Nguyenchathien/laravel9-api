<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class DoctorUpdateRequest extends BaseAPIRequest
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
            'name' => 'max:128',
            'type' => 'max:24',
            'org' => 'integer',
            'dept' => 'max:128',
            'user' => 'max:128',
            'post' => 'max:8',
            'pref' => 'max:24',
            'pref_code' => 'max:24',
            'address' => 'max:1024',
            'xaddress' => 'max:1024',
            'remark' => 'max:1024',
            'phone' => 'min:10|max:128',
            'mail' => 'email|max:255',
        ];
    }

    public function messages()
    {
        return [
            'address.max' => 'Address must be at least 1024 characters!',
            'note.max' => 'Note must be at least 1024 characters!',
        ];
    }
}
