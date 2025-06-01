<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription\Subscription;
use App\Http\Responses\BaseResponse;
use App\Models\Org\Company;
use App\Models\Org\User;
use Carbon\Carbon;

class SubscriptionController extends Controller
{   
    public function index(Request $request){
        $user = $request->user();
        $companyId = $user->workplace->id;
        
        $subscription = Subscription::with('company')
        ->where('id_company', $companyId)
        ->latest()
        ->get();
        return BaseResponse::success($subscription);
    }

    public function store(StoreSubscriptionRequest $request)
    {
        $company = Company::findOrFail($request->id_company);

        $subscription = Subscription::create([
            'id_company' => $company->id,
            'package_type' => $request->package_type,
            'seats' => $request->seats,
            'price_per_seat' => $this->getPricePerSeat($request->package_type),
            'is_trial' => true,
            'trial_ends_at' => now()->addDays(14),
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 'trial'
        ]);

        $company->id_subscription = $subscription->id;
        $company->save();

        return response()->json([
            'message' => 'Trial subscription started.',
            'data' => $subscription
        ]);
    }

    public function update(UpdateSubscriptionRequest $request, string $id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->update([
            'package_type' => $request->package_type ?? $subscription->package_type,
            'seats' => $request->seats ?? $subscription->seats,
            'price_per_seat' => $this->getPricePerSeat($request->package_type ?? $subscription->package_type),
            'status' => 'active',
            'is_trial' => false,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'trial_ends_at' => null,
        ]);

        return response()->json([
            'message' => 'Subscription updated.',
            'data' => $subscription
        ]);
    }

    private function getPricePerSeat(string $package): float
    {
        return match ($package) {
            'standard' => 10000,
            'premium' => 25000,
            default => 0
        };
    }
}
