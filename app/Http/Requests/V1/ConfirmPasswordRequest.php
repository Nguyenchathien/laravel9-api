<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class ConfirmPasswordRequest extends BaseAPIRequest
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
            'user_id' => 'required',
            'is_confirm' => 'required',
            'password' => 'required|min:6|max:1024',
            'password_confirmation' => 'required|same:password'
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Email is required!',
            'password_confirmation.required' => 'Password confirm is required!'
        ];
    }
}
