<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseAPIRequest;

class UserSignupRequest extends BaseAPIRequest
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
            'type' => 'required|min:3|max:24',
            'email' => 'required|email|max:255',
            'name' => 'required|min:3|max:128',
            'password' => 'required|min:6|max:1024',
            'password_confirmation' => 'required|same:password',
            'plan' => 'required|min:3|max:24',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required!',
            'name.min' => 'Name must be at least 3 characters!',
            'email.required' => 'Emmail is required!',
            'password.required' => 'Password is required!',
            'password.min' => 'Password must be at least 6 characters!',
        ];
    }
}
