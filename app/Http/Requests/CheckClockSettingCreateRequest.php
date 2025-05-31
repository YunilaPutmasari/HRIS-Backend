<?php

namespace App\Http\Requests;

class CheckClockSettingCreateRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'id_company' => 'required|uuid',
            'type' => 'required|in:WFA,WFO,Hybrid',
        ];
    }
}
