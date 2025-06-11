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
use App\Models\Payment\Invoice;
use App\Enums\InvoiceStatus;
use Xendit\Xendit;

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
        $user = $request->user();
        if (!$user){
            return response()->json(['messege' => 'user not authenticated'],401);
        }
        
        $company = $user->workplace;
        if (!$company){
            return response()->json(['messege' => 'User has no workplace'],422);    
        }

        $existingSubscription = Subscription::where('id_company', $company->id)
        ->where(function ($query){
            $query->where('status','trial')
                ->orWhere(function ($q) {
                    $q->where('status','active')->where('ends_at','>',now());
                });
        })
        ->first();

        if($existingSubscription){
            return response()->json([
                'message'=>'Company sudah ada subscription aktif atau trial berjalan.',
            ], 422);
        }

        $hasActiveSub = Subscription::where('id_company', $company->id)
            ->where('is_trial',false)
            ->where('status','active')
            ->exists();

        // If company has used trial, create non-trial subscription
        if ($company->has_used_trial) {
            $subscription = Subscription::create([
                'id_company' => $company->id,
                'package_type' => $request->package_type,
                'seats' => $request->seats,
                'price_per_seat' => $this->getPricePerSeat($request->package_type),
                'is_trial' => false,
                'trial_ends_at' => null,
                'starts_at' => now(),
                'ends_at' => now()->day(28)->endOfDay(),
                'status' => 'active'
            ]);

            $company->id_subscription = $subscription->id;
            $company->save();

            return BaseResponse::success([
                'message' => 'Subscription started.',
                'data' => $subscription
            ]);
        }

        // Create trial subscription for new companies
        $trialEndDate = now()->addDays(14);
        $subscription = Subscription::create([
            'id_company' => $company->id,
            'package_type' => $request->package_type,
            'seats' => $request->seats,
            'price_per_seat' => 0,
            'is_trial' => true,
            'trial_ends_at' => $trialEndDate,
            'starts_at' => now(),
            'ends_at' => $trialEndDate,
            'status' => 'trial'
        ]);

        $company->id_subscription = $subscription->id;
        $company->has_used_trial = true;
        $company->save();

        return BaseResponse::success([
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

        return BaseResponse::success([
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

    public function cancel(Request $request, string $id)
    {
        $subscription = Subscription::findOrFail($id);
    
        // Hanya bisa dibatalkan jika masih aktif
        if ($subscription->status !== 'active') {
            return BaseResponse::error('Langganan tidak aktif', 400);
        }

        $subscription->update([
            'ends_at' => now(),
            'status' => 'expired',
            'is_cancelled' => true,
        ]);

        return BaseResponse::success('Langganan berhasil dibatalkan');
    }

    public function upgrade(Request $request, string $id)
    {
        try {
            // Validate request
            $request->validate([
                'package_type' => 'required|string|in:standard,premium',
                'seats' => 'required|integer|min:1'
            ]);

            $subscription = Subscription::findOrFail($id);
            $user = $request->user();
            
            if (!$user) {
                return BaseResponse::error('User not authenticated', 401);
            }

            if (!$user->workplace) {
                return BaseResponse::error('User has no workplace', 422);
            }

            if ($user->workplace->id !== $subscription->id_company) {
                return BaseResponse::error('Unauthorized: Subscription does not belong to your company', 403);
            }

            // Check if subscription is active
            if ($subscription->status !== 'active' || $subscription->is_cancelled) {
                return BaseResponse::error('Cannot upgrade: Subscription is not active', 422);
            }

            // Validate that the new package is an upgrade
            $currentPrice = $this->getPricePerSeat($subscription->package_type);
            $newPrice = $this->getPricePerSeat($request->package_type);
            
            if ($newPrice <= $currentPrice) {
                return BaseResponse::error('New package must be an upgrade from current package', 422);
            }

            // Validate seats limit based on package
            $maxSeats = $request->package_type === 'standard' ? 100 : 1000;
            if ($request->seats > $maxSeats) {
                return BaseResponse::error("Maximum seats for {$request->package_type} package is {$maxSeats}", 422);
            }

            // Calculate prorated amount
            $remainingDays = now()->diffInDays(Carbon::parse($subscription->ends_at));
            $totalDays = Carbon::parse($subscription->starts_at)->diffInDays($subscription->ends_at);
            
            \Log::info('Subscription upgrade calculation details', [
                'subscription_id' => $subscription->id,
                'ends_at' => $subscription->ends_at,
                'starts_at' => $subscription->starts_at,
                'remaining_days' => $remainingDays,
                'total_days' => $totalDays,
                'current_time' => now()->toDateTimeString()
            ]);
            
            if ($totalDays <= 0) {
                return BaseResponse::error('Invalid subscription period', 422);
            }

            $proratedAmount = (($newPrice - $currentPrice) * $subscription->seats) * ($remainingDays / $totalDays);

            
            // Update subscription with new package info
            $subscription->update([
                'package_type' => $request->package_type,
                'price_per_seat' => $newPrice,
                'seats' => $request->seats ?? $subscription->seats,
                'status' => 'pending_upgrade'
            ]);

            // Create new invoice for the upgrade
            $invoice = Invoice::create([
                'id_user' => $user->id,
                'total_amount' => $proratedAmount,
                'due_datetime' => now()->addDays(7),
                'status' => InvoiceStatus::UNPAID,
                'description' => "Upgrade subscription from {$subscription->package_type} to {$request->package_type}"
            ]);
            // Create Xendit invoice
            try {
                Xendit::setApiKey(config('services.xendit.secret'));
                $xenditInvoice = \Xendit\Invoice::create([
                    'external_id' => $invoice->id,
                    'payer_email' => $user->email,
                    'description' => "Upgrade subscription from {$subscription->package_type} to {$request->package_type}",
                    'amount' => $proratedAmount,
                    'status' => 'unpaid'
                ]);

                $invoice->update([
                    'xendit_invoice_id' => $xenditInvoice['id'],
                    'invoice_url' => $xenditInvoice['invoice_url'],
                ]);

                return BaseResponse::success([
                    'message' => 'Upgrade request created successfully',
                    'data' => [
                        'subscription' => $subscription,
                        'invoice' => $invoice
                    ]
                ]);

            } catch (\Exception $e) {
                \Log::error('Failed to create Xendit invoice for upgrade', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $invoice->id
                ]);
                
                // Revert subscription changes
                $subscription->update([
                    'package_type' => $subscription->package_type,
                    'price_per_seat' => $currentPrice,
                    'status' => 'active'
                ]);
                
                $invoice->delete();
                
                return BaseResponse::error('Failed to process upgrade payment: ' . $e->getMessage(), 500);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Subscription not found', [
                'error' => $e->getMessage(),
                'subscription_id' => $id
            ]);
            return BaseResponse::error('Subscription not found', 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in upgrade request', [
                'errors' => $e->errors(),
                'subscription_id' => $id
            ]);
            return BaseResponse::error('Validation error: ' . json_encode($e->errors()), 422);
        } catch (\Exception $e) {
            \Log::error('Failed to process upgrade request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'subscription_id' => $id
            ]);
            
            return BaseResponse::error('Failed to process upgrade request: ' . $e->getMessage(), 500);
        }
    }
}
