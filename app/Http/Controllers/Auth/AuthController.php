<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
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

        DB::beginTransaction();

        try {
            // 1. Cek user berdasarkan email
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                // 2. Cek apakah employee sudah dibuat
                $existingEmployee = Employee::where('id_user', $user->id)->first();

                if ($existingEmployee) {
                    DB::rollBack();
                    return BaseResponse::error(
                        message: 'User with this email already exists and is fully registered.',
                        code: 409
                    );
                }

                // 3. User sudah ada tapi belum lengkap (dari Google)
                $user->update([
                    'phone_number' => $data['phone_number'], // update dari google_id ke nomor asli
                    'password' => Hash::make($data['password']), // set password manual (boleh disimpan)
                ]);
            } else {
                // 4. Jika belum ada, cek duplikasi phone_number
                $dupePhone = User::where('phone_number', $data['phone_number'])->first();

                if ($dupePhone) {
                    DB::rollBack();
                    return BaseResponse::error(
                        message: 'Phone number already used by another user.',
                        code: 409
                    );
                }

                // 5. Buat user baru
                $user = User::create([
                    'id' => Str::uuid(),
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'phone_number' => $data['phone_number'],
                    'is_admin' => true,
                ]);
            }

            // 6. Buat company jika belum ada tempat kerja
            if (!$user->id_workplace) {
                $company = Company::create([
                    'name' => $data['company_name'],
                    'address' => $data['company_address'],
                    'id_manager' => $user->id,
                ]);

                $user->update([
                    'id_workplace' => $company->id,
                ]);
            } else {
                $company = Company::find($user->id_workplace);
            }

            // 7. Buat employee
            $signInCode = $this->generateUniqueSignInCode();

            Employee::create([
                'id_user' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'address' => $data['address'] ?? 'Not Provided',
                'sign_in_code' => $signInCode,
            ]);

            DB::commit();

            return BaseResponse::success(
                message: 'Signup completed successfully',
                code: 201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::error(
                message: 'Failed to signup: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    private function generateUniqueSignInCode()
    {
        do {
            $code = 'ky' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
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
                ->with(['employee', 'workplace'])
                ->first();

        }

        if (!$user || !password_verify($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->workplace()) {
            return response()->json([
                'message' => 'User is not associated with a company'
            ], 403);
        }

        // $company = $user->workplace;
        // $hasSubscription = $company->subscription()->exists();
        // $freePlan = PackageType::where('is_free',true)->first();

        // if (!$hasSubscription) {
        //     $newSub = Subscription::create([
        //         'id_company' => $company->id,
        //         'id_package_type' => $freePlan->id,
        //         'seats' => $freePlan->max_seats,
        //         'starts_at' => now(),
        //         'ends_at' => now()->addMinutes(10),
        //         'status' => 'active',
        //     ]);

        //     $company->update(['id_subscription' => $newSub->id]);
        // }

        $token = $user->createToken('access_token')->plainTextToken;

        // ======================
    // ⬇️ Tambahkan role ke response user
    // ======================
    $userData = $user->toArray();
    $userData['role'] = $user->is_admin ? 'admin' : 'employee';
    
    return BaseResponse::success(
        data: [
            'user' => $userData,
            'token' => $token
        ],
        message: 'User signed in successfully',
        code: 200
    );
    

    }

    public function me(Request $request)
    {
        $user = auth()->user()->load('employee', 'workplace.subscription');

        if (!$user) {
            return BaseResponse::error(
                message: 'User not found',
                code: 404
            );
        }

        $user->load(['workplace', 'employee']);

        return BaseResponse::success(
            data: $user,
            message: 'User retrieved successfully',
            code: 200
        );
    }

    //autth id_karyawan
    public function redirectToGoogle(Request $request)
{
    $loginMethod = $request->query('login_method');

    // Simpan login_method di state agar dikirim ke Google dan kembali saat callback
    return Socialite::driver('google')
        ->stateless() // Hati-hati dengan stateless, kalau session penting bisa dihapus
        ->with(['state' => $loginMethod])
        ->redirect();
}


    public function redirectToGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            DB::beginTransaction();

            $user = User::where('email', $googleUser->getEmail())->first();
            $isNewUser = false;

            if (!$user) {
                // 1. USER BELUM ADA → REGISTER BARU
                $user = User::create([
                    'id' => Str::uuid(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // default password acak
                    'phone_number' => $googleUser->getId(), // sementara pakai Google ID
                    'is_admin' => true,
                    'id_workplace' => null,
                ]);

                $isNewUser = true; // harus isi data tambahan via frontend

            } else {
                // 2. USER SUDAH ADA
                $employee = Employee::where('id_user', $user->id)->first();

                if (!$employee) {
                    // Kalau belum ada employee → anggap ini user Google yang perlu isi data
                    $isNewUser = true;
                }
            }

            DB::commit();

            // 3. Login user
            Auth::login($user);

            $token = $user->createToken('auth_token')->plainTextToken;
            //auth id_karyawan
            $loginMethod = request()->get('state'); // Ambil dari parameter callback dari Google

            $role = $user->is_admin ? 'admin' : 'employee';

            // 4. Redirect ke FE
            return BaseResponse::redirect(config('app.frontend_url') . '/auth/google/callback?' . http_build_query([
                'token' => $token,
                'is_new_user' => $isNewUser ? 'true' : 'false',
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'login_method' => $loginMethod,  // Gunakan hasil dari `request()->get('state')`
                'role' => $role,
            ]));
        } catch (\Exception $e) {
            DB::rollBack();

            return BaseResponse::error(
                data: $e->getMessage(),
                message: 'Failed to authenticate with Google',
                code: 500
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return BaseResponse::error(
                message: 'An unexpected error occurred',
                code: 500
            );
        }
    }
}