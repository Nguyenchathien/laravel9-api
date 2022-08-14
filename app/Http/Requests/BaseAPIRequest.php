<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseAPIRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        foreach($this->validationData() as $key => $val){
            $this->merge([
                $key => $this->checkSecurity($val),
            ]);
        }
    }    

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'errors'   => $errors,
            'status_code'      => JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ]));
    }

    /**
     * Handle fields not include in rules
     * @return errors
    */
    protected function passedValidation(){
        // List all fields is allow in rules
        $rules = $this->rules();
        $allowFields = [];
        foreach($this->rules() as $key => $rule){
            if(! in_array($key, $allowFields)){
                array_push($allowFields, $key);
            }
        }

        // List all fileds in request
        $deniedFields = [];
        foreach($this->validationData() as $key => $rule){
            if(! in_array($key, $allowFields)){
                array_push($deniedFields, ucfirst($key)." is not allow in request!");
            }
        }

        // Response to client
        if(count($deniedFields)){
            throw new HttpResponseException(
                response()->json(['errors' => $deniedFields], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }

    private function checkSecurity($input){
        $output = strip_tags($input);
        return $output;
    }
}
