<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Invoice;
use App\Models\Payment\Payment;
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
        }

        return response()->json(['message' => 'Webhook handled'], 200);
    }
}

