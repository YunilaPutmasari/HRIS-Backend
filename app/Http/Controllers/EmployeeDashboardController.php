<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->is_admin == 0) {
            return response()->json([
                'message' => 'Selamat datang di dashboard employee',
                'user' => $request->user()
            ]);
        }

        return response()->json([
            'message' => 'Hanya employee yang boleh akses route ini.'
        ], 403);
    }
}
