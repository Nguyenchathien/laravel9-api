<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
        event(new PasswordReset($user));
    }

    protected function sendResetResponse(Request $request, $response)
    {
        $response = ['message' => "Password reset successful"];
        return response($response, 200);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        $response = "Token Invalid";
        return response($response, 401);
    }
}
