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
    // private function createNewSubscriptionAfterPayment(Invoice $invoice)
    // {
    //     $oldSubscription = $invoice->subscription;

    //     $currentSubscription = Subscription::where('id_company', $oldSubscription->id_company)
    //         ->where('id', '!=', $oldSubscription->id) // Exclude the old subscription
    //         ->orderByDesc('created_at')
    //         ->first();
    //     if ($currentSubscription && $currentSubscription->isActive()) {
    //         return;
    //     }


    //     $isExpiredOrExpiring = $oldSubscription->status === 'expired' || 
    //         ($oldSubscription->isActive() && $oldSubscription->ends_at->diffInDays(now()) <= 7);

    //     if (!$isExpiredOrExpiring) {
    //         return;
    //     }

    //     $unpaidInvoicesExist = Invoice::where('id_company', $oldSubscription->id_company)
    //         ->where('id_subscription', $oldSubscription->id)
    //         ->where('id', '!=', $invoice->id)
    //         ->where('status', '!=', InvoiceStatus::PAID)
    //         ->exists();

    //     if ($unpaidInvoicesExist) {
    //         return;
    //     }

    //     $newSubscription = Subscription::create([
    //         'id_company' => $oldSubscription->id_company,
    //         'package_type' => $oldSubscription->package_type,
    //         'seats' => $oldSubscription->seats,
    //         'price_per_seat' => $oldSubscription->price_per_seat,
    //         'is_trial' => false,
    //         'trial_ends_at' => null,
    //         'starts_at' => now(),
    //         'ends_at' => now()->day(28)->addMonthNoOverflow()->endOfDay(),
    //         'status' => 'active',
    //     ]);

    //     $oldSubscription->company->update(['id_subscription' => $newSubscription->id]);
    //     $invoice->update(['id_subscription' => $newSubscription->id]);
    // }
    
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

            // $this->createNewSubscriptionAfterPayment($invoice);
        }

        return response()->json(['message' => 'Webhook handled'], 200);
    }
}

