<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class FolderRequest extends BaseAPIRequest
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
            'name' => 'required|max:128',
            'color' => 'required|max:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required!',
            'color.required' => 'Color is required!',
            'color.max' => 'Color must be 6 character!',
        ];
    }
}
