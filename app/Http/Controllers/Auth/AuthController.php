<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Responses\BaseResponse;
use App\Models\Org\Company;
use App\Models\Org\User;
use App\Http\Controllers\Controller;
use App\Models\Org\Employee;

class AuthController extends Controller
{
    public function signup(SignUpRequest $request)
    {
        $user = new User();
        $user->email = request()->email;
        $user->phone_number = request()->phone_number;
        $user->password = bcrypt(request()->password);
        $user->save();

        $company = new Company();
        $company->name = request()->company_name;
        $company->address = request()->company_address;
        $company->id_manager = $user->id;
        $company->save();

        $employee = new Employee();
        $employee->id_user = $user->id;
        $employee->first_name = request()->first_name;
        $employee->last_name = request()->last_name;
        $employee->address = request()->address;
        $employee->save();

        $user = User::where('id', $user->id)->first();

        return BaseResponse::success(
            data: $user,
            message: 'User created successfully',
            code: 201
        );
    }

    public function signin(SignInRequest $request)
    {
        $user = User::where('email', request()->email)->orWhere('phone_number', request()->phone_number)->first();

        if (!$user || !password_verify(request()->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('access_token')->plainTextToken;

        return BaseResponse::success(
            data: [
                'user' => $user,
                'token' => $token
            ],
            message: 'User signed in successfully',
            code: 200
        );
    }

    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            return BaseResponse::error(
                message: 'User not found',
                code: 404
            );
        }

        return BaseResponse::success(
            data: $user,
            message: 'User retrieved successfully',
            code: 200
        );
    }
}
