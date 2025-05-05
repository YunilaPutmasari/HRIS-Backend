<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function signup(SignUpRequest $request)
    {
        $user = new \App\Models\User();
        $user->email = request()->email;
        $user->phone_number = request()->phone_number;
        $user->password = bcrypt(request()->password);
        $user->save();

        $employee = new \App\Models\Employee();
        $employee->id_user = $user->id;
        $employee->first_name = request()->first_name;
        $employee->last_name = request()->last_name;
        $employee->address = request()->address;
        $employee->save();

        $user = User::where('id', $user->id)->first();

        $response = new \App\Http\Resources\UserResource($user);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
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

        return response()->json([
            'message' => 'User signed in successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }

    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user
        ], 200);
    }
}
