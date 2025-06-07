<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return BaseResponse::success(
                message: 'Reset link sent to your email.',
                code: 200,
            );
        } else {
            return BaseResponse::error(
                message: 'Failed to send reset link.',
                code: 400,
            );
        }
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return BaseResponse::success(
                message: __('passwords.reset_success'),
                code: 201
            );
        } else if ($status === Password::INVALID_TOKEN) {
            return BaseResponse::error(
                message: __('passwords.token_invalid'),
                data: [
                    'is_token_invalid' => true,
                ],
                code: 400
            );
        } else {
            return BaseResponse::error(
                message: __('passwords.reset_failed'),
                code: 400
            );
        }
    }
}
