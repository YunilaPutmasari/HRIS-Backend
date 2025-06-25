<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Responses\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription\Subscription;
use App\Models\Subscription\DailyUsageRecord;
use App\Models\Subscription\PackageType;
use App\Models\Payment\Invoice;
use App\Models\Payment\Payment;
use App\Models\Org\Company;
use App\Models\Org\User;
use App\Enums\InvoiceStatus;
use Carbon\Carbon;
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

// AMBIL SUBS AKTIF
    public function getActiveSubscription()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return BaseResponse::error(null, 'Akses ditolak', 403);
        }

        $company = $user->workplace;

        $subscription = $company->latestSubscription()->with('packageType')->first();

        if (!$subscription || !in_array($subscription->status, ['active', 'pending_upgrade', 'pending_downgrade'])) {
            return BaseResponse::error(null, 'Tidak ada langganan aktif', 404);
        }

        return BaseResponse::success([
            'id' => $subscription->id,
            'package_type' => $subscription->packageType,
            'seats' => $subscription->seats,
            'starts_at' => $subscription->starts_at,
            'ends_at' => $subscription->ends_at,
            'status' => $subscription->status,
        ], 200);
    }

// AMBIL SUBS YANG SEDANG ADA DI COMPANY
    public function getCurrentSubscription()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin())
        {
            return BaseResponse::error(null, 'Akses ditolak', 403);
        }

        $company = $user->workplace;
        if(!$company){
            return BaseResponse::error(null, 'User tidak memiliki workplace', 422);
        }
        
        // Ambil subscription terbaru berdasarkan waktu mulai
        $currentSubscription = Subscription::where('id_company', $company->id)
            ->orderByDesc('starts_at')
            ->with('packageType')
            ->first();

        if (!$currentSubscription) {
            return BaseResponse::error(null, 'Tidak ada langganan ditemukan', 404);
        }

        return BaseResponse::success([
            'id' => $currentSubscription->id,
            'package_type' => $currentSubscription->packageType,
            'seats' => $currentSubscription->seats,
            'starts_at' => $currentSubscription->starts_at,
            'ends_at' => $currentSubscription->ends_at,
            'status' => $currentSubscription->status,
            'is_canceled' => $currentSubscription->is_canceled ?? false,
        ]);
    }
// AMBIL SEMUA HISTORY SUBSCRIPTION COMPANY TERSEBUT
    public function getAllSubscription()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return BaseResponse::error(null, 'Akses ditolak', 403);
        }

        $company = $user->workplace;
        if (!$company) {
            return BaseResponse::error(null, 'User tidak memiliki workplace', 422);
        }

        $subscriptions = Subscription::where('id_company', $company->id)
        ->orderByDesc('starts_at')
        ->with('packageType')
        ->get();

    if ($subscriptions->isEmpty()) {
        return BaseResponse::success([
            'data' => []
        ], 'Belum ada riwayat langganan');
    }

    // Format response untuk FE
    $formatted = $subscriptions->map(function ($sub) {
        return [
            'id' => $sub->id,
            'package_type' => $sub->packageType ? [
                'id' => $sub->packageType->id,
                'name' => $sub->packageType->name,
                'price_per_seat' => $sub->packageType->price_per_seat,
            ] : null,
            'seats' => $sub->seats,
            'starts_at' => $sub->starts_at,
            'ends_at' => $sub->ends_at,
            'status' => $sub->status,
        ];
    });

    return BaseResponse::success([
        'data' => $formatted
    ], 200);

    }

// NEW CARA STORE SUBSCRIPTION
    public function subscribe(Request $request)
    {   
        $validated = $request->validate([
            'id_package_type'=>'required|exists:tb_package_types,id',
            'seats'=>'required|integer|min:1',
        ]);

        $user = Auth::user();
        if(!$user){
            return BaseResponse::error(null, 'User not authenticated', 401);
        }

        $company = $user->workplace;
        if (!$company) {
            return BaseResponse::error(null, 'User has no workplace', 422);
        }

        if ($user->id !== $company->id_manager || !$user->isAdmin()) {
            return BaseResponse::error(null, 'Anda tidak memiliki akses', 403);
        }

        $packageType = PackageType::findOrFail($validated['id_package_type']);

        // Validasi jumlah seat
        if ($validated['seats'] > $packageType->max_seats) {
            return BaseResponse::error(null, 'Jumlah seat melebihi batas paket', 400);
        }

        // Cek apakah sudah ada subscription aktif/trial
        $existingSub = Subscription::where('id_company', $company->id)
            ->whereIn('status', ['trial', 'active'])
            ->first();

        if ($existingSub) {
            return BaseResponse::error(null, 'Sudah ada subscription aktif atau trial berjalan.', 400);
        }

        // Cek apakah perusahaan pernah trial sebelumnya
        $hasUsedTrial = $company->has_used_trial ?? false;
        
        // Buat subscription (fase develop buat 1 menit dan 5 menit)
        $trialEndDate = now()->addMinutes(1); //ganti ke addDays(14) nanti, sekarang untuk uji coba dlu aja
        $endsAt = $hasUsedTrial ? now()->addMinutes(10) : $trialEndDate;

        $subscription = Subscription::create([
            'id' => \Str::uuid(),
            'id_company' => $company->id,
            'id_package_type' => $packageType->id,
            'seats' => $validated['seats'],
            'is_trial' => !$hasUsedTrial,
            'trial_ends_at' => $hasUsedTrial ? null : $trialEndDate,
            'starts_at' => now(),
            'ends_at' => $endsAt, //now()->day(28)->endOfDay() kalau ingin ganti ke tgl 28 tiap akhir bulan
            'status' => $hasUsedTrial ? 'active' : 'trial'
        ]);

        // Update data company
        $company->id_subscription = $subscription->id;
        if (!$hasUsedTrial) {
            $company->has_used_trial = true;
        }
        $company->save();

        $responseMessage = $hasUsedTrial ? 'Langganan dimulai.' : 'Trial dimulai (1 menit).';

        return BaseResponse::success([
            'subscription' => $subscription
        ], $responseMessage, 201);
    }

// UPGRADE DAN DOWNGRADE SUBS
    public function requestChange(Request $request)
    {   
        $user = Auth::user();
        if (!$user) {
            return BaseResponse::error(null, 'User not authenticated', 401);
        }
        
        $validated = $request->validate([
            "id_new_package_type" => "nullable|exists:tb_package_types,id",
            "new_seats" => "required|integer|min:1"
        ]);

        $company = $user->workplace;
        if (!$company) {
            \Log::warning("User tidak punya workplace", ['user_id' => $user->id]);
            return BaseResponse::error(null, 'User tidak memiliki workplace', 422);
        }
        
        if (!$user || $user->id !== $company->id_manager || !$user->isAdmin()) {
            return BaseResponse::error(null, 'Anda tidak memiliki akses', 403);
        }
        
        $subscription = $company->subscription()
            ->whereIn('status', ['active'])
            ->orderByDesc('starts_at')
            ->first();

        if (!$subscription) {
            \Log::warning("Tidak ada subscription aktif ditemukan", ['company_id' => $company->id]);
            return BaseResponse::error(null, 'Tidak ada langganan aktif', 403);
        }

        if ($subscription->status !== 'active') {
            return BaseResponse::error(null, 'Langganan tidak aktif', 403);
        }

        // Ambil karyawan dan seats untuk subs baru
        $totalEmployees = $company->employees()->count();
        $currentSeats = $subscription->seats;
        $currentPackageTypeId = $subscription->id_package_type;

        $newSeats = $validated['new_seats'];
        $newPackageTypeId = $validated['id_new_package_type'] ?? $currentPackageTypeId;
        $newPackageType = PackageType::findOrFail($newPackageTypeId);
        
        Log::info("ID Package Type", ['id' => $newPackageTypeId]);
        
        if ($newSeats > $newPackageType->max_seats) {
            return BaseResponse::error(null,"Jumlah seat tidak boleh melebihi batas paket (maksimal: {$newPackageType->max_seats})", 400);
        }
        
        if ($newSeats < $totalEmployees) {
            return BaseResponse::error(null, 'Jumlah seat harus ≥ jumlah karyawan', 400);
        }
        
        $changeType = 'upgrade';
        if ($newSeats < $currentSeats || ($newPackageTypeId && $newPackageTypeId !== $subscription->id_package_type)) {
            $changeType = 'downgrade';


            if ($newPackageTypeId && $newPackageTypeId !== $subscription->id_package_type) {
                
                if ($newSeats > $newPackageType->max_seats){
                    return BaseResponse::error(null,"Jumlah seat melebihi {$newPackageType->max_seats}", 400);
                }

                if ($newPackageType->max_seats < $totalEmployees) {
                    return BaseResponse::error(null, 'Max seats paket baru tidak mencukupi', 400);
                }
            }
        }

        // ATURAN DARI FREE KE PAID PLAN & ATURAN JIKA TIDAK ADA DAILY USAGE
        $hasUsage = DailyUsageRecord::where('id_subscription', $subscription->id)->exists();
        if($subscription->packageType->is_free && $newPackageType->price_per_seat > 0 || !$hasUsage){
            $subscription->update([
                'status' => 'expired',
                'ends_at' => now(),
            ]);

            $newSubscription = Subscription::create([
                'id_company' => $subscription->id_company,
                'id_package_type' => $newPackageType->id,
                'seats' => $newSeats,
                'starts_at' => now(),
                'ends_at' => now()->minutes(10),
                'status' => 'active'
            ]);

            $company->update(['id_subscription' => $newSubscription->id]);

            return BaseResponse::success([
                'subscription_id' => $newSubscription->id
            ]);
        }

        // Simpan pending change
        $subscription->pendingChange()->updateOrCreate(
            ['id_subscription' => $subscription->id],
            [
                'id_new_package_type' => $newPackageTypeId,
                'new_seats' => $newSeats,
                'change_type' => $changeType,
                'status' => 'pending'
            ]
        );

        // UPDATE STATUS
        $newStatus = match ($changeType) {
            'upgrade' => 'pending_upgrade',
            'downgrade' => 'pending_downgrade',
            default => 'canceled',
        };

        $subscription->update(['status' => $newStatus]);
        
        // CEK CANCELLED
        if ($newStatus === 'canceled') {
            $subscription->update(['is_canceled' => true]);
        }

        // Trigger command generate invoice
        Artisan::call('invoice:generate-on-event');


        return BaseResponse::success([
            'message' => $newStatus === 'canceled' ? 'Langganan dibatalkan' : 'Silakan bayar tagihan sebelum melanjutkan'
        ]);

    }

    protected function createNewSubscription(Subscription $oldSub, string $newPackageTypeId, int $newSeats)
    {
        $now = Carbon::now();
        $endsAt = now()->addMinutes(10);

        $newSubscription = Subscription::create([
            'id_company' => $oldSub->id_company,
            'id_package_type' => $newPackageTypeId,
            'seats' => $newSeats,
            'is_trial' => false,
            'trial_ends_at' => null,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'status' => 'active',
        ]);

        // Update company.id_subscription
        $company = Company::find($oldSub->id_company);
        if ($company) {
            $company->update(['id_subscription' => $newSubscription->id]);
        }

        return $newSubscription;
    }

// CANCEL SUBS
    public function cancelSubscription(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return BaseResponse::error(null, 'User not authenticated', 401);
        }

        $company = $user->workplace;
        if (!$company) {
            return BaseResponse::error(null, 'User tidak memiliki workplace', 422);
        }

        // Ambil subscription aktif
        $subscription = $company->subscription()
            ->whereIn('status', ['active'])
            ->orderByDesc('starts_at')
            ->first();

        if (!$subscription) {
            return BaseResponse::error(null, 'Tidak ada langganan ditemukan', 404);
        }

        if (!in_array($subscription->status, ['active', 'pending_upgrade', 'pending_downgrade'])) {
            \Log::info("Status subscription saat ini", [
                'subscription_id' => $subscription->id,
                'status' => $subscription->status
            ]);
            return BaseResponse::error(null, 'Hanya langganan aktif yang bisa dibatalkan', 400);
        }

        // Update status subscription → canceled
        $subscription->update([
            'status' => 'canceled',
            'is_canceled' => true,
        ]);

        // Trigger command generate invoice
        Artisan::call('invoice:generate-on-event');

        // Cari package type free plan
        $freePlan = PackageType::where('is_free', true)->first();
        if (!$freePlan) {
            return BaseResponse::success([
                'message' => 'Langganan berhasil dibatalkan, tapi free plan tidak ditemukan'
            ]);
        }
    
        // Cek apakah sudah ada free subscription
        $existingFreeSub = Subscription::where('id_company', $company->id)
            ->where('id_package_type', $freePlan->id)
            ->where('status', 'active')
            ->exists();
    
        if (!$existingFreeSub) {
            // Buat subscription free plan
            $newFreeSub = Subscription::create([
                'id_company' => $company->id,
                'id_package_type' => $freePlan->id,
                'seats' => $freePlan->max_seats,
    
                'is_trial' => false,
                'trial_ends_at' => null,
    
                'starts_at' => now(),
                'ends_at' => now()->addMinutes(5),
    
                'status' => 'active',
            ]);
    
            // Update company.id_subscription
            $company->update(['id_subscription' => $newFreeSub->id]);
    
            \Log::info("Free plan langganan dibuat", [
                'company_id' => $company->id,
                'subscription_id' => $newFreeSub->id
            ]);
        }

        return BaseResponse::success([
            'message' => 'Langganan berhasil dibatalkan.',
            'subscription_id' => $subscription->id,
        ]);
    }

// ENSURE FREE SUBSCRIPTION
    public function ensureFreeSubscription(Company $company)
    {
        // Cek apakah company sudah punya subscription aktif
        $activeSub = $company->subscription()
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if ($activeSub) {
            return;
        }

        // Ambil package type free plan
        $freePlan = PackageType::where('name', 'Free Plan')->first();

        if (!$freePlan) {
            \Log::error("Free Plan tidak ditemukan");
            return;
        }

        // Buat subscription free plan
        $newSub = Subscription::create([
            'id_company' => $company->id,
            'id_package_type' => $freePlan->id,
            'seats' => 5, // misalnya
            'is_trial' => false,
            'starts_at' => now(),
            'ends_at' => null, // unlimited
            'status' => 'active',
        ]);

        // Update company.id_subscription
        $company->update(['id_subscription' => $newSub->id]);
    }

// DAPAT PACKAGE TYPE KESELURUHAN
    public function getAllPackageTypes()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return BaseResponse::error(null, 'Akses ditolak', 403);
        }

        $packageTypes = PackageType::all();

        return BaseResponse::success([
            'data' => $packageTypes->map(function ($pkg) {
                return [
                    'id' => $pkg->id,
                    'name' => $pkg->name,
                    'price_per_seat' => $pkg->price_per_seat,
                    'max_seats' => $pkg->max_seats,
                    'is_free' => $pkg->price_per_seat == 0,
                    'description' => $pkg->description,
                ];
            })
        ]);
    }

// DAPAT SEMUA INVOICE SAAT INI
    public function getCompanyInvoices()
    {
        $user = Auth::user();
        if (!$user) {
            return BaseResponse::error(null, 'User not authenticated', 401);
        }

        $company = $user->workplace;
        if (!$company) {
            return BaseResponse::error(null, 'User tidak memiliki workplace', 422);
        }

        // Ambil semua invoice milik perusahaan ini
        $invoices = Invoice::where('id_company', $company->id)
            ->with(['subscription.packageType', 'payments'])
            ->orderByDesc('created_at')
            ->get();

        // Format data untuk FE
        $formatted = $invoices->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'total_amount' => $invoice->total_amount,
                'due_datetime' => $invoice->due_datetime,
                'status' => $invoice->status,
                'description' => $invoice->description,
                'xendit_invoice_id' => $invoice->xendit_invoice_id,
                'invoice_url' => $invoice->invoice_url,
                'package_type' => optional($invoice->subscription)->packageType->name ?? null,
                'seats' => optional($invoice->subscription)->seats ?? null,
                'paid_at' => optional($invoice->payment)->payment_datetime ?? null,
            ];
        });

        return BaseResponse::success([
            'data' => $formatted
        ]);
    }

    public function getInvoiceDetail(string $invoiceId)
    {
        $user = Auth::user();
        if (!$user) {
            return BaseResponse::error(null, 'User not authenticated', 401);
        }

        $company = $user->workplace;
        if (!$company) {
            return BaseResponse::error(null, 'User tidak memiliki workplace', 422);
        }

        // Ambil invoice dengan relasi subscription & package type
        $invoice = Invoice::where('id', $invoiceId)
            ->where('id_company', $company->id)
            ->with(['subscription.packageType', 'payments'])
            ->first();

        if (!$invoice) {
            return BaseResponse::error(null, 'Invoice tidak ditemukan atau tidak punya akses', 404);
        }

        // Format response
        $responseData = [
            'id' => $invoice->id,
            'total_amount' => $invoice->total_amount,
            'due_datetime' => $invoice->due_datetime,
            'status' => $invoice->status,
            'description' => $invoice->description,
            'invoice_url' => $invoice->invoice_url,
            'subscription'=> $invoice->subscription ? [
                'id' => $invoice->subscription->id,
                'seats' => $invoice->subscription->seats,
                'starts_at' => $invoice->subscription->starts_at,
                'ends_at' => $invoice->subscription->ends_at,
                'status' => $invoice->subscription->status,
                'package_type' => optional($invoice->subscription->packageType)->toArray(),
            ] : null,
            'payment'=> optional($invoice->payments)->toArray()
        ];

        return BaseResponse::success([
            'data' => $responseData
        ]);
    }

//PENGGUNAAN HARIAN TIAP" SUBSCRIPTION 
    public function getUsageBySubscription(Request $request, string $subscriptionId)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return BaseResponse::error(null, 'Akses ditolak', 403);
        }

        $company = $user->workplace;
        if (!$company) {
            return BaseResponse::error(null, 'User tidak memiliki workplace', 422);
        }

        // Validasi apakah subscription ini milik company
        $usageRecords = DailyUsageRecord::where('id_company', $company->id)
            ->where('id_subscription', $subscriptionId)
            ->orderBy('date', 'asc')
            ->get();

        if ($usageRecords->isEmpty()) {
            return BaseResponse::success([
                'data' => []
            ], 'Tidak ada data penggunaan harian');
        }

        // Format untuk FE
        $formatted = $usageRecords->map(function ($record) {
            return [
                'date' => $record->date,
                'daily_cost' => $record->daily_cost,
            ];
        });

        return BaseResponse::success([
            'data' => $formatted
        ]);
    }
}
