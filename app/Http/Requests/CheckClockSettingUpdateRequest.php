<?php

namespace App\Http\Requests;

class CheckClockSettingUpdateRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:WFA,WFO,Hybrid',
        ];
    }
}
