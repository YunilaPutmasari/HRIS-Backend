<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment\Invoice;
use App\Models\Subscription\Subscription;
use App\Models\Org\User;
use App\Models\Org\Company;
use Carbon\Carbon;
use Xendit\Xendit;
// use Xendit\Invoice as XenditInvoice;
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
            if ($oldSubscription->ends_at && $oldSubscription->ends_at->isPast()) {
                $this->info("Subscription {$oldSubscription->id} was cancelled, skipping renewal.");
                continue;
            }

            // Validasi semua invoice dari subscription lama sudah dibayar
            $activeSubscriptionExists = Subscription::where('id_company', $oldSubscription->id_company)
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->first();

            if ($activeSubscriptionExists) {
                $this->warn("Perusahaan {$oldSubscription->id_company} masih memiliki subscription aktif.");
                continue;
            }
            
            $unpaidExists = Invoice::where('id_subscription', $oldSubscription->id)
                ->where('status', '!=', 'paid')
                ->exists();

            if ($unpaidExists) {
                continue; // Lewati jika belum lunas
            }

            // 🔁 Buat subscription baru
            $newSubscription = Subscription::create([
                'id_company' => $oldSubscription->id_company,
                'package_type' => $oldSubscription->package_type,
                'seats' => $oldSubscription->seats,
                'price_per_seat' => $oldSubscription->price_per_seat,

                'is_trial' => false,
                'trial_ends_at' => null,

                'starts_at' => now(),
                'ends_at' => now()->day(28)->addMonthNoOverflow()->endOfDay(),

                'status' => 'active',
            ]);

            // 📄 Buat invoice baru
            $company = Company::find($newSubscription->id_company);
            $manager = $company->manager;
            if (!$manager) {
                $this->warn("Perusahaan {$company->id} tidak punya manager");
                continue;
            }

            $dueDate = now()->day(28)->addMonthNoOverflow()->endOfDay();

            $invoice = Invoice::create([
                'id_user' => $manager->id,
                'id_subscription' => $newSubscription->id,
                'total_amount' => $newSubscription->seats * $newSubscription->price_per_seat,
                'due_datetime' => $dueDate,
                'status' => InvoiceStatus::UNPAID,
            ]);

            Xendit::setApiKey(env('XENDIT_SECRET_KEY'));

            try {
                // 🚀 Generate Xendit Invoice
                $xenditInvoice = \Xendit\Invoice::create([
                    'external_id' => $invoice->id,
                    'payer_email' => $manager->email ?? 'dummy@example.com',
                    'description' => "Langganan {$newSubscription->package_type} - Bulan " . $dueDate->format('F Y'),
                    'amount' => $invoice->total_amount,
                    'status' => 'unpaid'
                ]);

                // Debug log
                \Log::info('Xendit response', $xenditInvoice);

                $invoice->update([
                    'status' => InvoiceStatus::UNPAID,
                    'xendit_invoice_id' => $xenditInvoice['id'] ?? null,
                    'invoice_url' => $xenditInvoice['invoice_url'] ?? null,
                ]);

                $company->update(['id_subscription' => $newSubscription->id]);

                $this->info("Langganan & faktur diperbarui untuk perusahaan ID: {$company->id}");

            } catch (\Exception $e) {
                \Log::error('Gagal buat invoice di Xendit', [
                    'subscription_id' => $newSubscription->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->warn("Gagal membuat invoice di Xendit untuk subscription: {$newSubscription->id}");
            }
        }

        return Command::SUCCESS;
    }
}