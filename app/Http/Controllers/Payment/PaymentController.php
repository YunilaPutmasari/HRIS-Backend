<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Models\Payment\Payment;
use App\Models\Payment\Invoice;
use Illuminate\Support\Facades\validator;
use App\Http\Responses\BaseResponse;

class PaymentController extends Controller
{
    // get semua payment
    public function index()
    {
        $payment = Payment::with(['invoice'])
        ->whereHas('invoice', function ($query) {
            $query->where('id_user', auth()->id());
        })
        ->latest()
        ->paginate(10); //('invoice.user')
        return BaseResponse::success(
            data: $payment,
            message: 'Payments retrieved successfully',
            code: 200
        );
    }

    //Create
    public function store(PaymentStoreRequest $request)
    {
        $validated = $request->validated();
        $payment = Payment::create($validated);

        return BaseResponse::success(
            data: $payment,
            message: 'Payment berhasil dibuat',
            code: 201
        );
    }

    //Show
    public function show($id){

        $userId = auth()->id();

        try{
            $payment = Payment::where('id',$id)
            ->whereHas('invoice', function ($query) use ($userId) {
                $query->where('id_user', $userId);
            })
            ->firstOrFail();
            return BaseResponse::success(
                data: $payment,
                message: 'Payment berhasil didapatkan',
                code: 200
            );
        } catch (\Exception $e) {
            return BaseResponse::error(
                message: 'Payment tidak ditemukan',
                code: 404
            );
        }
    }

    //Update payment
    public function update(PaymentUpdateRequest $request, $id){
        $userId = auth()->id();
        
        $payment = Payment::where('id', $id)
        ->whereHas('invoice', function ($query) use ($userId){
            $query->where('id_user',$userId);
        })
        ->first();
        if (!$payment){
            return BaseResponse::error(
                message: 'Payment tidak ditemukan',
                code: 404
            );
        }

        $validated = $request->validated();
        $payment->update($validated);
        
        return BaseResponse::success(
            data: $payment,
            message: 'Payment berhasil diupdate',
            code: 200
        );
    }

    //Delete payment (soft delete)
    public function destroy($id){
        $userId = auth()->id();

        $payment = Payment::where('id',$id)
            ->whereHas('invoice', function ($query) use ($userId){
                $query->where('id_user', $userId);
            })
            ->first();

        if (!$payment) {
            return BaseResponse::error(
                message: 'Payment tidak ditemukan',
                code: 404
            );
        }

        $payment->delete();
        return BaseResponse::success(
            message: 'Payment berhasil dihapus',
            code: 200
        );
    }
}
