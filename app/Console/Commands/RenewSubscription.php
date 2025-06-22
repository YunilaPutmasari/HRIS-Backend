<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment\Invoice;
use App\Models\Subscription\Subscription;
use App\Models\Org\User;
use App\Models\Org\Company;
use Carbon\Carbon;
use Xendit\Xendit;
use App\Enums\InvoiceStatus;

class RenewSubscription extends Command
{
    protected $signature = 'subscription:renew';
    protected $description = 'Buat subscription baru jika langganan habis dan semua tagihan sudah lunas';

    public function __construct()
    {
        parent::__construct();
        
    }

    public function handle()
    {
        $now = Carbon::now();

        // Ambil semua subscription expired
        $subscriptions = Subscription::where('status', 'expired')
            ->whereNull('deleted_at')
            ->get();

        foreach ($subscriptions as $oldSubscription) {
            // Skip if subscription was cancelled
            if ($oldSubscription->is_cancelled) {
                $this->info("Subscription {$oldSubscription->id} was cancelled, skipping renewal.");
                continue;
            }

            // Cek apakah ini adalah subscription terbaru perusahaan
            $latestSubId = Subscription::where('id_company', $oldSubscription->id_company)
                ->orderByDesc('starts_at')
                ->value('id');

            if ($latestSubId !== $oldSubscription->id) {
                $this->warn("Subscription ID {$oldSubscription->id} bukan langganan terbaru → dilewati");
                continue;
            }

            $activeExists = Subscription::where('id_company', $oldSubscription->id_company)
                ->where('status','active')
                ->exists();

            if ($activeExists) {
                $this->warn("Perusahaan {$oldSubscription->id_company} masih punya subscription aktif");
                continue;
            }
            
            $unpaidExists = Invoice::where('id_subscription', $oldSubscription->id)
                ->where('status', '!=', InvoiceStatus::PAID)
                ->exists();

            if ($unpaidExists) {
                $this->warn("Ada tagihan belum lunas untuk subscription ID: {$oldSubscription->id}");
                continue;
            }

            $packageType = $oldSubscription->packageType;
            if (!$packageType) {
                $this->error("Tidak ada package type untuk subscription ID: {$oldSubscription->id}");
                continue;
            }

            $company = Company::find($oldSubscription->id_company);
            if (!$company) {
                $this->error("Tidak dapat menemukan perusahaan", ['company_id' => $oldSubscription->id_company]);
                continue;
            }

            // 🔁 Buat subscription baru
            $newSubscription = Subscription::create([
                'id_company' => $oldSubscription->id_company,
                'id_package_type' => $packageType->id,
                'seats' => $oldSubscription->seats,

                'is_trial' => false,
                'trial_ends_at' => null,

                'starts_at' => now(),
                'ends_at' => now()->addMinutes(10),

                'status' => 'active',
            ]);

            $company->update(['id_subscription' => $newSubscription->id]);

            $this->info("Langganan diperbarui untuk perusahaan ID: {$company->id}");
        }

        return Command::SUCCESS;
    }
}