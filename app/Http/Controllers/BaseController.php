<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponseGetToken($data, $result, $message)
    {
    	$response = [
            'success' => true,
            'access_token' => $result,
            'token_type' => 'Bearer',
            'data' => $data,
            'status_code' => 200
        ];

        return response()->json($response, 200);
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result = [], $message)
    {
    	$response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
            'status_code' => 200
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $errorMessages,
            'status_code' => $code,
        ];

        return response()->json($response, $code);
    }
}