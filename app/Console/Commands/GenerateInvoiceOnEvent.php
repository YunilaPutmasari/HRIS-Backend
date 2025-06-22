<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\Subscription;
use App\Models\Subscription\DailyUsageRecord;
use App\Models\Payment\Invoice;
use App\Models\Org\User;
use App\Models\Org\Company;
use Carbon\Carbon;
use Xendit\Xendit;

class GenerateInvoiceOnEvent extends Command
{
    protected $signature = 'invoice:generate-on-event';
    protected $description = 'Buat invoice jika ada event: cancel, upgrade, downgrade';

    public function handle()
    {
        $now = Carbon::now();

        // Cari subscription dengan status tertentu
        $subscriptions = Subscription::whereIn('status', ['pending_upgrade', 'pending_downgrade', 'canceled', 'expired'])
            ->get();

        foreach ($subscriptions as $subscription) {
            // Lewati jika ini free plan
            if ($subscription->packageType->price_per_seat == 0 || $subscription->packageType->is_free) {
                $this->info("Paket yang digunakan free, dilewati");
                continue;
            }
    
            $companyId = $subscription->id_company;

            // Cek apakah subscription ini masih yang terbaru
            $latestSubId = Company::where('id', $companyId)->value('id_subscription');
        
            if ($latestSubId !== $subscription->id) {
                $this->info("Bukan subscription terbaru, dilewati");
                continue;
            }

            $usageRecords = DailyUsageRecord::where('id_subscription', $subscription->id)
                ->get();

            $totalAmount = round($usageRecords->sum('daily_cost'), 0);

            if ($totalAmount <= 0) continue;

            // Deskripsi sesuai status
            switch ($subscription->status) {
                case 'canceled':
                    $description = 'Tagihan pembatalan langganan';
                    break;
                case 'pending_upgrade':
                    $description = 'Tagihan upgrade langganan';
                    break;
                case 'pending_downgrade':
                    $description = 'Tagihan downgrade langganan';
                    break;
                case 'expired':
                    $description = 'Tagihan akhir masa langganan';
                    break;
                default:
                    $description = "Tagihan custom: {$subscription->status}";
                    break;
            }

            // Buat invoice
            $invoice = Invoice::create([
                'id_company' => $subscription->id_company,
                'id_subscription' => $subscription->id,
                'total_amount' => $totalAmount,
                'due_datetime' => now()->addMinutes(10),
                'status' => 'unpaid',
                'description' => $description,
            ]);

            try {
                // Validasi API Key
                $xenditApiKey = env('XENDIT_SECRET_KEY');
                if (!$xenditApiKey) {
                    throw new \Exception("Xendit API Key tidak tersedia");
                }
            
                Xendit::setApiKey($xenditApiKey);
            
                // Ambil manager
                $manager = User::find($subscription->company->id_manager);
                $email = $manager?->email ?? "anonim_{$subscription->id_company}@example.com";
            
                // Bulatkan biaya ke 2 desimal
                $totalAmount = round($usageRecords->sum('daily_cost'), 2);
            
                // Debug log
                \Log::info("Membuat invoice di Xendit", [
                    'external_id' => $invoice->id,
                    'payer_email' => $email,
                    'amount' => $totalAmount,
                    'description' => $description,
                ]);
            
                // Buat invoice di Xendit
                $xenditInvoice = \Xendit\Invoice::create([
                    'external_id' => $invoice->id,
                    'payer_email' => $email,
                    'description' => $description,
                    'amount' => $totalAmount,

                    // 'success_redirect_url' => "http://localhost:3000/subscription/payment/invoice/{$invoice->id}",
                    // 'failure_redirect_url' => "http://localhost:3000/subscription/payment/invoice/{$invoice->id}?status=failed",
                ]);
            
                // Update invoice lokal
                $invoice->update([
                    'xendit_invoice_id' => $xenditInvoice['id'] ?? null,
                    'invoice_url' => $xenditInvoice['invoice_url'] ?? null,
                ]);
            
                $this->info("Invoice berhasil dibuat untuk subscription ID: {$subscription->id}");
            
            } catch (\Exception $e) {
                \Log::error("Gagal membuat invoice di Xendit", [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'payload' => [
                        'external_id' => $invoice->id,
                        'payer_email' => $email,
                        'amount' => $totalAmount,
                    ]
                ]);
                $this->warn("Gagal membuat invoice di Xendit untuk subscription: {$subscription->id}");
            }

        }

        return Command::SUCCESS;
    }
}