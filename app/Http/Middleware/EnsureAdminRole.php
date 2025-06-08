<?php

namespace App\Http\Middleware;

use App\Http\Responses\BaseResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

// app imports
use App\Models\Org\User;

class EnsureAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var User $currentUser
         */
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin()) {
            return BaseResponse::error(
                message: 'You do not have permission to access this resource',
                code: 403
            );
        }

        return $next($request);
    }

}