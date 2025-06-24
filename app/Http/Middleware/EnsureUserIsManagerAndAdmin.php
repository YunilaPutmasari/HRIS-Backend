<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsManagerAndAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            !$user || 
            !$user->workplace || 
            $user->workplace->id_manager !== $user->id || 
            $user->is_admin !== 1
        ) {
            return response()->json(['message' => 'Unauthorized - Only manager with admin access allowed'], 403);
        }

        return $next($request);
    }
}
