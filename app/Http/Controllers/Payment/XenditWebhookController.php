<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Invoice;
use App\Models\Payment\Payment;
use App\Models\Subscription\Subscription;
use App\Models\Org\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Enums\InvoiceStatus;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info('Xendit Webhook Payload', $data);

        $xenditId = $data['id'] ?? null;
        $statusFromXendit = $data['status'] ?? null;

        if (!$xenditId || !$statusFromXendit) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $invoice = Invoice::where('xendit_invoice_id', $xenditId)->first();
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        // Gunakan enum untuk normalisasi status
        $normalizedStatus = InvoiceStatus::fromXendit($statusFromXendit);

        $invoice->status = $normalizedStatus;
        $invoice->save();

        if ($normalizedStatus === InvoiceStatus::PAID) {
            Payment::create([
                'id' => Str::uuid(),
                'id_invoice' => $invoice->id,
                'payment_code' => $data['external_id'] ?? null,
                'amount_paid' => $data['paid_amount'] ?? $invoice->total_amount,
                'currency' => $data['currency'] ?? 'IDR',
                'payment_method' => $data['payment_channel'],
                'status' => 'success',
                'payment_datetime' => now(),
            ]);

            $this->applySubscriptionChanges($invoice);
        }

        return response()->json(['message' => 'Webhook handled'], 200);
    }

    protected function applySubscriptionChanges(Invoice $invoice)
    {
        $subscription = $invoice->subscription;

        if (!$subscription) {
            \Log::warning("Tidak ada subscription untuk invoice ID: {$invoice->id}");
            return;
        }

        // Cek apakah ada pending change
        $pendingChange = $subscription->pendingChange;

        if ($pendingChange && $pendingChange->status === 'pending') {
            $subscription->update([
                'status' => 'expired',
                'ends_at' => now(),
            ]);
            $newSubscription = Subscription::create([
                'id_company' => $subscription->id_company,
                'id_package_type' => $pendingChange->id_new_package_type,
                'seats' => $pendingChange->new_seats,
                'starts_at' => now(),
                'ends_at' => now()->day(28)->addMonthNoOverflow()->endOfDay(),
                'status' => 'active',
            ]);
            $company = $subscription->company;
            $company->update(['id_subscription' => $newSubscription->id]);

            // Hapus pending change
            $pendingChange->delete();

            \Log::info("Langganan baru dibuat", [
                'old_sub' => $subscription->id,
                'new_sub' => $newSubscription->id
            ]);

        }
        
        // Jika subscription status == canceled
        if ($subscription->status === 'canceled') {
            $subscription->update([
                'status' => 'expired',
                'ends_at' => now(),
            ]);

            \Log::info("Langganan dibatalkan", ['subscription_id' => $subscription->id]);
        }
    }
}

