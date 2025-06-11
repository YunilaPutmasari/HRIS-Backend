<?php

namespace App\Http\Requests;

class CheckClockSettingCreateRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // NOTE: Due to lack of context (likely because it hasn't been implemented yet) in frontend user auth, hence id_company become unrelevant (no source to take).
            // 'id_company' => 'required|uuid',
            'type' => 'required|in:WFA,WFO,Hybrid',
        ];
    }
}
