<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\BaseResponse;
use Illuminate\Support\Facades\Auth;

class EnsureFeatureAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = Auth::user();
        $company = $user->workplace;

        if (!$company || !$company->subscription) {
            return BaseResponse::error('Subscription not found.', 403);
        }

        $allowedFeatures = match($company->subscription->package_type) {
            'free' => ['attendance', 'leave_request'],
            'standard' => ['attendance', 'leave_request', 'overtime_rules', 'payroll_auto'],
            'premium' => ['attendance', 'leave_request', 'overtime_rules', 'payroll_auto', 'custom_schedule', 'custom_reports'],
        };

        if (!in_array($feature, $allowedFeatures)) {
            return BaseResponse::error("Fitur '{$feature}' tidak tersedia di paket Anda.", 403);
        }

        return $next($request);
    }
}
