<?php

namespace App\Http\Requests;

class ClockInRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
        ];
    }
}
