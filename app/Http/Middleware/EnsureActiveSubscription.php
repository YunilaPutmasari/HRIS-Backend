<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\BaseResponse;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $company = $user->workplace; //menjadi relasi user -> company

        if (!$company || !$company->subscription) {
            return BaseResponse::error('Company subscription not found.', 403);
        }

        if (!$company->subscription->isActive()) {
            return BaseResponse::error('Langganan company tidak aktif', 403);
        }

        $subscription = $company->subscription;

        if (!in_array($subscription->status, ['active', 'trial'])) {
            return BaseResponse::error('Subscription is not active or trial.', 403);
        }

        if ($company->employees()->count() > $subscription->seat_count) {
            return BaseResponse::error('Exceeded seat count limit.', 403);
        }

        return $next($request);
    }
}

