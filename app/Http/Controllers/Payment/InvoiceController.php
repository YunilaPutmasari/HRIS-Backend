<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Models\Payment\Invoice;
use Illuminate\Support\Facades\Validator;
use App\Http\Responses\BaseResponse;
use App\Enums\InvoiceStatus;
use Xendit\Xendit;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use Xendit\Invoice as XenditInvoice;

class InvoiceController extends Controller
{
    // Ambil semua invoice
    public function index(){
        $invoice = Invoice::with(['user', 'payments'])
        ->where('id_user', auth()->id())
        ->latest()
        ->get();
        return BaseResponse::success($invoice);
    }

    // Tambah invoice baru
    public function store(InvoiceStoreRequest $request){
        $validated = $request->validated();
        $invoice = Invoice::create(array_merge($validated, [
            'id_user'  => auth()->id(),
            'status' => InvoiceStatus::UNPAID,
        ]));

        // Debug logging for Xendit configuration
        \Log::info('Xendit Configuration Check', [
            'env_key_exists' => !empty(env('XENDIT_SECRET_KEY')),
            'config_key_exists' => !empty(config('services.xendit.secret')),
            'env_key_length' => strlen(env('XENDIT_SECRET_KEY') ?? ''),
            'config_key_length' => strlen(config('services.xendit.secret') ?? '')
        ]);

        // Get Xendit API key from config instead of env directly
        $xenditApiKey = config('services.xendit.secret');
        if (!$xenditApiKey) {
            \Log::error('Xendit API key not configured', [
                'env_value' => env('XENDIT_SECRET_KEY'),
                'config_value' => config('services.xendit.secret')
            ]);
            return BaseResponse::error(
                message: 'Payment gateway configuration error',
                code: 500
            );
        }

        // Set API key with debug logging
        try {
            Xendit::setApiKey($xenditApiKey);
            \Log::info('Xendit API key set successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to set Xendit API key', [
                'error' => $e->getMessage()
            ]);
            return BaseResponse::error(
                message: 'Failed to initialize payment gateway',
                code: 500
            );
        }

        try {
            $xenditInvoice = \Xendit\Invoice::create([
                'external_id' => $invoice->id,
                'payer_email' => $request->email ?? 'dummy@example.com',
                'description' => 'Pembayaran Invoice #' . $invoice->id,
                'amount' => $invoice->total_amount,
                'status' => 'unpaid'
            ]);

            // Enhanced logging
            \Log::info('Xendit invoice creation response', [
                'invoice_id' => $invoice->id,
                'xendit_response' => $xenditInvoice,
                'has_invoice_url' => isset($xenditInvoice['invoice_url']),
                'has_xendit_id' => isset($xenditInvoice['id'])
            ]);

            if (!isset($xenditInvoice['id']) || !isset($xenditInvoice['invoice_url'])) {
                throw new \Exception('Xendit response missing required fields');
            }

            $invoice->update([
                'xendit_invoice_id' => $xenditInvoice['id'],
                'invoice_url' => $xenditInvoice['invoice_url'],
            ]);

            return BaseResponse::success(
                data: $invoice,
                message: 'Invoice berhasil dibuat dengan Xendit',
                code: 201
            );

        } catch (\Exception $e) {
            \Log::error('Gagal membuat invoice Xendit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'invoice_id' => $invoice->id
            ]);
            
            // Delete the created invoice since Xendit creation failed
            $invoice->delete();
            
            return BaseResponse::error(
                message: 'Gagal membuat invoice di Xendit: ' . $e->getMessage(),
                code: 500
            );
        }
    }

//Show invoice
    public function show($id){
        try{
            $userId = auth()->id();

            $invoice = Invoice::with([
                'subscription',  // Use direct relationship
                'payments'
            ])
            ->where('id', $id)
            ->where('id_user', $userId)
            ->first();

            if (!$invoice) {
                return BaseResponse::error(
                    message: 'Invoice tidak ditemukan atau tidak berhak mengakses',
                    code: 404
                );
            }
            
            $displayStatus = $invoice->status;
            if ($invoice->status === InvoiceStatus::UNPAID && $invoice->due_datetime < now()) {
                $displayStatus = 'overdue';
            }

            return BaseResponse::success(
                data: [
                    'data' => $invoice,
                    'display_status' => $displayStatus,
                ],
                message: 'Invoice berhasil ditemukan',
                code: 200
            );

        } catch (\Exception $e) {
            return BaseResponse::error(
                message: 'Invoice tidak ditemukan',
                code: 404
            );
        }
    }

//Update invoice
    public function update(InvoiceUpdateRequest $request, $id){
        $userId = auth()->id();
        $invoice = Invoice::where('id', $id)
            ->where('id_user', $userId)
            ->first();
            
        if (!$invoice){
            return BaseResponse::error(
                data: $invoice,
                message: 'Invoice tidak ditemukan',
                code: 404
            );
        }
        $validated = $request->validated();
        $invoice->update($validated);
        
        return BaseResponse::success(
            data:$invoice,
            message: 'Invoice diupdate',
            code: 200
        );
    }

//Delete invoice (soft delete)
    public function destroy($id)
    {
        $userId = auth()->id();

        $invoice = Invoice::where('id', $id)
            ->where('id_user', $userId)
            ->first();

        if (!$invoice) {
            return BaseResponse::error(
                message: 'Invoice tidak ditemukan atau tidak berhak mengakses',
                code: 404
            );
        }

        $invoice->delete(); // Soft delete

        return BaseResponse::success(
            data: $invoice,
            message: 'Invoice berhasil dihapus',
            code: 200
        );
    }
}
