<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Responses\BaseResponse;
use App\Models\Org\Company;
use App\Models\Org\User;
use App\Http\Controllers\Controller;
use App\Models\Org\Employee;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Str;

class AuthController extends Controller
{
    public function signup(SignUpRequest $request)
    {
        $data = $request->validated();

        // Cek duplikasi user DULU sebelum masuk transaksi
        $existingUser = User::where('email', $data['email'])
            ->orWhere('phone_number', $data['phone_number'])
            ->first();

        if ($existingUser) {
            $existingEmployee = Employee::where('id_user', $existingUser->id)->first();

            if ($existingEmployee) {
                return BaseResponse::error(
                    message: 'User with this email or phone number already exists',
                    code: 409
                );
            }
        }

        DB::beginTransaction();

        try {
            
            $user = User::create([
                'id' => Str::uuid()->toString(),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'],
                'is_admin' => true,
                // 'id_workplace' => $company->id,
            ]);

            // MENAMBAHKAN COMPANY SETELAH ADA USER
            $company = Company::create([
                'name' => $data['company_name'],
                'address' => $data['company_address'],
                'id_manager' => $user->id, // Akan diisi nanti setelah user dibuat
            ]);
            
            // DIUPDATE AGAR ADA ID_WORKPLACE
            $user->update([
                'id_workplace' => $company->id
            ]);

            // Generate sign_in_code otomatis
            $signInCode = $this->generateUniqueSignInCode();

            Employee::create([
                'id_user' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'address' => $data['address'] ?? 'Not Provided', //AKU KOSONGI KARENA WAJIB DIISI TERNYATA
                'sign_in_code' => $signInCode,
            ]);


            DB::commit();

            return BaseResponse::success(
                message: 'User created successfully',
                code: 201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return BaseResponse::error(
                message: 'Failed to create user: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    private function generateUniqueSignInCode()
{
    do {
        $code = 'MN' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $exists = Employee::where('sign_in_code', $code)->exists();
    } while ($exists);

    return $code;
}


    public function signin(SignInRequest $request)
    {
        $data = $request->validated();

        // dd($data); 

        $user = null;

        if (!empty($data['email'])) {
            $user = User::where('email', $data['email'])->first();
        } elseif (!empty($data['phone_number'])) {
            $user = User::where('phone_number', $data['phone_number'])->first();
        } else if (!empty($data['sign_in_code']) && !empty($data['company_name'])) {
            $user = User::whereHas('employee', function ($query) use ($data) {
                    $query->where('sign_in_code', $data['sign_in_code']);
                })->whereHas('workplace', function ($query) use ($data) {
                    $query->where('name', $data['company_name']);
                })
                ->with(['employee','workplace'])
                ->first();
       
        }

        if (!$user || !password_verify($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->workplace) {
            return response()->json([
                'message' => 'User is not associated with a company'
            ], 403);
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

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function redirectToGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            $isNewUser = false;

            DB::beginTransaction();

            if (!$user) {
                $user = User::create([
                    'id' => Str::uuid(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'phone_number' => $googleUser->getId(),
                    'is_admin' => '0',
                    'id_workplace' => null,
                ]);

                // Buat sign_in_code otomatis
            $signInCode = $this->generateUniqueSignInCode();

            Employee::create([
                'id_user' => $user->id,
                'first_name' => $googleUser->getName(), // atau split ke first/last
                'last_name' => '',
                'address' => 'Not Provided',
                'sign_in_code' => $signInCode,
            ]);

                $isNewUser = true;

            //     // Buat sign_in_code otomatis
            // $signInCode = $this->generateUniqueSignInCode();

            } else {
                $employee = Employee::where('id_user', $user->id)->first();

                if (!$employee) {
                    $isNewUser = true;
                }
            }

            //tambahan
            DB::commit();

            Auth::login($user);

            $token = $user->createToken('auth_token')->plainTextToken;

            return BaseResponse::redirect(config('app.frontend_url') . '/auth/google/callback?' . http_build_query([
                'token' => $token,
                'is_new_user' => $isNewUser ? 'true' : 'false',
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
            ]));
        } catch (\Exception $e) {
            return BaseResponse::error(
                data: $e->getMessage(),
                message: 'Failed to authenticate with Google',
                code: 500
            );
        } catch (\Throwable $th) {
            return BaseResponse::error(
                message: 'An unexpected error occurred',
                code: 500
            );
        }
    }
}
