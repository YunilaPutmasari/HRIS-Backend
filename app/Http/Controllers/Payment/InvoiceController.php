<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Models\Payment\Invoice;
use Illuminate\Support\Facades\Validator;
use App\Http\Responses\BaseResponse;
use Xendit\Xendit;
// use Xendit\Invoice as XenditInvoice;

class InvoiceController extends Controller
{
    // Ambil semua invoice
    public function index(){
        $invoice = Invoice::with(['user', 'payments'])->get();
        return BaseResponse::success($invoice);
    }

    // Tambah invoice baru
    public function store(InvoiceStoreRequest $request){
        $validated = $request->validated();
        $invoice = Invoice::create($validated);

        try {
            $xenditInvoice = \Xendit\Invoice::create([
                'external_id' => $invoice->id,
                'payer_email' => $request->email ?? 'dummy@example.com',
                'description' => 'Pembayaran Invoice #' . $invoice->id,
                'amount' => $invoice->total_amount,
            ]);

            // Debug log (sementara untuk memastikan)
            \Log::info('Xendit response', $xenditInvoice);

            $invoice->update([
                'status' => 'unpaid',
                'xendit_invoice_id' => $xenditInvoice['id'] ?? null,
                'invoice_url' => $xenditInvoice['invoice_url'] ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal membuat invoice Xendit', ['error' => $e->getMessage()]);
            return BaseResponse::error(
                message: 'Gagal membuat invoice di Xendit: ' . $e->getMessage(),
                code: 500
            );
        }

        return BaseResponse::success(
            data: $invoice,
            message: 'Invoice berhasil dibuat dengan Xendit',
            code: 201
        );
    }

//Show invoice
    public function show($id){
        try{
            $invoice = Invoice::with(['user', 'payment'])->findOrFail($id);
            return BaseResponse::success(
                data: $invoice,
                message: 'Invoice berhasil didapatkan',
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
        $invoice = Invoice::find($id);
        if (!$invoice){
            return BaseResponse::error(
                data: $invoice,
                message: 'Invoice tidak ditemukan',
                code: 404
            );
        }
        $validated = $request->validated();
        $invoice->update($request->$validated());
        
        return BaseResponse::success(
            data:$invoice,
            message: 'Invoice diupdate',
            code: 200
        );
    }

//Delete invoice (soft delete)
    public function destroy($id){
        $invoice = Invoice::find($id);
        if (!$invoice){
            return BaseResponse::error(
                data: $invoice,
                message: 'Invoice tidak ditemukan',
                code: 404
            );
        }

        $invoice->delete();
        return BaseResponse::success(
            data:$invoice,
            message: 'Invoice berhasil dihapus',
            code: 200
        );
    }
}
