<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jika user tidak ada, atau user adalah manager, atau bukan employee (is_admin bukan 0)
        if (
            !$user ||
            !$user->workplace ||
            $user->workplace->id_manager === $user->id || // User adalah manager, tolak akses employee
            $user->is_admin !== 0 // is_admin harus 0 (employee)
        ) {
            return response()->json(['message' => 'Unauthorized - Only employee allowed'], 403);
        }

        return $next($request);
    }
}
