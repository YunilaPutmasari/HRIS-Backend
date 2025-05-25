<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_code' => $this->payment_code,
            'amount_paid' => $this->amount_paid,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'payment_datetime' => $this->payment_datetime,
        ];
    }
}
