<?php

namespace App\Observers;

use App\Models\Subscription\Subscription;
use App\Models\Payment\Invoice;
use Illuminate\Support\Facades\Auth;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "created" event.
     */
    public function created(Subscription $subscription): void
    {
        $user = Auth::user();
        if(!$user) return;

        $totalAmount = $subscription->seats * $subscription->price_per_seat;

        $invoice = Invoice::create([
            'id_user' => $user->id,
            'id_subscription' => $subscription->id,
            'total_amount' => $totalAmount,
            'due_datetime' => now()->addDays(28),
            'status' => \App\Enums\InvoiceStatus::UNPAID,
        ]);

        try {
            $xenditInvoice = \Xendit\Invoice::create([
                'external_id' => $invoice->id,
                'payer_email' => $user->email ?? 'anonim@example.com',
                'description' => "Pembayaran Subscription - {$subscription->package_type} ({$subscription->seats} seat)",
                'amount' => $invoice->total_amount,
            ]);

            $invoice->update([
                'xendit_invoice_id' => $xenditInvoice['id'] ?? null,
                'invoice_url' => $xenditInvoice['invoice_url'] ?? null,
            ]);

        } catch (\Exception $e) {
            \Log::error('Gagal membuat invoice di Xendit: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Subscription "updated" event.
     */
    public function updated(Subscription $subscription): void
    {
        //
    }

    /**
     * Handle the Subscription "deleted" event.
     */
    public function deleted(Subscription $subscription): void
    {
        //
    }

    /**
     * Handle the Subscription "restored" event.
     */
    public function restored(Subscription $subscription): void
    {
        //
    }

    /**
     * Handle the Subscription "force deleted" event.
     */
    public function forceDeleted(Subscription $subscription): void
    {
        //
    }
}
